<?php

/*
 * gesUser.php
 * 
 */
require_once "phplib/globals.inc.php";
require_once "phplib/users.inc.php";
require_once "phplib/check.inc.php";

switch ($_REQUEST['op']) {
    //Login ====================================================================
    case 'loginForm':
        if (!isset($_SESSION['inputData']) and checkLoggedIn())
            $_SESSION['inputData'] = (Array) $_SESSION['User'];
        $_SESSION['inputData']['oblig'] = 'login#password';
        $dataFormTemplate = 'userLoginForm.inc.htm';
        break;
    case 'login':
        $_SESSION['inputData'] = $_REQUEST;
        if (checkLoginErrors($_SESSION['inputData'])){
	    //we have errors
	    $dataFormTemplate = 'userLoginForm.inc.htm';
	    break;	
//          redirect('gesUser.php?op=loginForm');
	}
	var_dump($_SESSION['Ftp']);
        $_SESSION['User']->lastLogin = moment();
        prepUserWorkSpace();
        redirect("workspace.php");
        break;
    // Lost password management ================================================
    case 'lostPassForm' :
        $_SESSION['inputData']['oblig'] = 'login';
        $dataFormTemplate = 'lostPassForm.inc.htm';
        break;
    case 'lostPass' :
        //TODO
        //send Email, etc. 
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
        redirect("workspace.php");
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
        redirect("workspace.php");
        break;
}
if ($_REQUEST['op'] != "loginForm") 
	print headerTP('');

if (isset($_SESSION['errorData'])){
	print printErrorData();
}
print getFormTemplate(getTemplate($dataFormTemplate), $_SESSION['inputData'], $_SESSION['errorData'], True);

if ($_REQUEST['op'] != "loginForm")
	print footerTP();	
