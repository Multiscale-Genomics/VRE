<?php

require "../phplib/genlibraries.php";

redirectOutside();

if($_POST){

	$dt = getDataTypeFromFileType($_REQUEST['filetype']);

	echo json_encode($dt);

}else{
	redirect($GLOBALS['URL']);
}


