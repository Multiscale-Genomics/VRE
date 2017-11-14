<?php

require "../phplib/genlibraries.php";

//if($_POST){
if($_REQUEST['id'] == 1){

    //end php session
    logoutUser();

    //end oauth2 session
    $logoutUrl = $GLOBALS['urlLogout']."?redirect_uri=".urlencode("http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?id=2");
    header('Location: ' . $logoutUrl);
    exit(0);

}elseif($_GET['id'] == 2){
	echo '1';

}else{
	redirect($GLOBALS['URL']);
}
