<?php
require_once 'function.inc.php';
include_once 'class.EditImage.php';
// use codad5;
if(!isset($_POST['edit']) && $_SERVER['HTTP_REFERER'] != $_SERVER['HTTP_ORIGIN'].'/image-resizing-and-watermark/main_project/' && $_SERVER['REQUEST_METHOD'] != "POST"):
		header("location: ../../../../../../?error=stmterror ");
		exit;

endif;		
// var_dump($_SERVER['HTTP_REFERER'])
 

 $files = $_FILES['Images'];
 $watermarkType = isset($_FILES['watermarkImage']) ? 'image': "text";
 $watermark = isset($_FILES['watermarkImage']) ? $_FILES['watermarkImage'] : $_POST['watermark'];
 if (empty($files)) {
 	# code...
 	header("location: ../../../?error=nofilefound");
 	exit;
 }

 

//  var_dump($files);
//  echo "<br><br>";
//  var_dump(prepare_file_array($files));
$process;
 try{
 $files = prepare_file_array($files);
 $process = new codad5\imageUpload($files);
$process->moveFIles();
echo $watermark;
$process->addWatermark($watermark, $watermarkType);


$imagess = new FilesystemIterator('./uploads/'.$process->getUploadID());
$images = new RegexIterator($imagess, '/(?<!watermark|_rotated)\.(jpg|png|gif|webp)$/');
$watermarks_ = new RegexIterator($imagess, '/(watermark)\.(jpg|png|gif|webp)$/');

$watermarkPro ="";

$originals = [];
foreach ($images as $image) {
	$originals[] = $image->getFilename();

}
foreach ($watermarks_ as $image) {
	// echo $image;
	$watermarkPro = $image;
}
$dir = './uploads/'.$process->getUploadID();
try{
    $Edit = new codad5\EditImage($originals, $dir);
    // $Edit->setOutputSizes([ 400, "500", 600, 750]);
    $Edit->watermark($watermarkPro);
    $result = $Edit->OutputImages($dir.DIRECTORY_SEPARATOR.'edited');
}catch(Exception $e){
	// unlink()
    echo $e->getMessage();
	exit;
	
}
}
catch(Exception $e){
	echo "Error found : " .$e->getMessage();
	exit;
}
header("Location: index.php?end=".$process->getUploadID());
// $process->keepWoke();
// print_r($originals);



// if ($result['output']) {
//         echo 'The following images were generated:<br>';
//         echo '<ul>';
//         foreach ($result['output'] as $output) {
//             echo "<li>$output</li>";
//         }
//         echo '</ul>';
//     }
//     if ($result['invalid']) {
//         echo 'The following files were invalid:<br>';
//         echo '<ul>';
//         foreach ($result['invalid'] as $invalid) {
//             echo "<li>$invalid</li>";
//         }
//         echo '</ul>';
//     }