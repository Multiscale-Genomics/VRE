<?php

require "../phplib/genlibraries.php";

if($_POST){

	$login = $_SESSION['User']['Email'];
	
	$user = $GLOBALS['usersCol']->findOne(array('_id' => $login));
		
	if ($user['_id']) {
		$newdata = array('$set' => array('Surname' => ucfirst($_POST['Surname']), 'Name' => ucfirst($_POST['Name']), 'Inst' => $_POST['Inst'], 'Country' => $_POST['Country']));
		$GLOBALS['usersCol']->update(array('_id' => $login), $newdata);
		$_SESSION['User']['Name'] = ucfirst($_POST['Name']);
		$_SESSION['User']['Surname'] = ucfirst($_POST['Surname']);
		$_SESSION['User']['Country'] = $_POST['Country'];
		$_SESSION['User']['Inst'] = $_POST['Inst'];
		echo '1';
	}else{
		echo '0';
	}

}else{
	redirect($GLOBALS['URL']);
}

?>
