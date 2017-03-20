<?php

require "../phplib/genlibraries.php";

if($_POST){

$u = checkUserLoginExists(sanitizeString($_POST["Email"]));

if(!isSet($u)) {
	if($_POST['Type'] == 1){
		$_POST['Type'] = 100;
		requestPremiumUser(sanitizeString($_POST['Email']), sanitizeString($_POST['Name']), sanitizeString($_POST['Surname']));
	}
	createUser($_POST);
	echo "1";
}else{
	echo "0";
}

}else{
	redirect($GLOBALS['URL']);
}

?>
