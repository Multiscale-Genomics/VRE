<?php

require "../phplib/genlibraries.php";

//if($_POST){
if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST"){

	$path = "../assets/avatars/";

	$valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
	$name = $_FILES['imageprofile']['name'];
	$size = $_FILES['imageprofile']['size'];
		
	if(strlen($name)) {
		list($txt, $ext) = explode(".", strtolower($name));
		
		if(in_array($ext,$valid_formats)) {
				
			if($size<(1024*1024)) { // Image size max 1 MB

				$actual_image_name = $_SESSION['User']['id'].".".$ext;
				$tmp = $_FILES['imageprofile']['tmp_name'];
			
				if(move_uploaded_file($tmp, $path.$actual_image_name)) {
					// file successfully uploaded
					echo "1";
				} else	echo "0"; // error uploading file
			
			} else 	echo "2"; // max size error
		
		} else echo "3"; // format error
	
	} else {
		$filename = glob($path.$_SESSION['User']['id'].'.*');
		$avatarImg = $filename[0];
		if (file_exists($avatarImg)){
			// if file exists, then delete
			unlink($avatarImg);
			echo "5";
		} else {
			// if file not exisits, then warn the user he/she must provide a file
			echo "4";
		}

	}

}else{
	redirect($GLOBALS['URL']);
}

?>
