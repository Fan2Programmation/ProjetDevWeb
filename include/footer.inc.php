<?php
declare(strict_types=1);
    require "./include/counter.php";
?>
    <footer>
         <span>
             <strong>Contactez-nous : </strong><a href="mailto:fauxmail@gmail.com">fauxmail@gmail.com</a> | Tél : 01-23-45-67-89.
         </span>
         <span>
             Page mise-à-jour le <?php echo $update_date?> à <?php echo $update_hour?> par Thibault et Hayder. CY Cergy Paris Université.
         </span>
         <span><a href="tech.php">PAGE TECH</a></span>
         <span>Nombre de visites : <?php echo $hits; ?></span>
    </footer> 
</body>
</html>