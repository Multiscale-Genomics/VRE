<?php

require "../phplib/admin.inc.php";
require "../phplib/genlibraries.php";

if($_POST){

	$login = $_POST['emailf'];

	$user = $GLOBALS['usersCol']->findOne(array('_id' => $login));
	if ($user['_id'] && ($user['Status'] == 1)) {

		$hash = password_hash($user['_id'], PASSWORD_DEFAULT);

		$resp = requestNewPassword($login, $user['Name'], $user['Surname'], $hash);

		echo $resp;

	}
	else{
		echo "3";
	}

}else{
	redirect($GLOBALS['URL']);
}

?>
