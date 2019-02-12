<?php

require "../phplib/genlibraries.php";

//if($_POST){
if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST"){

	/*var_dump($_REQUEST);
	var_dump($_FILES['file']['size']);
	var_dump(getimagesize($_FILES['file']['tmp_name']));*/

	$toolid = $_REQUEST["toolid"];
	$result = $GLOBALS['toolsDevMetaCol']->findOne(array("_id" => $toolid));

	$path = __DIR__ . "/../files/".$result["user_id"]."/.dev/".$toolid."/logo/";
	if(!file_exists($path)) mkdir($path);

	$valid_formats = array("png");
	$name = $_FILES['file']['name'];
	$size = $_FILES['file']['size'];
	$tmp_name = $_FILES['file']['tmp_name'];
	$width = getimagesize($tmp_name)[0];
	$height = getimagesize($tmp_name)[1];

	if(strlen($name)) {
		list($txt, $ext) = explode(".", strtolower($name));
		
		if(in_array($ext,$valid_formats)) {
				
			if($size<(1024*1024*3)) { // Image size max 3 MB

				if($width == 600 && $height == 600) {

					$actual_image_name = "logo.".$ext;
				
					if(move_uploaded_file($tmp_name, $path.$actual_image_name)) {
						$_SESSION['errorData']['Info'][] = "Logo successfully uploaded.";
						redirect($GLOBALS['BASEURL'].'admin/myNewTools.php');
					} else	{
						$_SESSION['errorData']['Error'][] = "Error uploading files, please try again.";
						redirect($GLOBALS['BASEURL'].'admin/myNewTools.php');
					}

				} else {
					$_SESSION['errorData']['Error'][] = "Image size must be <strong>600x600</strong> pixels.";
					redirect($GLOBALS['BASEURL'].'admin/myNewTools.php');				
				}
			
			} else {
				$_SESSION['errorData']['Error'][] = "The maximum allowed size for the image mus be <strong>3MB</strong>.";
				redirect($GLOBALS['BASEURL'].'admin/myNewTools.php');
			}
		
		} else {
			$_SESSION['errorData']['Error'][] = "Only <strong>PNG</strong> images allowed.";
			redirect($GLOBALS['BASEURL'].'admin/myNewTools.php');
		}
	} else {
		$_SESSION['errorData']['Error'][] = "Incorrect file name, please try again.";
		redirect($GLOBALS['BASEURL'].'admin/myNewTools.php');
	}

}else{
	redirect($GLOBALS['URL']);
}
