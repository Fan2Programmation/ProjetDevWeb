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
            <h2>horaires de trains et informations en gare</h2>
                <article id= "exercice1">
                    <h3>Exercice 1</h3>
                    <?php
                        print(afficherGares("Paris"));
                    ?>
                </article>
            </section>
        </main>
<?php require "./include/footer.inc.php"; ?>    