<?php
if (isset($_FILES['myFile'])) {

	
	switch(strtolower($_FILES['myFile']['type'])) {
		case 'image/jpeg':
			$image = imagecreatefromjpeg($_FILES['myFile']['tmp_name']);
			break;
		case 'image/png':
			$image = imagecreatefrompng($_FILES['myFile']['tmp_name']);
			break;
		case 'image/gif':
			$image = imagecreatefromgif($_FILES['myFile']['tmp_name']);
			break;
		default:
			exit('Unsupported type: '.$_FILES['myFile']['type']);
	}

	// Target dimensions
	$max_width = 405;
	$max_height = 720;

	// Get current dimensions
	$old_width  = imagesx($image);
	$old_height = imagesy($image);

	// Calculate the scaling we need to do to fit the image inside our frame
	$scale      = min($max_width/$old_width, $max_height/$old_height);

	// Get the new dimensions
	$new_width  = ceil($scale*$old_width);
	$new_height = ceil($scale*$old_height);

	$new = imagecreatetruecolor($new_width, $new_height);
	imagecopyresampled($new, $image, 0, 0, 0, 0, $new_width, $new_height, $old_width, $old_height);

	imagejpeg($new, $_FILES['myFile']['tmp_name'] );

	imagedestroy($image);
	imagedestroy($new);	
	
	move_uploaded_file($_FILES['myFile']['tmp_name'], "uploads/" . $_FILES['myFile']['name']);
	$_SESSION['imageFile']=$_FILES['myFile']['name'];
    echo $_SESSION['imageFile'];
}
?>