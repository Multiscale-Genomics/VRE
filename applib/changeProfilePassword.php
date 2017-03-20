<?php

require "../phplib/genlibraries.php";

if($_POST){

	$login = $_SESSION['User']['Email'];
	$oldpass = $_POST['oldpass'];
	$pass1 = $_POST['pass1'];
	$pass2 = $_POST['pass2'];

	$newcrypPasswd = password_hash($pass1, PASSWORD_DEFAULT);

	$user = $GLOBALS['usersCol']->findOne(array('_id' => $login));
	if ($user['_id'] && password_verify($oldpass, $user['crypPassword'])) {
		$newdata = array('$set' => array('crypPassword' => $newcrypPasswd));
		$GLOBALS['usersCol']->update(array('_id' => $login), $newdata);
		echo '1';
	}
	else{
		echo '0';
	}

}else{
	redirect($GLOBALS['URL']);
}

?>
