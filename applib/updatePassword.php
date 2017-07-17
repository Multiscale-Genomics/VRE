<?php

require "../phplib/genlibraries.php";

if($_POST){

	$login = $_POST['usermail'];
	$login_hash = $_POST['q'];
	$pass = $_POST['pass1'];

	if(password_verify($login, $login_hash)){
	
		$user = $GLOBALS['usersCol']->findOne(array('_id' => $login));
				
		$newcrypPasswd = password_hash($pass, PASSWORD_DEFAULT);
		
		if ($user['_id']) {
			$newdata = array('$set' => array('crypPassword' => $newcrypPasswd));
			$GLOBALS['usersCol']->update(array('_id' => $login), $newdata);
			echo '1';
		}else{
			echo '2';
		}
	
	}else{
		echo '0';
	}

}else{
	redirect($GLOBALS['URL']);
}

?>
