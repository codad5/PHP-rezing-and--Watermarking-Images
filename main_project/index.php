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
		<input type="file"  name="Watermsrker" placeholder="put in Watermsrker">
		<br/>
		<input type="submit" name="	" value="submit">
	</form>
</body>
</html>