<?php

require "../phplib/genlibraries.php";

redirectOutside();

$dataDirPath = getAttr_fromGSFileId($_SESSION['User']['dataDir'],"path");
$wd          = $dataDirPath."/uploads";


if(empty($_FILES)){
	$_SESSION['errorData']['upload'][]="ERROR: Recieving blank. Please select a file to upload";
	die("0");
}

$wdP  = $GLOBALS['dataDir']."/".$wd;
$wdId = getGSFileId_fromPath($wd);


// check target directory
if ( $wdId == "0" || !is_dir($wdP) ){
	$_SESSION['errorData']['upload'][]="Target server directory '".basename($wd)."' does not exist. Please, login again.";
	die("0");
}

$FNs=Array();


$resp=0;
	
for ($i = 0; $i < count($_FILES['file']['tmp_name']); ++$i) {

	$rfnNew = "$wdP/".$_FILES['file']['name']; // $_FILES['file']['name'][$i
	$size   = $_FILES['file']['size']; // $_FILES['file'][$i]['size'][$i];

	// check upload errors
	if ($_FILES['file']['error'] ) { //if ($_FILES['file']['error'][$i] ) {
            $errMsg = array(
                0=>"[UPLOAD_ERR_OK]:  There is no error, the file uploaded with success",
                1=>"[UPLOAD_ERR_INI_SIZE]: The uploaded file exceeds the upload_max_filesize directive in php.ini",
                2=>"[UPLOAD_ERR_FORM_SIZE]: The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form",
                3=>"[UPLOAD_ERR_PARTIAL]: The uploaded file was only partially uploaded",
                4=>"[UPLOAD_ERR_NO_FILE]: No file was uploaded",
                6=>"[UPLOAD_ERR_NO_TMP_DIR]: Missing a temporary folder",
                7=>"[UPLOAD_ERR_CANT_WRITE]: Failed to write file to disk",
                8=>"[UPLOAD_ERR_EXTENSION]: File upload stopped by extension"
	    );
	    if(isset($errMsg[$code])){
		$_SESSION['errorData']['upload'][] = "ERROR [code $code] ".$errMsg[$code];
            }else{
                $_SESSION['errorData']['upload'][] = "Unknown upload error";
            }
	    die("0");
	}

	// check file size and space
        if (!$size || $size == 0 ){
		$_SESSION['errorData']['upload'][] = "ERROR: ".$_FILES['file']['name']." file size is zero";
		die("0");
        }
        if ( $size > return_bytes(ini_get('upload_max_filesize')) || $size > return_bytes(ini_get('post_max_size')) ){
		$_SESSION['errorData']['upload'][] = "ERROR: File size $size larger than UPLOAD_MAX_FILESIZE (".ini_get('upload_max_filesize').") ";
		die("0");
	}

	$usedDisk     = getUsedDiskSpace();
	$diskLimit    = $_SESSION['User']['diskQuota'];

  	if ($size > ($disklimit-$usedDisk) ) {
        $_SESSION['errorData']['upload'][] = "ERROR: Cannot upload file. Not enough space left in the workspace";
		die("0");
	}

	// GENIS: change renaming function
	if (is_file($rfnNew)){
		foreach (range(1, 99) as $N) {
			if ($pos = strrpos($rfnNew, '.')) {
				$name = substr($rfnNew, 0, $pos);
				$ext = substr($rfnNew, $pos);
			} else {
				$name = $rfnNew;
			}
			$tmpNew= $name .'_'. $N . $ext;
			if (!is_file($tmpNew)){
				$rfnNew = $tmpNew;
				break;
			}
		}
	}


	//upload
    if ( $_FILES['file']['tmp_name'] ){ //  $_FILES['file']['tmp_name'][$i]
		$resp = move_uploaded_file($_FILES['file']['tmp_name'], $rfnNew); // $_FILES['file']['tmp_name'][$i]
	}

    if (is_file($rfnNew)){
                chmod($rfnNew, 0666);
                $fnNew = basename($rfnNew);

                $insertData=array(
                    'owner' => $_SESSION['User']['id'],
                    'size'  => filesize($rfnNew),
                    'mtime' => new MongoDate(filemtime($rfnNew))
                );
		$metaData=array(
		    'validated' => FALSE
		);
	

        $fnId = uploadGSFileBNS("$wd/$fnNew", $rfnNew, $insertData,$metaData,FALSE);

		if ($fnId == "0"){
			$_SESSION['errorData']['upload']="Error occurred while registering the uploaded file";
			die("0");
		}
        array_push($FNs,$fnId);

	}else{
            $_SESSION['errorData']['upload'][]="Uploaded file not correctly stored";
			die("0");
    }
}


print implode(",",$FNs);

?>
