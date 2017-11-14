<?php

require "../phplib/genlibraries.php";

redirectOutside();

if($_POST){

	$dt = getFeaturesFromDataType($_REQUEST['datatype'], $_REQUEST['filetype']);

	echo json_encode($dt);

}else{
	redirect($GLOBALS['URL']);
}


