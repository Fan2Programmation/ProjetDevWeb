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
                    <h2>Recherche & Informations d'une gare</h2>
                    <form action="index.php" method="get">
                        <label for="nom">Entrez un nom de gare : </label>
                        <input type="search" id="nom" name="nom" placeholder="Entrez votre recherche..." required/>
                        <button type="submit">Rechercher</button>
                    </form>
                    <?php
                        if(isset($_GET['nom'])) {
                            $choix_gare = $_GET['nom'];
                            echo listeGaresSimilaires($choix_gare);
                            $gare_info = informationsGare($choix_gare);
                            if ($gare_info !== null) {
                                echo "<h3>Informations: $choix_gare</h3>";
                                echo "<p>Nom : ".$gare_info['name']."</p>";
                                echo "<p>Code UIC : ".$gare_info['stop_area']['codes'][0]['value']."</p>";
                                if (isset($gare_info['stop_area']['coord']['lat']) && isset($gare_info['stop_area']['coord']['lon'])) {
                                    echo "<p>Coordonnées : Latitude " . $gare_info['stop_area']['coord']['lat'] . ", Longitude " . $gare_info['stop_area']['coord']['lon'] . "</p>";
                                } else {
                                    echo "<p>Coordonnées : Information non disponible</p>";
                                }
                            } else {
                                echo "Aucune information disponible pour la gare '$choix_gare'";
                            }
                            echo '<a href="index.php" style="display:inline-block;margin-top:20px;padding:10px;background-color:#007bff;color:white;text-decoration:none;border-radius:5px;">Retour</a>';
                        }
                    ?>
                </article>
            </section>
        </main>
<?php require "./include/footer.inc.php"; ?>    