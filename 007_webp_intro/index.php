<?php
$image1 = "../00001__images/image2.jpg";
$size = getimagesize($image1);

// var_dump($size);

$image = imagecreatefromjpeg($image1);
// var_dump($image);
$watermark = imagecreatetruecolor(60, 20);
imagefilledrectangle($watermark, 0,0, 40, 40, 0x000000);
imagestring($watermark, 5, 10, 5, 'Codad5', 0xFFFFFF);

// imagecopymerge($image, $watermark, 1);
header("Content-Type: ".$size['mime']);
imagejpeg($watermark);
imagedestroy($watermark);
# TODO: remove
imagedestroy($image);