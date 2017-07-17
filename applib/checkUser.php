<?php

require "../phplib/genlibraries.php";

if($_POST){


$u = checkUserLoginExists(sanitizeString($_POST["usermail"]));

if(isSet($u)) {
	$up = loadUser(sanitizeString($_POST["usermail"]), sanitizeString($_POST["password"]));
	if($up){
		echo '1';
	}else{
		echo '2';
	}
}else{
	echo '3';
}

}else{
	redirect($GLOBALS['URL']);
}

?>
