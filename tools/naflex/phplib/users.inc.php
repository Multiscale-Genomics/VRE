<?php
/*  MDWeb
* users.inc.php
* Library for user management
*/
function saveUser () {
    //CF fixed to avoid SQL injection
    $surname = mysql_real_escape_string($_SESSION['inputData']['surname']);
    $name    = mysql_real_escape_string($_SESSION['inputData']['name']);
    $inst    = mysql_real_escape_string($_SESSION['inputData']['inst']);
    $country = mysql_real_escape_string($_SESSION['inputData']['country']);
    $login   = mysql_real_escape_string($_SESSION['inputData']['login']);
    $pass1   = mysql_real_escape_string($_SESSION['inputData']['pass1']);
    $email   = mysql_real_escape_string($_SESSION['inputData']['email']);
    $community   = mysql_real_escape_string($_SESSION['inputData']['community']);
    $caduca  = mysql_real_escape_string($GLOBALS['caduca']);
	
    //CF salt and md5 the password
    $newpass = crypt($pass1, PASSWORD_SALT);
    
	execSql ("INSERT INTO users (surname,name,inst,country,login,passwd, email, caduca, lastlogin, community) values (
    '".$surname."',
    '".$name."',
    '".$inst."',
    '".$country."',
    '".$login."',
    '".$newpass."',
    '".$email."',
    '".$caduca."',
    '".moment()."',
    '".$community."')");
    addLog($login, "no project", "Added new user: ".$login);
}

function getUserEnvironment($login) {
	//CF sanitized variables and passwords
	$login = mysql_real_escape_string($login);
logger("Login: $login");	
	$userData = getRecord('users','login',$login,'T');
logger("UserData: $userData");
	if ($userData) {
           $userData['workDir'] = $GLOBALS['baseDir']."/$login";
        } else if (substr($login,0,10) == 'NAFlexUser') {
            $userData = array("workDir" =>  $GLOBALS['baseDir']."/$login");
        }
	return $userData;
}

function prepUserSpace($login) {
	mkdir ($GLOBALS['baseDir']."/$login");
}
?>
