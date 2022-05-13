<?php

$image = "../00001__images/pelican.webp";

# NOTE: PHP 8 supports the webp extension
// php 8 now recongnise the webp format
$size = getimagesize($image);


if($size === false && mime_content_type($iage)  == "image/webp"){
    $resource = imagecreatefromwebp($image);
    $size['w'] = imagesx($resource);
    $size['h'] = imagesy($resource);
    imagedestroy($resource);
}

print_r($size);