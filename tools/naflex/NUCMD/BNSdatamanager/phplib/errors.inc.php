<?php

/*
 * Error messages
 */

loadErrorsDB();
#
function loadErrorsDB() {
    $err = array();
//    if ($tag)
//        $c = $GLOBALS['errorsCol']->find(array('tags' => $tag));
//    else
//        $c = $GLOBALS['errorsCol']->find();
//    foreach ($c as $doc) {
//        if ($idioma)
//            foreach (array('ca', 'cs', 'en') as $idi)
//                $err[$idi][$doc['_id']] = $doc[$idi];
//        else
//            $err[$doc['_id']] = $doc;
//    }
    $GLOBALS['errors'] = $err;
}

function errorText($idErr) {
    return $GLOBALS['errors'][$idErr]['errorText'];
}

function nonExistReq($field) {
    return !$_REQUEST[$field];
}

function nonExistUploadFile($field) {
    return !$_FILES[$field]['size'];
}

function fileSizeExceeded($file) {
    $limitFileSize = $GLOBALS['limitFileSize']; // 100MB = 100000KB
    //$limitFileSize = 10000; // 10MB = 10000KB
    $size = $file['size'];
    $sizeK = $size;
    if ($size > 0)
        $sizeK = $size / 1024;
    logger("File Size: $sizeK KB");
    if ($sizeK > $limitFileSize) {
        logger("File Size Exceeded: $sizeK KB > $limitFileSize KB");
        return true;
    }
    else
        return false;
}

function emptyFile($file) {
    $size = $file['size'];
    $f = $file['tmp_dir'];
    if ($size > 0) {
        logger("Empty File: $f");
        return false;
    }
    else
        return true;
}
function printErrorData(){
	$txt = "<div class=\"notify\"><img src=\"BNSdatamanager/images/delete.png\" align=\"left\" style=\"height:25px;\">";

	foreach ($_SESSION['errorData'] as $seccion =>$arrErr){
		$txt .="<b>$seccion</b><p>";
		if (is_array($arrErr)){
		    foreach ($arrErr as $textErr){
			$txt .="$textErr <br/>";
		    }
		}else{
		    $txt .="$arrErr <br/>";
		}
	}
	$txt .= "</p></div>";
	unset($_SESSION['errorData']);	
	return $txt;
}
