<?php

require "../phplib/genlibraries.php";
require "../phplib/admin.inc.php";


if($_REQUEST){
	$u = checkUserIDExists(sanitizeString($_REQUEST["id"]));

	if(!isSet($u)) {
		$_SESSION['errorData']['Error'][] = "You are trying to remove a non existing user.";	
        redirect($GLOBALS['URL'].'admin/adminUsers.php');
    }

	//check current user privilegies # TODO
	if($u['Type'] == 0) {
		$_SESSION['errorData']['Error'][] = "You are trying to remove an admin user.";
		redirect($GLOBALS['URL'].'admin/adminUsers.php');
	}
		
	//delete user
    $r = delUser($_REQUEST["id"]);

    redirect($GLOBALS['URL'].'/admin/adminUsers.php');

}else{
	redirect($GLOBALS['URL']);
}
?>
