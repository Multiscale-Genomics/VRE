<?php

require "../phplib/genlibraries.php";

if($_POST){

	$login = $_POST['id'];
	
	$user = $GLOBALS['usersCol']->findOne(array('_id' => $login));
		
	if ($user['_id']) {
		$newdata = array('$set' => array('Surname' => ucfirst($_POST['surname']), 'Name' => ucfirst($_POST['name']), 'Inst' => $_POST['inst'], 'Country' => $_POST['country'], 'diskQuota' => $_POST['disk'], 'Type' => $_POST['type']));
		$GLOBALS['usersCol']->update(array('_id' => $login), $newdata);
		echo '1';
	}else{
		echo '0';
	}

}else{
	redirect($GLOBALS['URL']);
}

?>
