<?php

require "../phplib/genlibraries.php";
require "../phplib/admin.inc.php";

switch($_REQUEST["Request"]) {
	case 'general': $req = "Technical question";
									break;
	case 'tools': $req = "Issue related with tools";
								break;
}

$tool_name = '';

if(isset($_REQUEST['Tool'])) {
	$toolProp = $GLOBALS['toolsCol']->findOne(array('_id' => $_REQUEST['Tool']));
	$toolContact = $toolProp["owner"]["contact"];
	$tool_name = ' - '.$toolProp["name"];
}

$message = '
	User name: '.$_REQUEST["Name"].'<br>
	User email: '.$_REQUEST["Email"].'<br>
	Request type: '.$req.$tool_name.'<br>
	Request subject: '.$_REQUEST["Subject"].'<br>
	Request message: '.$_REQUEST["Message"];

if(sendEmail($GLOBALS['ADMINMAIL'], $req." - ".$_REQUEST["Subject"], $message, $_REQUEST["Email"], $toolContact)) {

	$_SESSION['errorData']['Info'][] = "Ticket successfully open, you will receive a response soon.";
	redirect($_SERVER['HTTP_REFERER']);

} else {

	$_SESSION['errorData']['Error'][] = "Error opening ticket, please try again later.";
	redirect($_SERVER['HTTP_REFERER']);

}	
	

