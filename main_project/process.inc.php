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
 if (empty($files)) {
 	# code...
 	header("location: ../../../?error=nofilefound");
 	exit;
 }

 var_dump($files);
 echo "<br><br>";
 var_dump(prepare_file_array($files));

$files = prepare_file_array($files);
$process = new codad5\imageUpload($files);
$process->moveFIles();

