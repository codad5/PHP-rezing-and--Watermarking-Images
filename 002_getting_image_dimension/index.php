<?php

$original = "../00001__images/image2.jpg";
$destination = "edited/";

$dimension = getimagesize($original);

// var_dump($dimension);

$w = $dimension[0];
$h = $dimension[1];
$ratios = [
    'small' => 300/$w,
    'small@2' => 600/$w,
    'large' => 450/$w,
    'large@2' => 900/$w,
];


foreach ($ratios as $name => $value) {
    # code...
    $w2 = round($w * $value);
    $h2 = round($h * $value);
    
    echo "$name: $w2 x $h2 </br>";

}