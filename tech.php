<?php
    $title= "Projet de développement web";
    $desc = "Projet de développement web";
    $keywords = "PHP, Université, Projet";
    $update_date = "27/04/2024";
    $update_hour = "17:52";
    require "./include/header.inc.php";
?>

        <main>
            <section>
            <h2>Prise en main des formats d’échanges JSON et XML des API Web</h2>
                <article id= "exercice1">
                    <h3>Exercice 1</h3>
                    <?php
                        print(afficher_contenu());
                    ?>
                </article>
                <article id= "exercice2">
                    <h3>Exercice 2</h3>
                    <?php
                        print(position_geographiqueXML());
                    ?>
                </article>
                <article id= "exercice3">
                    <h3>Exercice 3</h3>
                    <?php
                        print(position_geographiqueJSON());
                    ?>
                </article>
                <article id= "exercice4">
                    <h3>Exercice 4</h3>
                    <?php
                        print(extraction_infoXML());
                    ?>
                </article>
            </section>
        </main>
<?php require "./include/footer.inc.php"; ?>    