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
                    <h2>Recherche & Informations d'une gare ou arrêt de bus</h2>
                    <form action="index.php" method="get">
                        <label for="nom">Entrez un nom de gare : </label>
                        <input type="search" id="nom" name="nom" placeholder="Entrez votre recherche..." required/>
                        <button type="submit">Rechercher</button>
                    </form>
                    <?php
                        if(isset($_GET['nom'])) {
                            $choix_gare = $_GET['nom'];
                            echo listeGaresSimilaires($choix_gare);
                            echo informationsGare($choix_gare);
                        }
                    ?>
                </article>
            </section>
        </main>
<?php require "./include/footer.inc.php"; ?>    