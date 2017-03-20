<?php

/*
 * users.inc.php
 * 
 */

#use BIGNASim\User;
#use BIGNASim\AnonUser;

require_once "phplib/global.inc.php";

function newUser($f) {
	$user = Array();
        foreach (array('Surname','Name','Inst','Country','Email','Uploader') as $k)
            $user[$k]=$f[$k];
        $user['_id'] = $user['Email'];
        $user['crypPassword'] = crypt($f['pass1'],PASSWORD_SALT);
        $user['lastLogin'] = moment();
        $user['id'] = uniqid($GLOBALS['AppPrefix'] . "USER");
        return $user;
}

function checkLoggedIn() {
    return (isset($_SESSION['User']));
}

function checkRegErrors($f, $checkLogin = True) {
    unset($_SESSION['errorData']);
    $errorCount = checkOblig($_SESSION['errorData'], $f, $f['oblig']);
    if ($_REQUEST['Country'] == 'XX') {
        $errorCount++;
        $_SESSION['errorData']['Country'][] = 'oblig';
    }
    if ($checkLogin) {
        if (checkUserLoginExists($f['Email'])) {
            $errorCount++;
            $_SESSION['errorData']['Email'][] = 'oldLogin';
        }
    }
    if ($f['pass1'] and ($f['pass1'] != $f['pass2'])) {
        $errorCount++;
        $_SESSION['errorData']['pass1'][] = 'passNoMatch';
    }
    return $errorCount;
}

function checkLoginErrors($f) {
    unset($_SESSION['errorData']);
    $errorCount = checkOblig($_SESSION['errorData'], $f, $f['oblig']);
    if (!$errorCount) {
        $userObj = loadUser($f['login'], $f['password']);
        if (!$userObj) {
            $errorCount++;
            $_SESSION['errorData']['login'][] = 'userNotFound';
        }
        else
            $_SESSION['User'] = (array)$userObj;
    }
    return $errorCount;
}

function createUser(&$f) {
    $objUser = new User($f, True);
    $_SESSION['User'] = (array)$objUser;
    saveNewUser($_SESSION['User']);
}

function createAnonUser() {
    $objUser = new AnonUser();
    $_SESSION['User'] = (array)$objUser;
    saveNewUser($_SESSION['User']);
}

function updateUser(&$f) {
    $_SESSION['User']->update($f);
    saveUser($_SESSION['User']);
}

function logoutUser() {
    unset($_SESSION['User']);
}

function saveUser($user) {
    $GLOBALS['usersCol']->update(array('_id' => $user->_id), $user, array('upsert=>1'));
}

function saveNewUser($userObj) {
    return $GLOBALS['usersCol']->insert($userObj);
}

function checkUserLoginExists($login) {
    if ($login)
        $user = $GLOBALS['usersCol']->findOne(array('_id' => $login));
    return ($user);
}

function loadUser($login, $pass) {
#print $pass;
#print PASSWORD_SALT;
    $crpass = crypt($pass, PASSWORD_SALT);
#print $crpass;
    $user = $GLOBALS['usersCol']->findOne(array('_id' => $login, 'crypPassword' => $crpass));
    if (!$user['_id'])
        return False;
    #$userObj = new User($user);
    #return $userObj;
    return $user;
}

function userMenu() {
    if (checkLoggedIn())
        if ($_SESSION['User']->Anon)
            return "<p><b>User: " . $_SESSION['User']->fullName() . "</b> [<a href=\"workspace.php?op=close\">Close Workspace</a>]</p>";
        else
            return "<p><b>User: " . $_SESSION['User']->fullName(True, True) . "</b> [<a href=\"gesUser.php?op=MyAccountForm\">My Account</a>] [<a href=\"workspace.php?op=close\">Close Workspace</a>]</p>";
    else
        return '';
}

function passwdForgot() {

	$login = $_SESSION['inputData']['login'];

	# Generating Temporary Password
	$newPasswd = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789') , 0 , 10 );
	$newpass = crypt($newPasswd, PASSWORD_SALT);
	#execSql ("UPDATE users set passwd = '$newpass' where login = '$login'");

	$newdata = array('$set' => array('crypPassword' => $newpass));
	$GLOBALS['usersCol']->update(array('_id' => $login), $newdata);

	$subject = "BIGNASim Request new Password";
	$message = ' 
	This is an automatic email from BIGNASim server '. $GLOBALS["homeNUCMD_FULL"]. '

	Your temporary password for user '. $login .' is: 

	'.$newPasswd.'

	Please, use this password to change your old one in this link:

	'. $GLOBALS["homeNUCMD_FULL"].'gesUser.php?op=newPasswd

	Thank you for using BIGNASim.';

	$from = "adam.hospital@irbbarcelona.org";

	mail($login,$subject,$message,"From: $from\n");

}

function changePasswd() {

	$login = $_SESSION['inputData']['Email'];
	$oldpass = $_SESSION['inputData']['oldpass'];
	$pass1 = $_SESSION['inputData']['pass1'];
	$pass2 = $_SESSION['inputData']['pass2'];

	$oldcrypPasswd = crypt($oldpass,PASSWORD_SALT);
	$newcrypPasswd = crypt($pass1,PASSWORD_SALT);

	$user = $GLOBALS['usersCol']->findOne(array('_id' => $login, 'crypPassword' => $oldcrypPasswd));
	if ($user['_id']) {
		$newdata = array('$set' => array('crypPassword' => $newcrypPasswd));
		$GLOBALS['usersCol']->update(array('_id' => $login), $newdata);
		return 1;
	}
	else
		return 0;
}

?>
