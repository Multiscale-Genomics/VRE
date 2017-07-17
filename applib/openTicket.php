<?php

require "../phplib/genlibraries.php";
require "../phplib/admin.inc.php";


$message = '
	User name: '.$_REQUEST["Name"].'<br>
	User email: '.$_REQUEST["Email"].'<br>
	Request type: '.$_REQUEST["Request"].'<br>
	Request subject: '.$_REQUEST["Subject"].'<br>
	Request message: '.$_REQUEST["Message"];

if(sendEmail($GLOBALS['helpdeskMail'], $_REQUEST["Request"]." - ".$_REQUEST["Subject"], $message)) {

	$_SESSION['errorData']['Info'][] = "Ticket successfully open, you will receive a response soon.";
	redirect($_SERVER['HTTP_REFERER']);

} else {

	$_SESSION['errorData']['Error'][] = "Error opening ticket, please try again later.";
	redirect($_SERVER['HTTP_REFERER']);

}	
	

