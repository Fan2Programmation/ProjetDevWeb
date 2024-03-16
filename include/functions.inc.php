<?php
    declare(strict_types=1);

    function afficher_contenu(): string {
        // notre clé API donnée par la NASA
        $API_KEY = "aP0WZbXQAdVEsMHTU3d4ZlpnHQNBTz0jsgrBeKdl";
        // l'URL de l'API avec notre clé dedans
        $URL = "https://api.nasa.gov/planetary/apod?api_key=$API_KEY";
        // on récupère le flux JSON
        $FLUX_JSON = file_get_contents($URL);
        // on "json decode" le flux JSON récupéré, true signifie que l'on veut le résultat sous forme de tableau associatif
        $DATA = json_decode($FLUX_JSON, true);
        $RESULTAT = "";
        if ($DATA['media_type'] == 'image') {
            $RESULTAT = "<img src=\"" . $DATA['url'] . "\" alt=\"" . $DATA['title'] . "\">\n";
        } else if ($DATA['media_type'] == 'video') {
            $RESULTAT = "<video controls\">\n\t<source src=\"" . $DATA['url'] . "\" type=\"video/mp4\">\n\t" . $DATA['title'] . "\n</video>\n";
        }
        return $RESULTAT;
    }
    


    function position_geographiqueXML() {
        // Récupération de l'adressse ip de l'utilisateur
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        // l'URL de l'API avec l'adresse ip dedans
        $url = "http://www.geoplugin.net/xml.gp?ip=" . $ipAddress;
        // on récupère le flux XML
        $response = file_get_contents($url);
        if ($response === false) {
            return "Erreur lors de la récupération des données.";
        }
        // on analyse le flux XML récupéré, false signifie qu'il y a une erreur lors de l'analyse.
        $xml = simplexml_load_string($response);
        if ($xml === false) {
            return "Erreur lors du parsing XML.";
        }
        // résultat sous la forme d'un tableau html
        $res = "\t<table>\n";
    
        $res .= "\t\t<tr>\n\t\t\t<th scope='row'>IP</th>\n\t\t\t<td>" . $ipAddress . "</td>\n\t\t</tr>\n";
        $res .= "\t\t<tr>\n\t\t\t<th scope='row'>Ville</th>\n\t\t\t<td>" . $xml->geoplugin_city . "</td>\n\t\t</tr>\n";
        $res .= "\t\t<tr>\n\t\t\t<th scope='row'>Région</th>\n\t\t\t<td>" . $xml->geoplugin_region . "</td>\n\t\t</tr>\n";
        $res .= "\t\t<tr>\n\t\t\t<th scope='row'>Pays</th>\n\t\t\t<td>" . $xml->geoplugin_countryName . "</td>\n\t\t</tr>\n";
    
        $res .= "\t</table>\n";
    
        return $res;
    }
    
    function position_geographiqueJSON(){

        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $url = "https://ipinfo.io/{$ipAddress}/geo";
        $response = file_get_contents($url);
        $response = file_get_contents($url);
        if ($response === false) {
            echo "Impossible de récupérer les informations de géolocalisation pour cette adresse IP.";
        }

        $geoInfo = json_decode($response, true);

        $res = "\t<table>\n";

        $res .= "\t\t<tr>\n\t\t\t<th scope='row'>IP</th>\n\t\t\t<td>" . $ipAddress . "</td>\n\t\t</tr>\n";
        $res .= "\t\t<tr>\n\t\t\t<th scope='row'>Ville</th>\n\t\t\t<td>" . ($geoInfo['city'] ?? 'Non spécifié') . "</td>\n\t\t</tr>\n";
        $res .= "\t\t<tr>\n\t\t\t<th scope='row'>Région</th>\n\t\t\t<td>" . ($geoInfo['region'] ?? 'Non spécifié') . "</td>\n\t\t</tr>\n";
        $res .= "\t\t<tr>\n\t\t\t<th scope='row'>Pays</th>\n\t\t\t<td>" . ($geoInfo['country'] ?? 'Non spécifié') . "</td>\n\t\t</tr>\n";
        $res .= "\t\t<tr>\n\t\t\t<th scope='row'>Code postal</th>\n\t\t\t<td>" . ($geoInfo['postal'] ?? 'Non spécifié') . "</td>\n\t\t</tr>\n";
    
        $res .= "\t</table>\n";

        return $res;
    }

    function extraction_infoXML() {
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $apiKey = "58b774fda81ebc97a84914C65290112b";
        $url = "https://api.whatismyip.com/ip-address-lookup.php?key=$apiKey&input=$ipAddress&output=xml";
    
        $responseXml = file_get_contents($url);
        if ($responseXml === false) {
            return "Error retrieving data.";
        }
    
        $xml = simplexml_load_string($responseXml);
        if ($xml === false) {
            return "Error parsing XML.";
        }
    
        $res = "<table>\n";

        $res .= "\t<tr><th scope='row'>Query Status</th><td>" . (string)$xml->query_status->query_status_code . " - " . (string)$xml->query_status->query_status_description . "</td></tr>\n";
        $res .= "\t<tr><th scope='row'>IP Address</th><td>" . $ipAddress . "</td></tr>\n";
        $res .= "</table>\n";
    
        return $res;
    }

    function rechercherGares($recherche) {
        $apiKey = "a908747a-e6e5-420e-a91d-7590dc83d005";
        $url = "https://api.sncf.com/v1/places?q=" . urlencode($recherche);
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: " . $apiKey
        ));
    
        $resultat = curl_exec($ch);
        curl_close($ch);
    
        if ($resultat === false) {
            return "Erreur lors de la récupération des données.";
        }
    
        $data = json_decode($resultat, true);
    
        if (!isset($data['places'])) {
            return "Aucune gare trouvée.";
        }
    
        return $data;
    }
    
    
    function afficherGares($recherche) {
        $donnees = rechercherGares($recherche);
    
        $res = "<table>\n";
        $res .= "<tr><th>Nom de la gare</th><tr>";
        foreach ($donnees['places'] as $place) {
            $res .= "<tr><td>" . htmlspecialchars($place['name']) . "</td></tr>";
        }
        $res .= "</table>";

        return $res;
    }
    

?>