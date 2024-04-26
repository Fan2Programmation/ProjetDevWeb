<?php
$title = "CityTapeur";
$desc = "Aide à la mobilité intra-muros";
$keywords = "PHP, Université, Projet";
$update_date = "25/03/2024";
$update_hour = "15:23";
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
            <h1>Aide à la mobilité en Île-De-France</h1>
            <article id="recherchegare">
                <h2>Formulaire à remplir</h2>
                <form action="index.php" method="get" target="_self">
                    <label for="depart">Gare de départ : </label>
                    <input type="search" id="depart" name="depart" placeholder="<?php  echo isset($_GET['id']) ? nomDeLaGare($_GET['id']) : "Entrez la gare de départ" ?>" <?php echo (isset($_GET['id']) || isset($_GET['depart'])) ? "disabled='disabled'" : "required='required'" ?>/>
                    <fieldset>
                        <legend>Sélectionnez une option</legend>
                        <span class="form">
                            <input type="radio" id="departs" name="option" value="departs" <?php echo $selectedOption == 'departs' ? 'checked="checked"' : ''; ?> required="required" />
                            <label for="departs">Afficher les prochains départs depuis cet arrêt</label>
                        </span>
                        <span class="form">
                            <input type="radio" id="itineraire" name="option" value="itineraire" <?php echo $selectedOption == 'itineraire' ? 'checked="checked"' : ''; ?> />
                            <label for="itineraire">Afficher l'itinéraire (tous modes) entre la gare de départ et une autre gare</label>
                        </span>
                    </fieldset>
                    <button type="submit">Rechercher</button>
                </form>
                <?php
                // Si l'option est d'afficher les départs depuis la gare
                if($selectedOption === "departs") {
                    // On regarde si l'utilisateur vient d'entrer une gare de départ
                    if (isset($_GET['depart'])) {
                        // On affiche alors les suggestions de gare de départ plus précises
                        echo listeGaresSimilaires($_GET['depart'], "id");
                    }
                    // Si l'utilisateur à selectionné une gare de départ plus précise à partir des suggestions
                    if (isset($_GET['id'])) {
                        // On affiche les départs depuis cette gare
                        stockerGareConsultee($_GET['id']);
                        echo afficherProchainsDeparts($_GET['id']);
                    }
                // Si l'option est d'afficher un itinéraire entre deux gares
                } else if($selectedOption === "itineraire") {
                    // On regarde si l'utilisateur vient d'entrer une gare de départ
                    if (isset($_GET['depart'])) {
                        // On affiche alors les suggestions de gare de départ plus précises
                        echo listeGaresSimilaires($_GET['depart'], "id");
                    }
                    // Si l'utilisateur à selectionné une gare de départ plus précise à partir des suggestions mais pas de gare d'arrivée
                    if (isset($_GET['id']) && !isset($_GET['arrivee'])) {
                        // On lui demande d'entrer une gare d'arrivée en recréant un formulaire tout en gardant l'identifiant de la gare de départ
                        echo "<form action=\"index.php\" method=\"get\" target=\"_self\">\n";
                        echo "\t\t\t\t<input type=\"hidden\" name=\"id\" value=\"".$_GET['id']."\" />\n";
                        echo "\t\t\t\t<label for=\"arrivee\">Gare d'arrivée : </label>\n";
                        echo "\t\t\t\t<input type=\"search\" id=\"arrivee\" name=\"arrivee\" placeholder=\"".(isset($_GET['id2']) ? nomDeLaGare($_GET['id2']) : "Entrez la gare d'arrivée")."\" ".(isset($_GET['id2']) ? "disabled='disabled'" : "")." required='required' />\n";
                        echo "\t\t\t\t<button type=\"submit\">Rechercher</button>\n";
                        echo "\t\t\t\t</form>\n";
                    }
                    // Si l'utilisateur à selectionné une gare d'arrivée
                    if (isset($_GET['arrivee'])) {
                        // On lui suggère des propositions plus précises
                        echo listeGaresSimilaires($_GET['arrivee'], "id=".$_GET['id']."&id2");
                    }
                    // Enfin si on a bien récupéré l'identifiant des deux gares voulues
                    if (isset($_GET['id']) && isset($_GET['id2'])) {
                        echo "\t\t\t\t<form action=\"index.php\" method=\"get\" target=\"_self\">\n";
                        echo "\t\t\t\t\t<select name=\"type\" required>\n";
                        echo "\t\t\t\t\t\t<option value=\"depart\">Départ</option>\n";
                        echo "\t\t\t\t\t\t<option value=\"arrivee\">Arrivée</option>\n";
                        echo "\t\t\t\t\t</select>\n";
                        echo "\t\t\t\t\t<input type=\"date\" name=\"date\" required/>\n";
                        echo "\t\t\t\t\t<input type=\"time\" name=\"heure\" required/>\n";
                        echo "\t\t\t\t\t<input type=\"hidden\" name=\"id\" value=\"".$_GET['id']."\" />\n";
                        echo "\t\t\t\t\t<input type=\"hidden\" name=\"id2\" value=\"".$_GET['id2']."\" />\n";
                        echo "\t\t\t\t\t<button type=\"submit\">Go !</button>\n";
                        echo "\t\t\t\t</form>\n";
                        if (isset($_GET['id']) && isset($_GET['id2']) && isset($_GET['type']) && isset($_GET['date']) && isset($_GET['heure'])) {
                            stockerGareConsultee($_GET['id']);
                            stockerGareConsultee($_GET['id2']);
                            echo afficherItineraire($_GET['id'], $_GET['id2'], $_GET['type'], $_GET['date'], $_GET['heure']);
                        }
                    }
                }
                ?>
            </article>
            <article id="derniereGareConsultee">
                <?php echo derniereGareConsultee(); ?>
            </article>
        </section>
    </main>
<?php require "./include/footer.inc.php"; ?>
