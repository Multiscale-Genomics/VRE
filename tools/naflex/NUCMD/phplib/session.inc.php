<?php
session_name("FlexPortalSession");

session_start();
require_once "phplib/global.inc.php";
require_once "phplib/users.inc.php";
#require_once "phplib/check.inc.php";
require_once "phplib/User.php";
require_once "phplib/AnonUser.php";
require_once "pdbconn.inc.php";

$fileSession = uniqId('NAFlex');
#if(!$_SESSION['BNSId'])
if(!$_SESSION['User']) 
	#$_SESSION['BNSId'] = $fileSession;
	createAnonUser();
?>
