<?php

require "../phplib/genlibraries.php";

if($_POST){

	$login = $_POST['id'];
	$type = $_POST['t'];
	$oldtype = $_POST['ot'];

	$user = $GLOBALS['usersCol']->findOne(array('_id' => $login));
	if ($user['_id']) {
		$newdata = array('$set' => array('Type' => $type));
		$GLOBALS['usersCol']->update(array('_id' => $login), $newdata);

		if(($oldtype == 100) && (($type == 101) || ($type == 1))){

			answerPremium($login, $user['Name'], $user['Surname'], $type);
		
		}

		echo '1';
	}else{
		echo '0';
	}		

}else{
	redirect($GLOBALS['URL']);
}

?>
