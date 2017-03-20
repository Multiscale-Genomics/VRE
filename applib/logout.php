<?php

require "../phplib/genlibraries.php";

if($_POST){

	logoutUser();
	echo '1';

}else{
	redirect($GLOBALS['URL']);
}


?>

