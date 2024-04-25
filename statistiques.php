<?php
$title = "CityTapeur";
$desc = "Aide à la mobilité intra-muros";
$keywords = "PHP, Université, Projet";
$update_date = "25/03/2024";
$update_hour = "15:23";
require "./include/functionsSNCF.inc.php";
require "./include/header.inc.php";

// Définir ou mettre à jour le cookie lors de la soumission du formulaire
    if (isset($_GET['option'])) {
        setcookie("selectedOption", $_GET['option'], time() + (86400 * 30), "/"); // 86400 = 1 jour
        $selectedOption = $_GET['option'];
    } elseif (isset($_COOKIE['selectedOption'])) {
        $selectedOption = $_COOKIE['selectedOption'];
    } else {
        $selectedOption = ''; // Aucune option sélectionnée par défaut
    }
?>

    <main>
        <section>
            <article id ="histogramme">
                <?php genererHistogramme(); ?>
                <figure>
                    <figcaption>Histogramme des gares consultées</figcaption>
                    <img src="./images/histogramme.png" alt="Histogramme des gares consultées">
                </figure>
            </article>
        </section>
    </main>
<?php require "./include/footer.inc.php"; ?>
