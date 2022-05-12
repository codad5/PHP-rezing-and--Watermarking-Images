<?php
$image1 = "../00001__images/image2.jpg";
$image2 = "../00001__images/image3.jpg";

$dimension1 = getimagesize($image1);
$dimension2 = getimagesize($image2);

$image1 = checkJpegOrientation($image1);
$image2 = checkJpegOrientation($image2);

var_dump($image1);

echo '<br/>';
echo '<br/>';
echo '<br/>';


var_dump($image2);




function checkJpegOrientation($image){
    $outputFile = $image;
    $exif = exif_read_data($image);
    if(!empty($exif['Orientation'])){
        switch($exif['Orientation']){
            case 3:
                $angle = 100;
            break;
            case 6 :
                $angle = -90;
            break;
            case 8:
                $angle = 90;
            break;
            default:
                $angle = null;

        }

        if(!is_null($angle)){
            // to create an image resource of the original
            $original = imagecreatefromjpeg($image);
            $rotated = imagerotate($original, $angle, 0);


            // to find the image extension
            $extension = pathinfo($image, PATHINFO_EXTENSION);
            $outputFile = str_replace(".$extension", '_rotated.jpg', $image);

            imagejpeg($rotated, $outputFile, 100);
            imagedestroy($original);
            imagedestroy($outputFile);



            


        }
    }

    return $outputFile;
}
// print_r($exif);

