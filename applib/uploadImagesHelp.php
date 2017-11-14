<?php

require "../phplib/genlibraries.php";

$uploadedFiles = array();

if (! empty($_FILES)) {
	foreach ($_FILES as $file) {

		$uploadOk = 1;
		$imageFileType = pathinfo($file["name"], PATHINFO_EXTENSION);	

		// Check is image
		$check = getimagesize($file["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        $uploadOk = 0;
    }

		// Check file size
		if ($file["size"] > 5000000) {
				$uploadOk = 0;
		}

		// Allowed file formats
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
		&& $imageFileType != "gif" ) {
				$uploadOk = 0;
		}

		// If previous requirements ok, try to copy the file
    if ($uploadOk == 0) {
				$uploadedFiles = array();
				break;
		} else {
			$filename = uniqid("", true) . ".". pathinfo($file["name"], PATHINFO_EXTENSION);
			$target_file = $GLOBALS['htmlPath'].'tools/'.$_REQUEST['tool'].'/help/img/' . $filename ;
			if (move_uploaded_file($file["tmp_name"], $target_file)) {
				$uploadedFiles[] = '/tools/'.$_REQUEST['tool'].'/help/img/' . urlencode($filename);
			}else{
				$uploadedFiles = array();
				break;
			}
		}
  }
}

echo json_encode($uploadedFiles);
