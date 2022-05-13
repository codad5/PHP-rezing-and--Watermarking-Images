<?php
require_once './Class.EditImage.php';

use codad5\EditImage;

$images = new FilesystemIterator('../00001__images');
$images = new RegexIterator($images, '/(?<!watermark|_rotated)\.(jpg|png|gif|webp)$/');
$originals = [];
foreach ($images as $image) {
    $originals[] = $image->getFilename();
}

try{
    $Edit = new EditImage($originals, '../00001__images');
    // $Edit->setOutputSizes([ 400, "500", 600, 750]);
    $Edit->watermark('../00001__images/watermark.png');
    $result = $Edit->OutputImages('./Edited2');
    if ($result['output']) {
        echo 'The following images were generated:<br>';
        echo '<ul>';
        foreach ($result['output'] as $output) {
            echo "<li>$output</li>";
        }
        echo '</ul>';
    }
    if ($result['invalid']) {
        echo 'The following files were invalid:<br>';
        echo '<ul>';
        foreach ($result['invalid'] as $invalid) {
            echo "<li>$invalid</li>";
        }
        echo '</ul>';
    }
}catch(Exception $e){
    // unlink()
    echo $e->getMessage();

}
// print_r($originals);
