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

function checkToolDev() {

	$user = $GLOBALS['usersCol']->findOne(array('_id' => $_SESSION['User']['_id']));

	if(isset($_SESSION['User']) && ($user['Status'] == 1) && (allowedRoles($user['Type'], $GLOBALS['TOOLDEV']) || allowedRoles($user['Type'], $GLOBALS['ADMIN']))) return true;
	else return false;
}

// create user - from sign up
function createUser(&$f) {

    // create full user object
   	$objUser = new User($f, True);
	$aux = (array)$objUser;

    //load user in current session
	$_SESSION['userId'] = $aux['id']; //OBSOLETE
	$_SESSION['User'] = $aux;
	unset($_SESSION['crypPassword']);

    // create user directory 
	$dataDirId =  prepUserWorkSpace($aux['id']);
	if (!$dataDirId){
        $_SESSION['errorData']['Error'][]="Error creating data dir";
        echo "Error creating data dir";
        return false;
    }
    $aux['dataDir']     = $dataDirId;
    $aux['AuthProvider']= "ldap-cloud";
    $_SESSION['User']['dataDir'] = $dataDirId;

    // register user into mongo
    $r = saveNewUser($aux);
    if (!$r){
        $_SESSION['errorData']['Error'][]="User creation failed while registering it into the database. Please, manually clean orphan files for ".$aux['id']. "(".$dataDirId.")";
        echo 'Error saving new user into Mongo database';
	    unset($_SESSION['User']);
        return false;
    }

    // register user in MuG ldap
    $r = saveNewUser_ldap($aux);
    if (!$r){
        $_SESSION['errorData']['Error'][]="Failed to register ".$userObj['id']." into LDAP";
        $_SESSION['errorData']['Error'][]="User creation failed while registering it to LDAP. Please, <a href=\"applib/delUser.php?id=".$aux['id']."\">DELETE USER</a>";
	    unset($_SESSION['User']);
        return false;
    }

    // send mail
	sendWelcomeToNewUser($aux['_id'], $aux['Name'], $aux['Surname']);
    return true;
}

// create user - after being authentified by the Auth Server
function createUserFromToken($login,$token,$userinfo=array()){

		//var_dump($userinfo);
		//die();

    // create full user oject
    $f = array(
        "Email"        => $login,
        "Token"        => $token,
        "Type"         => 2
    );
    if ($userinfo){
        if ($userinfo['lastName'])
           $f['Surname'] = $userinfo['lastName'];
        if ($userinfo['firstName'])
            $f['Name'] = $userinfo['firstName'];
        if ($userinfo['provider'])
            $f['AuthProvider'] = $userinfo['provider'];
    }
    $objUser = new User($f, True);
    if (!$objUser)
        return false;
    $aux = (array)$objUser;

    //load user in current session
    $_SESSION['userId'] = $aux['id']; //OBSOLETE
	$_SESSION['User']   = $aux;
    unset($_SESSION['crypPassword']);

    // create user directory
    $dataDirId =  prepUserWorkSpace($aux['id']);
	if (!$dataDirId){
        $_SESSION['errorData']['Error'][]="Error creating data dir";
        echo "Error creating data dir";
        return false;
    }
    $aux['dataDir']= $dataDirId;
    $_SESSION['User']['dataDir'] = $dataDirId;

    // register user in mongo. NOT in ldap, as user exists for a oauth2 provider
  	$r = saveNewUser($aux);
    if (!$r){
        $_SESSION['errorData']['Error'][]="User creation failed while registering it into the database. Please, manually clean orphan files for ".$aux['id']. "(".$dataDirId.")";
        echo 'Error saving new user into Mongo database';
	    unset($_SESSION['User']);
        return false;
    }

    //  inject user['id'] into auth server (keycloak) as 'mug_id' (so APIs will find it in /openid-connect/userinfo endpoint)
    $r = injectMugIdToKeycloak($aux['_id'],$aux['id']);

    // if not all user metadata mapped from oauth2 provider, ask the user
    if (!$aux['Name'] || !$aux['Surname'] || !$aux['Inst'] || !$aux['Country']){
        redirect('../user/usrProfile.php');
        exit(0);
    }
    return true;
}

