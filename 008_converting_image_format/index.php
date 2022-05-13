<?php

$image = "../00001__images/pelican.webp";

$resource = imagecreatefromwebp($image);
imagejpeg($resource, 'edited/pelican.jpg');
imagedestroy($resource);
echo 'done';