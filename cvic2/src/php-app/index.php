<?php

header('Content-Type: image/png');

$width = 100;
$height = 200;
$image = imagecreatetruecolor($width, $height);

$white = imagecolorallocate($image, 255, 255, 255);
$black = imagecolorallocate($image, 0, 0, 0);

imagefill($image, 0, 0, $white);

for ($i = 1; $i <= 11; $i++) {
    imagestring($image, 5, 10, $i * 15, $i, $black);
}

imagepng($image);
imagedestroy($image);
?>