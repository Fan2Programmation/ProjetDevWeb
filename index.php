<?php
    $title= "Projet de développement web";
    $desc = "Projet de développement web";
    $keywords = "PHP, Université, Projet";
    $update_date= "01/03/2024";
    $update_hour= "21:17";
    require "./include/functions.inc.php";
    require "./include/header.inc.php";
?>

        <main>
            <section>
                <article id= "recherchegare">
                    <h2>Horaires de trains et informations en gare</h2>
                    <form action="index.php" method="post">
                    <label for="nom">Entrez un nom de gare :</label>
                    <input type="text" id="nom" name="nom" required>
                    <input type="submit" value="Rechercher">
                    </form>
                    <?php
                        if (isset($_POST['nom'])) {
                            $nom_gare = $_POST['nom'];
                            $api_key = 'a908747a-e6e5-420e-a91d-7590dc83d005';
                            $url = "https://api.navitia.io/v1/coverage/fr-idf/places?q=" . urlencode($recherche)."&type[]=stop_area&key=$apiKey";                     
                            $fluxjson = file_get_contents($url);
                            if ($fluxjson !== false) {
                                $donnee = json_decode($fluxjson, true);
                                $suggestions = array();
                                foreach ($donnee['places'] as $place) {
                                    $suggestions[] = $place['name'];
                                }
                                echo "<h3>Résultats de la recherche pour '$nom_gare'</h3>";
                                if (!empty($suggestions)) {
                                    echo "<ul>";
                                    foreach ($suggestions as $gare) {
                                        echo "<li><a href='?nom=".urlencode($gare)."'>$gare</a></li>";
                                    }
                                    echo "</ul>";
                                } else {
                                    echo "Aucune gare trouvée pour '$nom_gare'";
                                }
                            } else {
                                echo "Erreur lors de la récupération des suggestions";
                            }
                        }
                        if(isset($_GET['nom'])) {
                            $gare_selectionnee = $_GET['nom'];
                            $gare_info = rechercherGares($gare_selectionnee);
                        
                            if ($gare_info !== null) {
                                $informations_gare = [
                                    "Nom" => $gare_info['name'],
                                    "Code UIC" => $gare_info['stop_area']['codes'][0]['value'],
                                    "Type" => isset($gare_info['stop_area']['stop_area_type']) ? $gare_info['stop_area']['stop_area_type'] : "Information non disponible",
                                    "Coordonnées" => isset($gare_info['stop_area']['coord']['lat']) && isset($gare_info['stop_area']['coord']['lon']) ? "Latitude " . $gare_info['stop_area']['coord']['lat'] . ", Longitude " . $gare_info['stop_area']['coord']['lon'] : "Information non disponible",
                                    "Ville" => isset($gare_info['administrative_regions'][0]['name']) ? $gare_info['administrative_regions'][0]['name'] : "Information non disponible",
                                ];
                        
                                echo "<h3>Informations sur la gare : $gare_selectionnee</h3>";
                                echo "<table border='1'>";
                                foreach ($informations_gare as $cle => $valeur) {
                                    echo "<tr><td>$cle</td><td>$valeur</td></tr>";
                                }
                                echo "</table>";
                            } else {
                                echo "Aucune information disponible pour la gare '$gare_selectionnee'";
                            }
                        }
                    ?>
                </article>
            </section>
        </main>
<?php require "./include/footer.inc.php"; ?>    