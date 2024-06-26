<?php
$title = "CityTapeur";
$desc = "Aide à la mobilité intra-muros";
$keywords = "PHP, Université, Projet";
$update_date = "27/04/2024";
$update_hour = "17:52";
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
    <ul class="navigation">
        <li><a href="#rechercheGare">Obtenir des informations</a></li>
        <li><a href="#derniereGareConsultee">Dernière gare consultée</a></li>
        <li><a href="#gareProcheDeChezMoi">Certains arrêts proches de chez vous</a></li>
        <li><a href="#gareProcheAdresse">Certains arrêts proches d'une adresse</a></li>
    </ul>
    <main>
        <section>
            <h1>Aide à la mobilité en Île-De-France</h1>
            <article id="rechercheGare">
                <h2>Obtenir des informations</h2>
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
                        $type = isset($_GET['type']) ? $_GET['type'] : '';
                        $date = isset($_GET['date']) ? $_GET['date'] : '';
                        $heure = isset($_GET['heure']) ? $_GET['heure'] : '';

                        echo "\t\t\t\t<form action=\"index.php\" method=\"get\" target=\"_self\">\n";
                        echo "\t\t\t\t\t<select name=\"type\" required='required'>\n";
                        echo "\t\t\t\t\t\t<option value=\"depart\" ".($type == 'depart' ? 'selected="selected"' : '').">Départ</option>\n";
                        echo "\t\t\t\t\t\t<option value=\"arrivee\" ".($type == 'arrivee' ? 'selected="selected"' : '').">Arrivée</option>\n";
                        echo "\t\t\t\t\t</select>\n";
                        echo "\t\t\t\t\t<input type=\"date\" name=\"date\" value=\"".htmlspecialchars($date)."\" required='required'/>\n";
                        echo "\t\t\t\t\t<input type=\"time\" name=\"heure\" value=\"".htmlspecialchars($heure)."\" required='required'/>\n";
                        echo "\t\t\t\t\t<input type=\"hidden\" name=\"id\" value=\"".$_GET['id']."\" />\n";
                        echo "\t\t\t\t\t<input type=\"hidden\" name=\"id2\" value=\"".$_GET['id2']."\" />\n";
                        echo "\t\t\t\t\t<button type=\"submit\">Go !</button>\n";
                        echo "\t\t\t\t</form>\n";
                        if (isset($_GET['type']) && isset($_GET['date']) && isset($_GET['heure'])) {
                            stockerGareConsultee($_GET['id']);
                            stockerGareConsultee($_GET['id2']);
                            echo afficherItineraire($_GET['id'], $_GET['id2'], $_GET['type'], $_GET['date'], $_GET['heure']);
                        }
                    }
                }
                ?>
            </article>
            <article id="derniereGareConsultee">
                <h2>Dernière gare consultée</h2>
                <?php echo derniereGareConsultee(); ?>
            </article>
            <article id="gareProcheDeChezMoi">
                <h2>Certains arrêts proches de chez vous</h2>
                <?php $coords = getUserCoords(); echo gareProche($coords["latitude"], $coords["longitude"]); ?>
            </article>
            <article id="gareProcheAdresse">
                <h2>Certains arrêts proches d'une adresse</h2>
                <form action="index.php#gareProcheAdresse" method="get" target="_self">
                <input type="text" id="adresse" name="adresse" placeholder="<?php echo isset($_GET['adresse']) ? $_GET['adresse'] : "Adresse" ?>" required="required" />
                <input type="text" id="ville" name="ville" placeholder="<?php echo isset($_GET['ville']) ? $_GET['ville'] : "Ville" ?>" required="required" />
                    <button type="submit">Rechercher</button>
                </form>
                <?php
                if (isset($_GET['adresse']) && isset($_GET['ville'])) {
                    $coords = getAddressCoords($_GET['adresse']." ".$_GET['ville']);
                    if($coords !== null) {
                        echo gareProche($coords["latitude"], $coords["longitude"]);
                    } else {
                        echo "<p>Impossible de récupérer les informations de géolocalisation pour cette adresse.</p>\n";
                    }
                }
                ?>
            </article>
        </section>
    </main>
<?php require "./include/footer.inc.php"; ?>
