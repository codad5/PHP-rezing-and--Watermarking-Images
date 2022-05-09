<?php

$original = "../images/image2.jpg";
$destination = "edited/";

$sizes = [
    'small' => 300,
    'small@2' => 600,
    'large' => 450,
    'large@2' => 900,
];

$resource = imagecreatefromjpeg($original);

foreach ($sizes as $name => $size) {
    # code...
    $scaled = imagescale($resource, $size);
    imagejpeg($scaled, $destination.'logo_'.$name.'.jpg', 60);
    imagedestroy($scaled);
}
imagedestroy($resource);

echo "done";