<?php

require "../phplib/genlibraries.php";

//die("login.php!!!!");


if($_POST){

$u = checkUserLoginExists(sanitizeString($_POST["usermail"]));

if(isSet($u)) {
	$up = loadUser(sanitizeString($_POST["usermail"]), sanitizeString($_POST["password"]));
	if($up){
		redirect("../home/redirect.php");
	}else{
		redirect($GLOBALS['URL']);
	}
}else{
	 redirect($GLOBALS['URL']);
}

}else{
	redirect($GLOBALS['URL']);
}

?>

