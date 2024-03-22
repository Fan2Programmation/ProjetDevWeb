<?php
    declare(strict_types=1);
    define("TOKEN", "bb7b800f-8205-41c0-998c-09e0f55c2ed7");
    define("URL", "https://".TOKEN."@api.sncf.com/v1/");

    /**
     * Fonction permettant d'afficher les prochains départ en gare
     * @param id l'identifiant de la gare
     * @return res la liste non ordonnée de tous les prochains départs
     */
    function afficherProchainsDeparts(string $id):string {
        $url = URL."coverage/sncf/stop_areas/".$id."/departures";
        $fluxjson = file_get_contents($url);
        $res = "<ul>\n";
        if($fluxjson !== false) {
            $donnees = json_decode($fluxjson, true);
            // On parcourt chaque prochain départ en gare (il y en a 10 à la fois dans le flux JSON)
            foreach($donnees['departures'] as $departure) {
                $res .= "\t\t\t\t\t\t<li>Prochain départ à destination de : ".$departure['display_informations']['direction']." à ".decodeTemps($departure['stop_date_time']['departure_date_time'])."</li>\n";
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
     * @return res la liste non triée des gares similaires à la recherche
     */
    function listeGaresSimilaires(string $recherche):string {
        // On récupère le flux JSON correspondant aux informations relatives à notre recherche
        $url = URL."/coverage/sncf/places?q=" . urlencode($recherche);                   
        $fluxjson = file_get_contents($url);

        $res = "<h3>Résultats de la recherche pour '$recherche'</h3>\n";
        $res .= "\t\t\t\t\t<ul>\n";

        // Si le flux n'est pas vide
        if ($fluxjson !== false) {
            // On décode les données dans un tableau associatif
            $donnees = json_decode($fluxjson, true);
            // On parcourt chaque endroit reconnu
            foreach ($donnees['places'] as $place) {
                // l'identifiant uic est sous la forme XX:XXX:00000000, on récupère la partie numéraire 00000000
                $decoupe = explode(':', $place['id']);
                $partie_numeraire = $decoupe[2];
                // Si la partie numéraire de l'identifiant UIC de l'endroit sélectionné commence par 87, alors c'est une gare ferroviaire, on l'affiche dans les suggestions
                // Le lien hypertexte permettra d'entrer l'identifiant en paramètre id sur la page
                if(strpos($partie_numeraire, "87") === 0){
                    $res .= "\t\t\t\t\t\t<li><a href=\"?id=".$place['id']."\">".$place['name']."</a></li>\n";
                }
            }
        }
        $res .= "\t\t\t\t\t</ul>\n";
        return $res;
    }
?>