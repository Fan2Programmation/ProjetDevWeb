<?php
    declare(strict_types=1);

    /**
     * Affiche le contenu de l'API de la NASA
     * @return string Le contenu de l'API de la NASA
     */
    function afficher_contenu():string {
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
            $RESULTAT = "<video controls>\n\t<source src=\"" . $DATA['url'] . "\" type=\"video/mp4\">\n\t" . $DATA['title'] . "\n</video>\n";
        }
        return $RESULTAT;
    }

    /**
     * Récupère les informations de géolocalisation de l'utilisateur à partir de son adresse IP
     * @return string Les informations de géolocalisation de l'utilisateur
     */
    function position_geographiqueXML():string {
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
    
    /**
     * Récupère les informations de géolocalisation de l'utilisateur à partir de son adresse IP
     * @return string Les informations de géolocalisation de l'utilisateur
     */
    function position_geographiqueJSON():string{

        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $url = "https://ipinfo.io/{$ipAddress}/geo";
        $response = file_get_contents($url);
        if ($response === false) {
            echo "Impossible de récupérer les informations de géolocalisation pour cette adresse IP.";
        }

        $donnees = json_decode($response, true);

        $res = "\t<table>\n";

        $res .= "\t\t<tr>\n\t\t\t<th scope='row'>IP</th>\n\t\t\t<td>" . $ipAddress . "</td>\n\t\t</tr>\n";
        $res .= "\t\t<tr>\n\t\t\t<th scope='row'>Ville</th>\n\t\t\t<td>" . ($donnees['city'] ?? 'Non spécifié') . "</td>\n\t\t</tr>\n";
        $res .= "\t\t<tr>\n\t\t\t<th scope='row'>Région</th>\n\t\t\t<td>" . ($donnees['region'] ?? 'Non spécifié') . "</td>\n\t\t</tr>\n";
        $res .= "\t\t<tr>\n\t\t\t<th scope='row'>Pays</th>\n\t\t\t<td>" . ($donnees['country'] ?? 'Non spécifié') . "</td>\n\t\t</tr>\n";
        $res .= "\t\t<tr>\n\t\t\t<th scope='row'>Code postal</th>\n\t\t\t<td>" . ($donnees['postal'] ?? 'Non spécifié') . "</td>\n\t\t</tr>\n";
    
        $res .= "\t</table>\n";

        return $res;
    }

    /**
     * Extrait les informations de l'utilisateur à partir de son adresse IP
     * @return string Les informations extraites de l'utilisateur
     */
    function extraction_infoXML():string {
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

    /**
     * Récupère les coordonnées géographiques de l'utilisateur
     * @return string Les coordonnées géographiques de l'utilisateur
     */
    function getUserCoords() {
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $url = "https://ipinfo.io/{$ipAddress}/json";
        $response = file_get_contents($url);
        if ($response === false) {
            echo "Impossible de récupérer les informations de géolocalisation pour cette adresse IP.";
        }
        $donnees = json_decode($response, true);
        $coords = explode(',', $donnees['loc']); // Sépare la chaîne en un tableau à l'aide de la virgule comme séparateur
        return [
            'latitude' => $coords[0],
            'longitude' => $coords[1]
        ];
    }

    /**
     * Récupère les coordonnées géographiques d'une adresse
     * @param string $address L'adresse dont on veut récupérer les coordonnées
     * @return array Les coordonnées géographiques de l'adresse (latitude et longitude)
     */
    function getAddressCoords($address) {
        // On encode l'adresse pour qu'elle soit utilisable dans une URL
        $address = urlencode($address);
        $API_key = "310e5cf5652d49148170713344cb016a";
        // On monte l'URL de l'API avec l'adresse et la clé dedans
        $url = "https://api.opencagedata.com/geocode/v1/json?q=$address&key=$API_key";
        $fluxjson = file_get_contents($url);
        if ($fluxjson !== false) {
            $donnees = json_decode($fluxjson, true);
            return [
                'latitude' => $donnees['results'][0]['geometry']['lat'],
                'longitude' => $donnees['results'][0]['geometry']['lng']
            ];
        }
        return null;
    }
?>