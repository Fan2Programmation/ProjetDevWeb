<?php
    $title= "Projet de développement web";
    $desc = "Projet de développement web";
    $keywords = "PHP, Université, Projet";
    $update_date= "01/03/2024";
    $update_hour= "21:17";
    require "./include/functionsSNCF.inc.php";
    require "./include/header.inc.php";
?>

        <main>
            <section>
                <article id= "recherchegare">
                    <h2>Prochains départs en gare</h2>
                    <form action="index.php" method="get">
                        <label for="recherche">Entrez un nom de gare : </label>
                        <input type="search" id="recherche" name="recherche" placeholder="Entrez votre recherche..." required/>
                        <button type="submit">Rechercher</button>
                    </form>
                    <?php
                        if(isset($_GET['recherche'])) {
                            echo listeGaresSimilaires($_GET['recherche']);
                        }
                        if(isset($_GET['gare'])) {
                            echo findID($_GET['gare']);
                        }
                    ?>
                </article>
            </section>
        </main>
<?php require "./include/footer.inc.php"; ?>    