// create user - from Admin section
function createUserFromAdmin(&$f) {

    // create full user object
    $objUser = new User($f, True);
    $aux = (array)$objUser;
    $_SESSION['errorData']['Info'][] = "New user data object created. Login = ".$aux['_id']." Password = ".$f['pass1'];

    // create user directory
    $dataDirId =  prepUserWorkSpace($aux['id'],$f['DataSample']);
    if (!$dataDirId){
		$_SESSION['errorData']['Error'][] = "Error creating new user directory with '".$aux['id']."'. If needed <a href=\"applib/delUser.php?id=".$aux['id']."\">delete user</a>";
        echo "Error creating data dir";
        return false;
    }
    $_SESSION['errorData']['Info'][] = "New workspace created at '".$aux['id']."' (id=$dataDirId).";
    $aux['dataDir']= $dataDirId;
    $aux['AuthProvider']= "ldap-cloud";

    // register user in mongo
    $r = saveNewUser($aux);
    if (!$r){
        $_SESSION['errorData']['Error'][]="User creation failed while registering it into the database. Please, manually clean orphan files for ".$aux['id']. "(".$dataDirId.")";
        echo 'Error saving new user into Mongo database';
    }
    $_SESSION['errorData']['Info'][] = "New user successfuly created";

    // register user in MuG ldap
    $r = saveNewUser_ldap($aux);
    if (!$r){
        $_SESSION['errorData']['Error'][]="Failed to register ".$userObj['id']." into LDAP";
        $_SESSION['errorData']['Error'][]="User creation failed while registering it to LDAP. Please, <a href=\"applib/delUser.php?id=".$aux['id']."\">DELETE USER</a>";
        return false;
    }

    // send mail to user, if selected
	if($f['sendEmail'] == 1) sendPasswordToNewUser($f['Email'], $f['Name'], $f['Surname'], $f['pass1']);
    
    return true;
}

// update user document in  Mongo
function updateUser($f) {
    $GLOBALS['usersCol']->update(array('_id' => $f['_id']), $f, array('upsert=>1'));
}

// load user to SESSION
function setUser($f,$lastLogin) {
    $aux = (array)$f;
	unset($aux['crypPassword']);
	//unset($aux['lastLogin']);
    $_SESSION['User']   = $aux;
	$_SESSION['curDir'] = $_SESSION['User']['id'];

	if(!isset($_SESSION['lastUserLogin'])) $_SESSION['lastUserLogin'] = $lastLogin;
}

function delUser($id, $asRoot=1){

    //delete data from Mongo and disk
    $homeId = getGSFileId_fromPath($id,$asRoot);
    if (!$homeId)
        $homeId = getGSFileId_fromPath($id."/",$asRoot);

    $home   = getGSFile_fromId($homeId,"all",$asRoot);
    $rfn   = $GLOBALS['dataDir']."/".$home['path'];
/*
    if (empty($home) || !is_dir($rfn) ){
	    $_SESSION['errorData']['Error'][]="Cannot delete user ID=$id. Its data in the repository ($rfn) or in the databse ($homeId) is not found";
      return 0;
    }
*/

    $r = deleteGSDirBNS($homeId,$asRoot);
    if ($r == 0){
	    $_SESSION['errorData']['Error'][]="Cannot delete user entry in database.";
        return 0;
    }
 

    if (is_dir($rfn)){
	    exec ("rm -r \"$rfn\" 2>&1",$output);
      	// if (is_dir($rfn)){
	    //   $_SESSION['errorData']['Error'][]="Cannot delete user data in repository. '$rfn' still accessible";
    	//	 $_SESSION['errorData']['Error'][]=implode(" ",$output);
        //   return 0;
        //}
    }


    //delete user from ldap
    $user = $GLOBALS['usersCol']->findOne(array('id' => $id));
    $r = delUser_ldap($user['_id']);

	//delete user from mongo
	$GLOBALS['usersCol']->remove(array('id'=> $id));

    return 1;


    //delete data from Mongo and disk
    $data = $GLOBALS['filesCol']->find(array('owner' => $id));
	if ($data->count() != 0 ){
	    foreach ($data as $f){
            $rfn      = $GLOBALS['dataDir']."/".$f['path'];
    		if ((isset($f['type']) && $f['type']=="dir")  || isset($f['files'])){
                if (!is_file($rfn))
                    next;
    			$r = deleteGSDirBNS($f['_id'],1);
                if ($r == 0)
         			return 0;
	    		if (is_dir($rfn)){
	               	exec ("rm -r \"$rfn\" 2>&1",$output);
				    if (is_dir($rfn)){
		                $_SESSION['errorData']['error'][]="Cannot delete user data $rfn";
		                $_SESSION['errorData']['error'][]=implode(" ",$output);
				    }
			    }
		    }else{
                if (!is_dir($rfn))
                    next;
			    $r = deleteGSFileBNS($f['_id'],1);
	            if ($r == 0)
         		    return 0;
			    if (is_file($rfn)){
		        	unlink ($rfn);
				    if (is_file($rfn)){
    			        $_SESSION['errorData']['error'][]="Cannot delete user file $rfn";
    			        if (error_get_last())
    			            $_SESSION['errorData']['error'][]=error_get_last()["message"];
    	         		return 0;
    				}
			    }
		    }
	    }
	}
    $data = $GLOBALS['filesCol']->find(array('owner' => $id));
	if ($data->count() != 0){
		$_SESSION['errorData']['Error'][]="Cannot delete user. Failed to clean all files onwed by $id. Manual clean required.";
	    	foreach ($data as $f){
			    $_SESSION['errorData']['Error'][]= "Cannot delete user file ".$f['path'];
		    }
		return 0;
	}
    //delete user from ldap
    $user = $GLOBALS['usersCol']->findOne(array('id' => $id));
    $r = delUser_ldap($user['_id']);

	//delete user from mongo
	$GLOBALS['usersCol']->remove(array('id'=> $id));

    return 1;

}


