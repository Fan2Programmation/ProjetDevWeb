<?php
    declare(strict_types=1);
    define("TOKEN", "bb7b800f-8205-41c0-998c-09e0f55c2ed7");

    /**
     * Fonction pour trouver le code UIC de la gare voulue
     * @param gare la gare dont on veut récupérer le code UIC
     * @return uic le code uic
     */
    function findUIC(string $gare):string {
        $url = "https://".TOKEN."@api.sncf.com/v1/coverage/sncf/places?q=".urlencode($gare);
        $uic = "";

        $fluxjson = file_get_contents($url);
        if ($fluxjson !== false) {
            $donnees = json_decode($fluxjson, true);
            $uic .= $donnees['places']['0']['id'];
        }    

        return $uic;
    }

    /**
     * Fonction permettant l'affichage d'une liste de gares ayant le motif de la recherche dans le nom
     * @param recherche chaine de caractère permettant la recherche d'une gare donnée
     */
    function listeGaresSimilaires(string $recherche):void {
        $url = "https://".TOKEN."@api.sncf.com/v1/coverage/sncf/places?q=" . urlencode($recherche);
                          
        $fluxjson = file_get_contents($url);
        if ($fluxjson !== false) {
            $donnees = json_decode($fluxjson, true);
            $suggestions = array();
            foreach ($donnees['places'] as $place) {
                $suggestions[] = $place['name'];
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