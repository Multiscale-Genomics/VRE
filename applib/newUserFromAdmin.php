<?php

require "../phplib/genlibraries.php";
require "../phplib/admin.inc.php";

if($_POST){

	$u = checkUserLoginExists(sanitizeString($_POST["Email"]));

	if(!isSet($u)) {
		$newArray = $_POST;
		$newArray['pass1'] = generatePassword();
		createUserFromAdmin($newArray);
		echo "1";
	}else{
		echo "0";
	}

}else{
	redirect($GLOBALS['URL']);
}

?>
