<?php

require "../phplib/genlibraries.php";


if($_POST){
    resetPasswordViaKeycloak($_SESSION['User']['_id']);
    die();
    echo 1;

}else{
	redirect($GLOBALS['URL']);
}

?>
