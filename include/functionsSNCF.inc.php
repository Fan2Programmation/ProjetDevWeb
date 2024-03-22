<?php
    declare(strict_types=1);
    define("TOKEN", "bb7b800f-8205-41c0-998c-09e0f55c2ed7");
    define("URL", "https://".TOKEN."@api.sncf.com/v1/");

    /**
     * Fonction pour trouver l'identifiant de la gare voulue
     * @param gare la gare dont on veut récupérer l'identifiant
     * @return id l'identifiant
     */
    function findID(string $gare):string {
        $url = URL."coverage/sncf/places?q=".urlencode($gare);
        $id = "";

        $fluxjson = file_get_contents($url);
        if ($fluxjson !== false) {
            $donnees = json_decode($fluxjson, true);
            $id .= $donnees['places']['0']['id'];
        }    

        return $id;
    }

    /**
     * Fonction permettant l'affichage d'une liste de gares ayant le motif de la recherche dans le nom
     * @param recherche chaine de caractère permettant la recherche d'une gare donnée
     */
    function listeGaresSimilaires(string $recherche):void {
        // On récupère le flux JSON correspondant aux informations relatives à notre recherche
        $url = URL."/coverage/sncf/places?q=" . urlencode($recherche);                   
        $fluxjson = file_get_contents($url);

        // Si le flux n'est pas vide
        if ($fluxjson !== false) {
            // On décode les données dans un tableau associatif
            $donnees = json_decode($fluxjson, true);
            // On crée l'array qui va récupérer tous les endroits reconnus avec la recherche
            $suggestions = array();
            // On parcourt chaque endroit reconnu
            foreach ($donnees['places'] as $place) {
                // l'identifiant uic est sous la forme XX:XXX:00000000, on récupère la partie numéraire 00000000
                $decoupe = explode(':', $place['id']);
                $partie_numeraire = $decoupe[2];
                // Si la partie numéraire de l'identifiant UIC de l'endroit sélectionné commence par 87, alors c'est une gare ferroviaire, on l'affiche dans les suggestions 
                if(strpos($partie_numeraire, "87") === 0){
                    $suggestions[] = $place['name'];
                }
            }
            echo "<h3>Résultats de la recherche pour '$recherche'</h3>";
            if (!empty($suggestions)) {
                echo "<ul>";
                foreach ($suggestions as $gare) {
                    echo "<li><a href='?gare=".urlencode($gare)."'>$gare</a></li>";
                }
                echo "</ul>";
            } else {
                echo "Aucune gare trouvée pour '$recherche'";
            }
        } else {
            echo "Erreur lors de la récupération des suggestions";
        }
    }
?>