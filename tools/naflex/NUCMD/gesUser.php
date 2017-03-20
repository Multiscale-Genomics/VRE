<?php

/*
 * gesUser.php
 * 
 */
require_once "phplib/global.inc.php";
require_once "phplib/users.inc.php";
require_once "phplib/check.inc.php";
require_once "phplib/User.php";
#require_once "UserClass.php";


switch ($_REQUEST['op']) {
    //Login ====================================================================
    case 'loginForm':
        if (!isset($_SESSION['inputData']) and checkLoggedIn())
            $_SESSION['inputData'] = (Array) $_SESSION['User'];
        $_SESSION['inputData']['oblig'] = 'login#password';
        $dataFormTemplate = 'userLoginForm.inc.htm';
        break;
    case 'logout':
	logoutUser();
	redirect("index.php");
	break;
    case 'login':
        $_SESSION['inputData'] = $_REQUEST;
        if (checkLoginErrors($_SESSION['inputData']))
            redirect('gesUser.php?op=loginForm');
        $_SESSION['User']['lastLogin'] = moment();
        saveUser($_SESSION['User']);
	$id = $_SESSION['User']['id'];
        redirect("$GLOBALS[homeNUCMD_FULL]/BNSdatamanager/workspace.php?BNSId=$id");
        break;
    // Lost password management ================================================
    case 'lostPassForm' :
        $_SESSION['inputData']['oblig'] = 'login';
        $dataFormTemplate = 'lostPassForm.inc.htm';
        break;
    case 'lostPass' :
        //TODO
        //send Email, etc. 
	$_SESSION['inputData'] = $_REQUEST;
	passwdForgot();
        $dataFormTemplate = 'lostPassEmail.inc.htm';
        break;
    case 'newPasswd' :
        $_SESSION['inputData']['oblig'] = 'login#oldpass#pass1#pass2';
        $dataFormTemplate = 'newPassForm.inc.htm';
	break;
    case 'newPass' :
	$_SESSION['inputData'] = $_REQUEST;
	if ( changePasswd() )
		$dataFormTemplate = 'newPassOK.inc.htm';
	else
		$dataFormTemplate = 'newPassFailed.inc.htm';
	break;
    //Register New User ========================================================
    case 'registerForm':
        if (!isset($_SESSION['inputData']) and checkLoggedIn())
            $_SESSION['inputData'] = (Array) $_SESSION['User'];
        $_SESSION['inputData']['oblig'] = 'Surname#Name#Country#Email#pass1';
        $_SESSION['inputData']['submitVal'] = "Register";
        $_SESSION['inputData']['actionVal'] = "gesUser.php?op=register";
        $dataFormTemplate = 'userDataForm.inc.htm';
        break;
    case 'register':
        $_SESSION['inputData'] = $_REQUEST;
        if (checkRegErrors($_SESSION['inputData']))
            redirect('gesUser.php?op=registerForm');
        createUser($_SESSION['inputData']);
	$id = $_SESSION['User']['id'];
        redirect("$GLOBALS[homeNUCMD_FULL]/BNSdatamanager/workspace.php?BNSId=$id");
        break;
    //Update User Data =========================================================
    case 'MyAccountForm' :
        $_SESSION['inputData'] = (Array) $_SESSION['User'];
        $_SESSION['inputData']['oblig'] = 'Surname#Name#Country#Email';
        $_SESSION['inputData']['submitVal'] = "Update";
        $_SESSION['inputData']['actionVal'] = "gesUser.php?op=MyAccount";
        $dataFormTemplate = 'userDataForm.inc.htm';
        break;
    case 'MyAccount':
        $_SESSION['inputData'] = $_REQUEST;
        if (checkRegErrors($_SESSION['inputData'], False))
            redirect('gesUser.php?op=MyAccountForm');
        updateUser($_SESSION['inputData']);
        redirect("BNSdatamanager/workspace.php");
        break;
}
print headerMMB("BIGNASim database structure and analysis portal for nucleic acids simulation data");
print "<div class='usersMgSection'>\n";
print getFormTemplate(
                getTemplate($dataFormTemplate), $_SESSION['inputData'], $_SESSION['errorData'], True);
print "</div>\n";
print footerMMB();
