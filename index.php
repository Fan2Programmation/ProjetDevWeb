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
                <article id= "horaires de trains et informations en gare">
                    <h2>Horaires de trains et informations en gare</h2>
                    <form action="functions.inc.php" method="post">
                        <p>
                            <label for="search">Entrez une ville:</label>
                            <input type="search" id="search" name="q" placeholder="Entrez une ville..."/>
                            <button type="submit">Rechercher</button>
                        </p>
                    </form>
                    <?php
                        if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['q'])) {
                            $recherche = $_POST['q'];
                        
                            $resultats = afficherGares($recherche);
                        } else {
                            $resultats = "Veuillez entrer une recherche.";
                        }
                    ?>
                </article>
            </section>
        </main>
<?php require "./include/footer.inc.php"; ?>    