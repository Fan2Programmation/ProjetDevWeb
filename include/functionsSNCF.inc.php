<?php
    declare(strict_types=1);
    define("SNCF_TOKEN", "bb7b800f-8205-41c0-998c-09e0f55c2ed7");
    define("SNCF_URL", "https://".SNCF_TOKEN."@api.sncf.com/v1/");
    define("NAVITIA_TOKEN", "e4732adc-eefe-4b2c-b528-acdc6bd2f1c5");
    define("NAVITIA_URL", "https://".NAVITIA_TOKEN."@api.navitia.io/v1/");

    /**
     * Fonction créant le svg du logo de la ligne de transport
     * @param label le nom de la ligne de transport
     * @param color la couleur de la ligne de transport
     * @return svg le code svg du logo
     */
    function creerLogoSvg(string $label, string $color):string {
        $svg = "<svg width=\"24\" height=\"24\" xmlns=\"http://www.w3.org/2000/svg\"><circle cx=\"12\" cy=\"12\" r=\"12\" fill=\"#".$color."\" /><text x=\"50%\" y=\"55%\" text-anchor=\"middle\" fill=\"black\" font-size=\"10\" font-family=\"Arial\" dy=\".3em\">".$label."</text></svg>";
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
                $res .= "\t\t\t\t\t\t<li>Prochain départ à destination de : ".$departure['display_informations']['direction']." à ".$heureDeDepart." (".$departure['display_informations']['physical_mode'].")".creerLogoSvg($departure['display_informations']['label'], $departure['display_informations']['color'])."</li>\n";
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
     * Fonction permettant l'affichage d'un itinéraire entre deux gares à partir de leurs deux codes UIC
     * @param id l'identifiant de la gare de départ
     * @param id2 l'identifiant de la gare d'arrivée
     * @return res la liste non triée des gares à traverser sur l'itinéraire
     */
    function afficherItineraire(string $id, string $id2):string {
        // On récupère le flux JSON correspondant aux informations relatives à notre recherche
        $url = NAVITIA_URL."coverage/fr-idf/journeys?from=".$id."&to=".$id2;
        $fluxjson = file_get_contents($url);

        $res = "\t\t\t<h3>Meilleur itinéraire trouvé :</h3>\n";
        $res .= "\t\t\t<ul>\n";

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
                                    // On parcourt tous les arrêts de la section de transport en commun
                                    foreach ($section['stop_date_times'] as $stop) {
                                        // On ajoute le nom de l'arrêt à notre liste non triée
                                        $res .= "\t\t\t\t<li>".$stop['stop_point']['name']."</li>\n";
                                    }
                                }
                                // Pour les sections correspondantes à un changement (qualifié par "waiting")
                                else if ($section['type'] === "waiting") {
                                    // On notifie un changement dans notre liste en ajoutant un élement vide
                                    $res .= "\t\t\t\t<li></li>\n";
                                }
                            }
                        }
                    }
                }
            }
        }

        $res .= "\t\t\t</ul>\n";
        $res .= "\t\t\t<a href=\"index.php\" style=\"display:inline-block;margin-top:20px;padding:10px;background-color:#007bff;color:white;text-decoration:none;border-radius:5px;\">Retour</a>\n";
        return $res;
    }
?>