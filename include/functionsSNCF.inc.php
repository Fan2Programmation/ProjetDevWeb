<?php
    declare(strict_types=1);
    define("NAVITIA_TOKEN", "e4732adc-eefe-4b2c-b528-acdc6bd2f1c5");
    define("NAVITIA_URL", "https://".NAVITIA_TOKEN."@api.navitia.io/v1/");

    /**
     * Fonction créant le svg du logo de la ligne de transport
     * @param label le nom de la ligne de transport
     * @param color la couleur de la ligne de transport
     * @param textColor la couleur du texte
     * @return svg le code svg du logo englobé dans un span de classe "logo"
     */
    function creerLogoSvg(string $label, string $color, string $textColor): string {
        $fontSize = 15; // Taille de police par défaut
        $minimumFontSize = 5;
        $maxLength = 3; // Nombre maximal de caractères sans ajustement de la taille
    
        // Ajuster la taille de la police en fonction de la longueur du label
        if (strlen($label) > $maxLength) {
            $fontSize = max($fontSize - (strlen($label) - $maxLength), $minimumFontSize); // Réduire la taille de la police pour les longs textes
        }
    
        $svg = "<svg width=\"40\" height=\"40\" xmlns=\"http://www.w3.org/2000/svg\" style=\"vertical-align: middle;\"><circle cx=\"20\" cy=\"20\" r=\"18\" fill=\"#{$color}\" /><text x=\"50%\" y=\"50%\" dominant-baseline=\"middle\" text-anchor=\"middle\" fill=\"#{$textColor}\" font-size=\"{$fontSize}px\" font-family=\"Arial\" dy=\".3em\">{$label}</text></svg>";
        return $svg;
    }    
    
    /**
     * Fonction permettant d'afficher les prochains départ en gare
     * @param id l'identifiant de la gare
     * @return res la liste non ordonnée de tous les prochains départs
     */
    function afficherProchainsDeparts(string $id):string {
        $url = NAVITIA_URL."coverage/fr-idf/stop_areas/".$id."/departures";

        $fluxjson = file_get_contents($url);
        $res = "<ul>\n";

        if($fluxjson !== false) {
            $donnees = json_decode($fluxjson, true);
            // On parcourt chaque prochain départ en gare (il y en a 10 à la fois dans le flux JSON)
            foreach($donnees['departures'] as $departure) {
                $heureDeDepart = explode(" ", decodeTemps($departure['stop_date_time']['departure_date_time']))[1];
                $res .= "\t\t\t\t\t\t<li>".creerLogoSvg($departure['display_informations']['label'], $departure['display_informations']['color'],$departure['display_informations']['text_color'])." Prochain départ à destination de : ".$departure['display_informations']['direction']." à ".$heureDeDepart." (".$departure['display_informations']['physical_mode'].")</li>\n";
            }
        }

        $res .= "\t\t\t\t\t</ul>\n";
        $res .= "\t\t\t\t\t<a href=\"index.php\" style=\"display:inline-block;margin-top:20px;padding:10px;background-color:#007bff;color:white;text-decoration:none;border-radius:5px;\">Retour</a>\n";
        return $res;
    }

    /**
     * Fonction décodant l'affichage du temps fourni par l'API
     * @param temps la chaine de caractère non décodée fournie par l'API
     * @return decodeTemps la chaine de caractère davantage lisible pour un utilisateur lambda
     */
    function decodeTemps(string $temps): string {
        // Convertir le temps en objet DateTime
        $datetime = DateTime::createFromFormat('Ymd\THis', $temps);
        
        // Vérifie si la conversion a réussi
        if ($datetime instanceof DateTime) {
            // Formate la date et l'heure
            return $datetime->format('Y-m-d H:i:s');
        } else {
            // En cas d'échec de la conversion, retourne une chaîne vide
            return '';
        }
    }

    /**
     * Fonction permettant l'affichage d'une liste de gares ayant le motif de la recherche dans le nom
     * @param recherche chaine de caractère permettant la recherche d'une gare donnée
     * @param var le nom de la variable qui contiendra l'identifiant de la gare sur la page de traitement
     * @return res la liste non triée des gares similaires à la recherche
     */
    function listeGaresSimilaires(string $recherche, string $var):string {
        // On récupère le flux JSON correspondant aux informations relatives à notre recherche
        $url = NAVITIA_URL."coverage/fr-idf/places?q=".urlencode($recherche);                   
        $fluxjson = file_get_contents($url);

        $res = "<h3>Résultats de la recherche pour '$recherche'</h3>\n";
        $res .= "\t\t\t<ul>\n";

        // Si le flux n'est pas vide
        if ($fluxjson !== false) {
            // On décode les données dans un tableau associatif
            $donnees = json_decode($fluxjson, true);
            // On parcourt chaque endroit reconnu
            if(isset($donnees['places'])) {
                foreach($donnees['places'] as $place) {
                    // Si cet endroit est un arrêt de transport
                    if(isset($place['stop_area'])) {
                        $res .= "\t\t\t\t<li> <a href=\"?".$var."=".$place['id']."\">".$place['name'];
                        $stop_area = $place['stop_area'];
                        // Si cet endroit propose des modes de transport
                        if(isset($stop_area['commercial_modes'])) {
                            $res .= " - (";
                            // On parcourt tous les modes de transports disponibles à cette gare
                            foreach($stop_area['commercial_modes'] as $commercial_mode){
                                $res .= " ".explode(" ",$commercial_mode['name'])[0]." ";
                            }
                            $res .= ")";
                        }
                        $res .= "</a></li>\n";
                    }
                }
            }
        }

        $res .= "\t\t\t</ul>\n";
        $res .= "\t\t\t<a href=\"index.php\" style=\"display:inline-block;margin-top:20px;padding:10px;background-color:#007bff;color:white;text-decoration:none;border-radius:5px;\">Retour</a>\n";
        return $res;
    }

    /**
     * Fonction permettant de récupérer le nom de la gare à partir de son identifiant
     * @param id l'identifiant de la gare
     * @return nom le nom de la gare
     */
    function nomDeLaGare(string $id):string {
        $url = NAVITIA_URL."coverage/fr-idf/stop_areas/".$id;
        $fluxjson = file_get_contents($url);

        if($fluxjson !== false) {
            $donnees = json_decode($fluxjson, true);
            if(isset($donnees['stop_areas'][0]['name'])) {
                return $donnees['stop_areas'][0]['name'];
            }
        }
        return "";
    }

    /**
     * Fonction permettant l'affichage d'un itinéraire entre deux gares à partir de leurs deux codes UIC
     * @param id l'identifiant de la gare de départ
     * @param id2 l'identifiant de la gare d'arrivée
     * @param type le type de recherche (départ ou arrivée)
     * @param date la date de départ/arrivée
     * @param heure l'heure de départ/arrivée
     * @return res la liste non triée des gares à traverser sur l'itinéraire
     */
    function afficherItineraire(string $id, string $id2, string $type, string $date, string $heure):string {
        // On va d'abord traiter le date et l'heure pour les transformer en objet DateTime
        $heure = urldecode($heure);
        $datetime = new DateTime($date . ' ' . $heure);
        $datetime = $datetime->format('Ymd\THis');

        // Ensuite on modifie la valeur de $type pour qu'elle corresponde à l'API
        if($type === "depart") {
            $type = "departure";
        } else if($type === "arrivee") {
            $type = "arrival";
        }

        // On récupère le flux JSON correspondant aux informations relatives à notre recherche
        $url = NAVITIA_URL."coverage/fr-idf/journeys?from=".$id."&to=".$id2."&datetime=".$datetime."&datetime_represents=".$type;
        $fluxjson = file_get_contents($url);

        $res = "\t\t\t<h3>Meilleur itinéraire trouvé :</h3>\n";

        // Si le flux n'est pas vide
        if ($fluxjson !== false) {
            // On décode les données dans un tableau associatif
            $donnees = json_decode($fluxjson, true);
            // On parcourt chaque itinéraire trouvé
            if(isset($donnees['journeys'])) {
                foreach ($donnees['journeys'] as $journey) {
                    // Si l'itinéraire est qualifié de meilleur itinéraire entre ces deux gares
                    if(isset($journey['type']) && $journey['type'] === "best") {
                        // On parcourt chaque section de l'itinéraire (chaque bouts séparés pas des changements)
                        if(isset($journey['sections'])) {
                            foreach($journey['sections'] as $section) {
                                // Pour les sections correspondantes à un transport en commun, afficher les arrêts intermédiaires
                                if ($section['type'] === "public_transport" && isset($section['stop_date_times'])) {

                                    $res .= "\t\t\t<section id=\"".$label = $section['display_informations']['label']."\">\n";

                                    // Récupération des temps de départ et d'arrivée pour la section
                                    $departure = DateTime::createFromFormat('Ymd\THis', $section['departure_date_time']);
                                    $arrival = DateTime::createFromFormat('Ymd\THis', $section['arrival_date_time']);
                                    $departureTime = $departure->format('H:i');
                                    $arrivalTime = $arrival->format('H:i');
                                    
                                    // On récupère la infos de la ligne de transport
                                    $label = $section['display_informations']['label'];
                                    $color = $section['display_informations']['color'];
                                    $textColor = $section['display_informations']['text_color'];

                                    $res .= "\t\t\t\t".creerLogoSvg($label, $color, $textColor)." <p><strong>Départ : </strong>".$departureTime."</p>\n";

                                    // Chaque section de transport en commun fera l'objet d'une liste d'arrêts
                                    $res .= "\t\t\t\t<ul style=\"border-left: 10px solid #{$color}; padding-left: 20px;\">\n";

                                    // On parcourt tous les arrêts de la section de transport en commun
                                    foreach ($section['stop_date_times'] as $stop) {
                                        // On ajoute le nom de l'arrêt à notre liste non triée
                                        $res .= "\t\t\t\t\t<li>".$stop['stop_point']['name']."</li>\n";
                                    }

                                    $res .= "\t\t\t\t</ul>\n"; // On referme la liste d'arrêts

                                    $res .= "\t\t\t\t<p><strong>Arrivée : </strong>".$arrivalTime."</p>\n";

                                    $res .= "\t\t\t</section>\n";
                                }
                                // Pour les sections correspondantes à un changement (qualifié par "waiting")
                                else if ($section['type'] === "waiting") {
                                    // On affiche le temps du changement
                                    $changeTime = $section['duration'];
                                    // On divise toutes ces secondes par 60 pour avoir les minutes
                                    $minutes = intdiv($changeTime, 60);
                                    // Le reste de la division sont les secondes restantes
                                    $seconds = $changeTime % 60;
                                    $formattedTime = $minutes . " min " . $seconds . " sec";
                                    $res .= "\t\t\t<p><strong>Temps du changement: </strong>" . $formattedTime . "</p>\n";
                                }
                                else if ($section['type'] === "street_network") {
                                    // On récupère le temps de marche
                                    $walkingTime = $section['duration'];
                                    // On divise toutes ces secondes par 60 pour avoir les minutes
                                    $minutes = intdiv($walkingTime, 60);
                                    // Le reste de la division sont les secondes restantes
                                    $seconds = $walkingTime % 60;
                                    $formattedTime = $minutes . " min " . $seconds . " sec";
                        
                                    // On affiche le temps de marche avec un logo de bonhomme qui marche
                                    $res .= "\t\t\t<p><img src='./images/walking.png' alt='Walking icon'/> Temps de marche: " . $formattedTime . "</p>\n";
                                }
                            }
                        }
                    }
                }
            }
        }

        $res .= "\t\t\t<a href=\"index.php\" style=\"display:inline-block;margin-top:20px;padding:10px;background-color:#007bff;color:white;text-decoration:none;border-radius:5px;\">Retour</a>\n";
        return $res;
    }

    /**
     * Fonction permettant de stocker la gare consultée dans un fichier CSV et dans un COOKIE
     * @param id l'identifiant de la gare consultée
     */
    function stockerGareConsultee($id):void {
        // Récupérer le nom de la gare à partir de son identifiant
        $nomGare = nomDeLaGare($id);

        // Récupérer la localisation de l'utilisateur pour des raisons de sécurité
        $coords = getUserCoords();
        $latitude = $coords["latitude"];
        $longitude = $coords["longitude"];

        // Nom du fichier CSV
        $fichier = 'gares_consultees.csv';
    
        // Ouvrir le fichier en mode append
        $handle = fopen($fichier, 'a');
    
        // Vérifier si le fichier a été ouvert avec succès
        if ($handle !== false) {
            // Données à écrire dans le fichier CSV
            $data = [$nomGare, date('Y-m-d H:i:s'), $latitude, $longitude];
    
            // Écrire les données dans le fichier CSV
            fputcsv($handle, $data);
    
            // Fermer le fichier
            fclose($handle);
        }

        // Stocker la gare consultée dans un cookie côté client
        $cookieValue = $nomGare . '|' . date('Y-m-d H:i:s');
        setcookie('derniereGareConsultee', $cookieValue, time() + (86400 * 30), "/"); // Le cookie expire après 30 jours
    }

    /**
     * Fonction permettant de récupérer la dernière gare consultée à partir d'un COOKIE
     * @return string la dernière gare consultée
     */
    function derniereGareConsultee():string {
        if (isset($_COOKIE['derniereGareConsultee'])) {
            $cookieValue = $_COOKIE['derniereGareConsultee'];
            $data = explode('|', $cookieValue);
            return $data[0]." le ".$data[1]."\n";
        }
        return "Aucune gare consultée récemment\n";
    }

    /**
     * Fonction permettant de générer un histogramme des gares consultées et de l'enregistrer en tant qu'image PNG
     * Nécessite la bibliothèque JpGraph @see http://jpgraph.net/
     */
    function genererHistogramme():void {
        require_once ('jpgraph-4.4.2/src/jpgraph.php');
        require_once ('jpgraph-4.4.2/src/jpgraph_bar.php');
    
        $fichier = 'gares_consultees.csv';
    
        // Lire le fichier CSV et compter les consultations par gare
        $gares = [];
        if (($handle = fopen($fichier, 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                $gare = $data[0];
                if (isset($gares[$gare])) {
                    $gares[$gare]++;
                } else {
                    $gares[$gare] = 1;
                }
            }
            fclose($handle);
        }
    
        // Créer le graphique
        $graph = new Graph(800, 600);
        $graph->SetScale('textlin');
    
        // Ajouter les barres
        $barPlot = new BarPlot(array_values($gares));
        $graph->Add($barPlot);
    
        // Définir les légendes pour l'axe x
        $graph->xaxis->SetTickLabels(array_keys($gares));
    
        // Faire pivoter les étiquettes de l'axe x de 45 degrés
        $graph->xaxis->SetLabelAngle(45);
    
        // Utiliser une police TTF pour les étiquettes de l'axe x
        $graph->xaxis->SetFont(FF_VERDANA, FS_NORMAL, 10);
    
        // Augmenter la marge inférieure du graphique
        $graph->img->SetMargin(40, 40, 40, 200); // Les marges sont définies dans l'ordre suivant : gauche, droite, haut, bas
    
        // Enregistrer le graphique en tant que PNG
        $graph->Stroke('./images/histogramme.png');
    }

    /**
     * Fonction permettant d'afficher une image aléatoire à partir d'un dossier spécifié
     */
    function randomImage() : void {
        // Chemin vers le dossier des photos
        $dir = 'photos';

        // Vérifie si le dossier existe et lire son contenu
        if (is_dir($dir)) {
            // Enlève les entrées '.' et '..'
            $files = array_diff(scandir($dir), array('.', '..')); 
            
            // Vérifier s'il y a des images
            if (count($files) > 0) {
                $randomImage = $files[array_rand($files)]; // Sélectionner une image au hasard
                $imagePath = $dir . '/' . $randomImage; // Chemin complet vers l'image

                // Afficher l'image avec figure et figcaption
                echo "<figure><img src='$imagePath' alt='Image aléatoire'/></figure>";
            } else {
                echo "<p>Aucune image trouvée dans le dossier.</p>";
            }
        } else {
            echo "<p>Le dossier spécifié n'existe pas.</p>";
        }
    }

    /**
     * Fonction retournant les gares proches d'une position géographique donnée
     * @param latitude la latitude de la position géographique
     * @param longitude la longitude de la position géographique
     * @return string la liste des gares proches
     */
    function gareProche(string $latitude, string $longitude):string{
        // Construction de l'URL pour les requêtes
        $url = NAVITIA_URL."/coverage/fr-idf/coords/$longitude;$latitude/places_nearby?distance=1000&type[]=stop_area";

        $fluxjson = file_get_contents($url);

        $res = "";

        if($fluxjson !== false) {
            $donnees = json_decode($fluxjson, true);
            if(isset($donnees['places_nearby'])) {
                foreach ($donnees['places_nearby'] as $place) {
                    if(isset($place['embedded_type'])) {
                        if ($place['embedded_type'] == 'stop_area') {
                            $distance = $place['distance'];
                            $res .= "\t\t\t\t<p>".$place['name']." - <strong>Distance : ".$distance." m</strong></p>\n";
                        }
                    }
                }
            }
        }

        return (($res == "") ? "<p>Aucune gare trouvée à proximité.</p>\n" : $res);
    }
?>