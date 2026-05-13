<?php
// Nastavenie obsahového typu na obrázok PNG
header('Content-Type: image/png');

// Vytvorenie obrázka
$width = 100;
$height = 200;
$image = imagecreatetruecolor($width, $height);

// Nastavenie farieb
$white = imagecolorallocate($image, 255, 255, 255);
$black = imagecolorallocate($image, 0, 0, 0);

// Vyplnenie pozadia obrázka bielou farbou
imagefill($image, 0, 0, $white);

// Vypísanie čísel od 1 do 10
for ($i = 1; $i <= 11; $i++) {
    // Použitie preddefinovaného písma (1 až 5) a vypísanie čísla
    // Pozícia Y je upravená, aby čísla boli vertikálne rozložené
    imagestring($image, 5, 10, $i * 15, $i, $black);
}

// Výstup obrázka
imagepng($image);
imagedestroy($image);
?>