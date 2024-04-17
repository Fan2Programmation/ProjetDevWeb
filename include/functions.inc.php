<?php
    declare(strict_types=1);

    function randomImage() : void {
        // Chemin vers le dossier des photos
        $dir = 'photos';

        // Vérifie si le dossier existe et lire son contenu
        if (is_dir($dir)) {
            // Enlève les entrées '.' et '..'
            $files = array_diff(scandir($dir), array('.', '..')); 
            
            // Vérifier s'il y a des images
            if (count($files) > 0) {
                $randomImage = $files[array_rand($files)]; // Sélectionner une image au hasard
                $imagePath = $dir . '/' . $randomImage; // Chemin complet vers l'image

                // Afficher l'image avec figure et figcaption
                echo "<figure><img src='$imagePath' alt='Image aléatoire'></figure>";
            } else {
                echo "<p>Aucune image trouvée dans le dossier.</p>";
            }
        } else {
            echo "<p>Le dossier spécifié n'existe pas.</p>";
        }
    }
?>