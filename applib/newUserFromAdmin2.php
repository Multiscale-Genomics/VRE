<?php

require "../phplib/genlibraries.php";
require "../phplib/admin.inc.php";

if($_POST){
	//TODO check compulsory field
	if (!$_POST['Email'] || !$_POST['pass1']){
		$_SESSION['errorData']['Error'][] = "Email and password are compulsory fields";
		redirect($_SERVER['HTTP_REFERER']);
	}

	$u = checkUserLoginExists(sanitizeString($_POST["Email"]));
	if(!isSet($u)) {
		//TODO check Email is email

		// set password
		$newArray = $_POST;
		if (!$newArray['pass1']){
			$newArray['pass1'] = generatePassword();
			//$newArray['pass1'] = "mug_".$newArray['Surname'];
		}
		// diskQuota from GB to MB
		$newArray['diskQuota'] = $newArray['diskQuota']*1024*1024*1024;
		


		//create user
		$r = createUserFromAdmin($newArray);

		//return
		redirect($_SERVER['HTTP_REFERER']);
		//echo "1";

	}else{
		$_SESSION['errorData']['Error'][] = "User ".$_POST["Email"]." already exists";
		redirect($_SERVER['HTTP_REFERER']);
	}

}else{
	redirect($GLOBALS['URL']);
}

?>
