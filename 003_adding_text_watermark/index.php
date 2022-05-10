<?php

$original = "../00001__images/image2.jpg";
$destination = "edited/";


// width of resized image
$sizes = [
    'small' => 300,
    'small@2' => 600,
    'large' => 450,
    'large@2' => 900,
];


// image resource of original imaege
$resource = imagecreatefromjpeg($original);


// seting dimension of text wartermarl

$markW = 170;
$markH =25;
$margin_buttom = $margin_right = 10;

// create an image resource for the watermark;
// params are
//      width , height 
// of the umage
$watermark = imagecreatetruecolor($markW, $markH);

// this  function is for creating background for the waternark
// the params are (the image resource, where the background should begin. where the background should begin, width, height, color of the background )
imagefilledrectangle($watermark, 0,0,$markW, $markH, 0x000000);
// adding the text
// the params are (the image resource, the fontsize, the horizontal co-ordinate where the text should begin. the verticsl cordinate, the text, the color)
imagestring($watermark, 5, 10, 5, 'Codad5', 0xFFFFFF);



foreach ($sizes as $name => $size) {

    
    # code...
    // this is to scale the image

    $scaled = imagescale($resource, $size);
    // get the height of the scaled image

    $h2 = imagesy($scaled);

    // mergeing the watermark and the scaled image
    // the param are as follows 
    // the image to add tbe watermark to 
    // the watermark image resource 
    // the horizontal co-ordinate of the watermark position  
    // the vertical co-ordinate of the watermark position  
    // the top left conner of the water mark from the cordinate given (0, 0)
    // the width of the watermark 
    // the height of the watermark
    // the opacity
    imagecopymerge($scaled, $watermark, $size - $markW- $margin_right, $h2 - $markH - $margin_buttom, 0, 0, $markW, $markH, 50 );


    // saving the image to the destination above
    imagejpeg($scaled, $destination.'logo_'.$name.'.jpg', 60);
    imagedestroy($scaled);
    imagedestroy($watermark);
}
imagedestroy($resource);

echo "done";