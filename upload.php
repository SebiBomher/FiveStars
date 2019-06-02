<?php include('server.php') ?>
<!DOCTYPE html>
<html>
<head>
  	<title>Upload image</title>
  	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<div class="header">
  		<h2>Upload image</h2>
  	</div>
  	<form action="upload.php" method="POST" enctype="multipart/form-data">
  		<?php include('errors.php'); ?>
  		<div class="input-group">
    		<label>File: </label>
    		<input type="file" name="image" />
    		<div class="input-group">
  	  			<button type="submit" class="btn" name="upload_photo">Upload Photo</button>
  			</div>
  		</div>
	</form>
</body>
</html>