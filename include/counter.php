<?php
$filename = "counter.txt";

if(file_exists($filename)) {
    $hits = file_get_contents($filename);
    $hits++;
} else {
    $hits = 1;
}

file_put_contents($filename, $hits);

?>