<?php

/**************************/
/*                        */
/*     UPLOAD A FILE      */
/*                        */
/**************************/

function getData_fromLocal() {

	// getting workind directory
	$dataDirPath = getAttr_fromGSFileId($_SESSION['User']['dataDir'],"path");
	$wd          = $dataDirPath."/uploads";
	
	//$dirTmp    = $GLOBALS['tmpDir']."/$dataDirPath/.tmp";

	if(empty($_FILES)){
		$_SESSION['errorData']['upload'][]="ERROR: Receiving blank. Please select a file to upload";
		die("ERROR: Recieving blank. Please select a file to upload0");
	}

	$wdP  = $GLOBALS['dataDir']."/".$wd;
	$wdId = getGSFileId_fromPath($wd);

	// check target directory
	if ( $wdId == "0" || !is_dir($wdP) ){
		$_SESSION['errorData']['upload'][]="Target server directory '".basename($wd)."' does not exist. Please, login again.";
		die("Target server directory '".basename($wd)."' does not exist. Please, login again.0");
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
				die("Unknown upload error0");
		}

		// check file size and space
					if (!$size || $size == 0 ){
			$_SESSION['errorData']['upload'][] = "ERROR: ".$_FILES['file']['name']." file size is zero";
			die("ERROR: ".$_FILES['file']['name']." file size is zero0");
					}
					if ( $size > return_bytes(ini_get('upload_max_filesize')) || $size > return_bytes(ini_get('post_max_size')) ){
			$_SESSION['errorData']['upload'][] = "ERROR: File size $size larger than UPLOAD_MAX_FILESIZE (".ini_get('upload_max_filesize').") ";
			die("ERROR: File size $size larger than UPLOAD_MAX_FILESIZE (".ini_get('upload_max_filesize').") 0");
		}

		$usedDisk     = (int)getUsedDiskSpace();
		$diskLimit    = (int)$_SESSION['User']['diskQuota'];

		//var_dump($size, (int)$_SESSION['User']['diskQuota'], $usedDisk);
		//die();


			if ($size > ($diskLimit-$usedDisk) ) {
					$_SESSION['errorData']['upload'][] = "ERROR: Cannot upload file. Not enough space left in the workspace";
			die("ERROR: Cannot upload file. Not enough space left in the workspace");
		}

			//do not overwrite, rename
			/*if (is_file($rfnNew)){
						foreach (range(1, 99) as $N) {
								$tmpNew= $rfnNew."_".$N;
								if (!is_file($tmpNew)){
										$rfnNew = $tmpNew;
										break;
								}
						}
		}*/

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
				die("Error occurred while registering the uploaded file0");
			}
					array_push($FNs,$fnId);

		}else{
							$_SESSION['errorData']['upload'][]="Uploaded file not correctly stored";
				die("Uploaded file not correctly stored0");
			}
	}


	print implode(",",$FNs);

}

/**************************/
/*                        */
/*     DATA FROM URL      */
/*                        */
/**************************/

function progress($resource,$download_size, $downloaded, $upload_size, $uploaded)
	{
		ob_get_clean();
		if($download_size > 0)
     	echo (number_format($downloaded / $download_size  * 100))."\n";
  	ob_flush();
		flush();
	}


function getData_fromURL($source, $ext = null) {

	// checking if remote file exists
	$ch = curl_init($source);

	curl_setopt($ch, CURLOPT_NOBODY, true);
	curl_exec($ch);
	$retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	//$ftp = curl_getinfo($ch, CURLINFO_FTP_ENTRY_PATH);

	curl_close($ch);

	if(($retcode != 200) && ($retcode != 350)) {
		//$_SESSION['errorData']['upload'][]="ERROR: Trying to upload an unexisting file. Please select a correct file.";
		die("ERROR: Trying to upload an unexisting file. Please select a correct file.");
	}

	// getting working directory
	$dataDirPath = getAttr_fromGSFileId($_SESSION['User']['dataDir'],"path");
	$wd          = $dataDirPath."/uploads";

	$wdP  = $GLOBALS['dataDir']."/".$wd;
	$wdId = getGSFileId_fromPath($wd);

	// check target directory
	if ( $wdId == "0" || !is_dir($wdP) ){
		//$_SESSION['errorData']['upload'][]="Target server directory '".basename($wd)."' does not exist. Please, login again.";
		die("ERROR: Target server directory '".basename($wd)."' does not exist. Please, login again.");
	}

	// downloading file
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $source);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, 'progress');
	curl_setopt($ch, CURLOPT_NOPROGRESS, false); // needed to make progress function work
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	$data = curl_exec ($ch);
	$error = curl_error($ch); 
	curl_close ($ch);

	// getting path and file size
	$nameFile = explode("/", $source);
	$rfnNew = "$wdP/".$nameFile[sizeof($nameFile) - 1];
	if(isset($ext)) $rfnNew .= '.'.$ext;
	$size   = strlen($data);

	if($size == 0) {
		//$_SESSION['errorData']['upload'][] = "ERROR: ".$nameFile." file size is zero";
		die("ERROR: ".$nameFile." file size is zero");
	}

	// checking if user disk has run out
	$usedDisk     = (int)getUsedDiskSpace();
	$diskLimit    = (int)$_SESSION['User']['diskQuota'];
			
	if ($size > ($diskLimit-$usedDisk) ) {
		//$_SESSION['errorData']['upload'][] = "ERROR: Cannot upload file. Not enough space left in the workspace";
		die("ERROR: Cannot upload file. Not enough space left in the workspace");
	}

	// checking if file name exists
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
	
	$file = fopen($rfnNew, "w+");

	fputs($file, $data);
	fclose($file);

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
			unlink($rfnNew);
			//$_SESSION['errorData']['upload']="Error occurred while registering the uploaded file";
			die("ERROR: Error occurred while registering the uploaded file.");
		}

		echo $fnId;

	}else{
		//$_SESSION['errorData']['upload'][]="Uploaded file not correctly stored";
		die("ERROR: Uploaded file not correctly stored.");
	}

}


