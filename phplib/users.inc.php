<?php

/*
 * users.inc.php
 * 
 */

require "User.php";


function checkLoggedIn() {

	$user = $GLOBALS['usersCol']->findOne(array('_id' => $_SESSION['User']['_id']));
	
	if(isset($_SESSION['User']) && ($user['Status'] == 1)) return true;
	else return false;
}

function checkAdmin() {

	$user = $GLOBALS['usersCol']->findOne(array('_id' => $_SESSION['User']['_id']));
	
	if(isset($_SESSION['User']) && ($user['Status'] == 1) && (allowedRoles($user['Type'], $GLOBALS['ADMIN']))) return true;
	else return false;
}

function createUser(&$f) {
   	$objUser = new User($f, True);
	$aux = (array)$objUser;

	$_SESSION['userId'] = $aux['id']; //OBSOLETE
	$_SESSION['User'] = $aux;
	unset($_SESSION['crypPassword']);

	$dataDirId =  prepUserWorkSpace($aux['id']);
	if ($dataDirId){
		$aux['dataDir']= $dataDirId;
    	saveNewUser($aux);
		$_SESSION['User']['dataDir'] = $dataDirId;
		sendWelcomeToNewUser($aux['_id'], $aux['Name'], $aux['Surname']);
	}else{ echo 'error creating data dir!'; }
}

function createUserFromAdmin(&$f) {
    $objUser = new User($f, True);
    $aux = (array)$objUser;

    $_SESSION['userId'] = $aux['id'];
    $_SESSION['User'] = $aux;
    unset($_SESSION['crypPassword']);

    $dataDirId =  prepUserWorkSpace($aux['id']);
    if ($dataDirId){
		$aux['dataDir']= $dataDirId;
		saveNewUser($aux);
		$_SESSION['User']['dataDir'] = $dataDirId;
    	sendPasswordToNewUser($aux['_id'], $aux['Name'], $aux['Surname'], $f['pass1']);
    }else{ echo 'error creating data dir!'; }
}

function updateUser($f,$all) {
	$aux = (array)$f;
	unset($aux['crypPassword']);
	unset($aux['lastLogin']);
	$_SESSION['User'] = $aux;
	$_SESSION['curDir'] = $_SESSION['User']['id'];
	$GLOBALS['usersCol']->update(array('_id' => $f['_id']), $f, array('upsert=>1'));
	if(!isset($_SESSION['lastUserLogin'])) $_SESSION['lastUserLogin'] = $all;
}

function logoutUser() {
	session_unset();
//	unset($_SESSION['User']);
//	unset($_SESSION['lastUserLogin']);
}

function saveNewUser($userObj) {
    return $GLOBALS['usersCol']->insert($userObj);
}

function checkUserLoginExists($login) {
	//die("check user login exists");
    if ($login)
        $user = $GLOBALS['usersCol']->findOne(array('_id' => $login));
    return ($user);
}


function loadUser($login, $pass) {
    //$crpass = crypt($pass, PASSWORD_SALT);
    $user = $GLOBALS['usersCol']->findOne(array('_id' => $login));
    if (!$user['_id'] || !password_verify($pass, $user['crypPassword']) || ($user['Status'] == 0)) {
		return False;
	}else{
		$auxlastlog = $user['lastLogin'];
		$user['lastLogin'] = moment();
		updateUser($user, $auxlastlog);
	}
    return $user;
}

function allowedRoles($role, $allowed){
	
	if(in_array($role,$allowed)){
		return true;
	}else{
		return false;
	}

}

function getUser_diskQuota($login) {
    $r = $GLOBALS['usersCol']->findOne(array('_id'  => $login,
                                         'diskQuota'=> array('$exists' => true)
                                   ));
    if (isset($r['diskQuota']))
        return $r['diskQuota'];
    else
        return false;
}

function saveUserJobs($login,$jobInfo) {
    $GLOBALS['usersCol']->update(array('_id' => $login),
                                 array('$set'   => array('lastjobs' => $jobInfo)),
                                 array('upsert' => 1));
}

function delUserJob($login,$pid) {
    $GLOBALS['usersCol']->update(array('_id' => $login),
                                 array('$unset' => array("lastjobs.$pid" => 1 ))
                                );
                                 //array('$pull' => array("lastjobs" => $pid ))
                                //multi
}

function addUserJob($login,$data,$pid) {

    $pid = strval($pid);
    $lastjobs = getUserJobs($login);

    $lastjobs[$pid] = $data;

    $GLOBALS['usersCol']->update(array('_id' => $login),
                                 //array('$set'  => array("lastjobs.$pid" => $data )),
                                 array('$set'   => array('lastjobs' => $lastjobs)),
                                array('upsert' => 1)
                                );
}

function getUserJobs($login) {
    $r = $GLOBALS['usersCol']->findOne(array('_id'  => $login,
                                             'lastjobs'=> array('$exists' => true)
                                            ));
    if (isset($r['lastjobs']))
        return $r['lastjobs'];
    else
        return Array();
}

function getUserJobPid($login,$pid) {
    $r = $GLOBALS['usersCol']->findOne(array("_id"      => $login,
                                             "lastjobs._id"=> $pid
                                            ));
    if (isset($r['lastjobs']))
        return $r['lastjobs'];
    else
        return Array();
}


?>