function injectMugIdToKeycloak($login,$id){

    $kc_token = get_keycloak_admintoken();

    if ($kc_token  && isset($kc_token['access_token'])){
        $kc_user = get_keycloak_user($login,$kc_token['access_token']);
        if ($kc_user && isset($kc_user['id'])){
            $attributes = array();
            if ($kc_user['attributes'])
                $attributes = $kc_user['attributes'];
            $attributes['mug_id'] = array($id);
            $data = array("attributes" => $attributes); 
            $r = update_keycloak_user($kc_user['id'],json_encode($data),$kc_token['access_token']);

            if (!$r){
                $_SESSION['errorData']['Warning'][]="User not valid to be used outside VRE. Could not inject 'mug_id' into Auth Server. Cannot update ".$aux['_id']." in its registry";
                return false;
            }else{
                return true;
            }
        }else{
            $_SESSION['errorData']['Warning'][]="User not valid to be used outside VRE. Could not inject 'mug_id' into Auth Server. Cannot get ".$aux['_id']." from its registry";
            return false;
        }
    }else{
        $_SESSION['errorData']['Warning'][]="User not valid to be used outside VRE. Could not inject 'mug_id' into Auth Server. Token not created";
        return false;
    }
}
    
    
function logoutUser() {
    session_unset();
}

function saveNewUser($userObj) {
    $r = $GLOBALS['usersCol']->insert($userObj);
    if (!$r)
        return false;

    return true;
}

function checkUserIDExists($login) {
	//die("check user login exists");
    if ($login)
        $user = $GLOBALS['usersCol']->findOne(array('id' => $login));

    return ($user);
}

function checkUserLoginExists($login) {
	//die("check user login exists");
    if ($login)
        $user = $GLOBALS['usersCol']->findOne(array('_id' => $login));

    return ($user);
}

function loadUser($login, $pass) {
    $user = $GLOBALS['usersCol']->findOne(array('_id' => $login));
    //if (!$user['_id'] || !password_verify($pass, $user['crypPassword']) || ($user['Status'] == 0)) {
    if (!$user['_id'] || !check_password($pass, $user['crypPassword']) || ($user['Status'] == 0)) {
        return False;
	}
	$auxlastlog = $user['lastLogin'];
    $user['lastLogin'] = moment();
    updateUser($user);
    setUser($user,$auxlastlog);

    return $user;
}

function loadUserWithToken($login, $token){
    $user = $GLOBALS['usersCol']->findOne(array('_id' => $login));

    if (!$user['_id'] || $user['Status'] == 0)
        return False;
    
	$auxlastlog = $user['lastLogin'];
    $user['lastLogin'] = moment();
    $user['Token']     = $token;
    updateUser($user);
    setUser($user,$auxlastlog);

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
                                             "lastjobs.$pid"=> array('$exists' => true)
                                            ));
    if (isset($r['lastjobs']))
        return $r['lastjobs'];
    else
        return Array();
}

?>