/**************************/
/*                        */
/*      DATA FROM ID      */
/*                        */
/**************************/

function getSourceURL() {

	$input = $_REQUEST;

	$source = array();

	switch($input["databank"]) {

		case 'pdb': $source['url'] = "http://mmb.pcb.ub.es/api/pdb/".$input["idcode"];
								$source['ext'] = "pdb";
								break;

		default: die(0);

	}

	return $source;

}


/**************************/
/*                        */
/*      DATA FROM TXT     */
/*                        */
/**************************/

function getData_fromTXT() {

	$filename = $_REQUEST['filename'];
	$data = 	$_REQUEST['txtdata'];

	// getting working directory
	$dataDirPath = getAttr_fromGSFileId($_SESSION['User']['dataDir'],"path");
	$wd          = $dataDirPath."/uploads";

	$wdP  = $GLOBALS['dataDir']."/".$wd;
	$wdId = getGSFileId_fromPath($wd);

	// check target directory
	if ( $wdId == "0" || !is_dir($wdP) ){
		//$_SESSION['errorData']['upload'][]="Target server directory '".basename($wd)."' does not exist. Please, login again.";
		die("ERROR: Target server directory '".basename($wd)."' does not exist. Please, login again.");
	}

	// getting path and file size
	$rfnNew = "$wdP/".$filename;
	if(isset($ext)) $rfnNew .= '.'.$ext;
	$size   = strlen($data);

	if($size == 0) {
		//$_SESSION['errorData']['upload'][] = "ERROR: ".$nameFile." file size is zero";
		die("ERROR: ".$nameFile." file size is zero");
	}

	// checking if user disk has run out
	$usedDisk     = (int)getUsedDiskSpace();
	$diskLimit    = (int)$_SESSION['User']['diskQuota'];
			
	if ($size > ($diskLimit-$usedDisk) ) {
		//$_SESSION['errorData']['upload'][] = "ERROR: Cannot upload file. Not enough space left in the workspace";
		die("ERROR: Cannot upload file. Not enough space left in the workspace");
	}

	// checking if file name exists
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
	
	$file = fopen($rfnNew, "w+");

	fputs($file, $data);
	fclose($file);

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
			unlink($rfnNew);
			//$_SESSION['errorData']['upload']="Error occurred while registering the uploaded file";
			die("ERROR: Error occurred while registering the uploaded file.");
		}

		echo $fnId;

	}else{
		//$_SESSION['errorData']['upload'][]="Uploaded file not correctly stored";
		die("ERROR: Uploaded file not correctly stored.");
	}


}

/**************************/
/*                        */
/* FILETYPES / DATATYPES  */
/*                        */
/**************************/

function getFileTypesList() {

	$ft = $GLOBALS['fileTypesCol']->find()->sort(array('_id' => 1));

	return iterator_to_array($ft);

}

function getDataTypesList() {

	$dt = $GLOBALS['dataTypesCol']->find()->sort(array('_id' => 1));

	return iterator_to_array($dt);

}

function getDataTypeFromFileType($filetype) {

	$dt = $GLOBALS['dataTypesCol']->find(array('file_types' => array('$in' => array($filetype))))->sort(array('_id' => 1));

	return iterator_to_array($dt);

}

function getFeaturesFromDataType($datatype, $filetype) {

	$dt = $GLOBALS['dataTypesCol']->findOne(array('_id' => $datatype), array('assembly' => 1, 'taxon_id' => 1, 'paired' => 1, 'sorted' => 1, ));

	$res = array();

	$res["_id"] = $dt["_id"];
	$res["taxon_id"] = false;
	$res["assembly"] = false;
	$res["paired"] = false;
	$res["sorted"] = false;


	if($dt["taxon_id"]) $res["taxon_id"] = true;

	if (in_array($filetype,$dt["assembly"])) $res["assembly"] = true;

	if (isset($dt["paired"]) && in_array($filetype,$dt["paired"])) $res["paired"] = true;

	if (isset($dt["sorted"]) && in_array($filetype,$dt["sorted"])) $res["sorted"] = true;

	return $res;

}




?>
