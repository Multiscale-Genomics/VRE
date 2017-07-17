<?php

require "../phplib/genlibraries.php";
require "../phplib/admin.inc.php";

var_dump($_REQUEST);

if($_REQUEST){
	$u = checkUserLoginExists(sanitizeString($_REQUEST["id"]));

	if(!isSet($u)) {
		//check current user privilegies # TODO
		
		//delete user
		$r = delUser($_REQUEST["id"]);
		echo $r;
	}else{
		echo "0";
	}
}else{
	redirect($GLOBALS['URL']);
}

var_dump($_SESSION['errorData']);
unset($_SESSION['errorData']);
?>
