<?php
    require "./include/functions.inc.php";

    // Définir ou changer le mode (dark ou light)
    if (isset($_POST['theme'])) { // Vérifie si le formulaire a été soumis via l'image
        $theme = $_POST['theme'];
        setcookie('theme', $theme, time() + 86400 * 30, '/'); // Expire dans 30 jours
        $_COOKIE['theme'] = $theme; // Mise à jour immédiate de la variable $_COOKIE

        // Construire la query string à partir des paramètres GET actuels pour les inclure dans la redirection
        $queryString = http_build_query($_GET);

        // Rediriger vers la même page avec les paramètres GET conservés
        header("Location: " . $_SERVER['PHP_SELF'] . '?' . $queryString);
        exit;
    }

    // Valeurs par défaut
    $theme = 'lightmode.css';
    $otherTheme = 'darkmode.css';
    $image = './images/darkmode.png';

    if (isset($_COOKIE['theme'])) {
        $theme = $_COOKIE['theme'];
        if ($theme == "darkmode.css") {
            $otherTheme = 'lightmode.css';
            $image = './images/lightmode.png';
        }
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title><?php echo $title; ?></title>
    <meta charset="UTF-8"/>
    <meta name="description" content="<?php echo $desc; ?>"/>
    <meta name="keywords" content="<?php echo $keywords; ?>"/>
    <meta name="author" content="Thibault TERRIE, Hayder UR REHMAN"/>
    <link rel="stylesheet" href="<?php echo $theme; ?>"/>
</head>
<body>
    <header>
        <a href="index.php"><img src="./images/logo.png" alt="CityTapeur"/></a>
        <aside><?php randomImage(); ?></aside>
        <form id="viewmode" method="post">
            <input type="hidden" name="theme" value="<?php echo $otherTheme; ?>">
            <input type="image" src="<?php echo $image; ?>" alt="Change viewmode">
        </form>
    </header>