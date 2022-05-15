<!DOCTYPE html>
<html>
<head>
	<title>Image Watermsrker</title>
</head>
<body>
	<form method="post" action="process.inc.php" enctype="multipart/form-data">
		<label>Images</label>
		<input type="file" name="Images[]" multiple="true">
		<br/>
		<label>Watermark</label>
		<input type="text"  name="watermark" id="watermarkinput" placeholder="put in Watermsrker">
		<h5>Select to switch watermark input type [image(if checked) or text]</h5>
		<input type="checkbox" id="watermarktype" />
		<br/>
		<input type="submit" name="	" value="submit">
	</form>
	<ul>
	<?php if(isset($_GET['end'])){
		$folderid = $_GET['end'];

		if(is_dir("./uploads/".$folderid)){
			$imagess = new FilesystemIterator('./uploads'.DIRECTORY_SEPARATOR.$folderid.DIRECTORY_SEPARATOR.'edited/');
			$images = new RegexIterator($imagess, '/(jpg|png|gif|webp)$/');
			$watermarks_ = new RegexIterator($imagess, '/(watermark)\.(jpg|png|gif|webp)$/');			
				foreach ($images as $key => $value) {
					# code...
					
					?>
						<li>
							<h3>Preview</h3>
							<img src="<?php echo $value; ?>" width="200px"/><br/>
							<?php echo $value->getFilename(); ?>  <a href="<?php echo $value; ?>" download>Download</a> <br/> 
							
						</li>
						<?php
	}
}else{
	echo "wowo";
}
}
?>
</ul>
</body>
<script>
	const watermakerswitch = document.querySelector('#watermarktype');
	const watermakerinput = document.querySelector('#watermarkinput');
	watermakerswitch.addEventListener('change', () => {
		if(watermakerswitch.checked){
			watermakerinput.type = 'file';
			watermakerinput.name = 'watermarkImage';
		}else{
			watermakerinput.type = 'text';
			watermakerinput.name = 'watermark';
		}
	})
</script>
</html>