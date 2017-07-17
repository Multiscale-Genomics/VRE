<?php

/*
 * users.inc.php
 * 
 */

//use PMut\User;


function checkLoggedIn() {
    return (isset($_SESSION['User']));
}

function checkLoginErrors($f) {
    unset($_SESSION['errorData']);
    $errorCount = checkOblig($_SESSION['errorData'], $f, $f['oblig']);
    if (!$errorCount) {
        $userObj = loadUserLdap($f['login'], $f['password']);
        if (!$userObj) {
            $errorCount++;
            $_SESSION['errorData']['login'][] = "Contact <a href=\"mailto:transplant@bsc.es?Subject=Account%20DataManager\" target=\"_top\">transplantdb@bsc.es</a> to get a new password or user account";
        }else{
            $_SESSION['User'] = $userObj;
        }
    }
    // add login info into logger
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
	$ipProxy = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	$ipProxy = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
	$ipProxy = 'UNK';
    }
    if (!empty($_SERVER['REMOTE_ADDR'])) {
	$ip = $_SERVER['REMOTE_ADDR'];
    } else {
	$ip = 'UNK';
    }
    $host     = gethostbyaddr($ip);
    $hostProxy= gethostbyaddr($ipProxy);
    logger('Login: '.$f['login']." ; Err: $errorCount; IP: $ip|$host; IP_FORWARDED: $ipProxy|$hostProxy");
    return $errorCount;
}

function logoutUser() {
    logger('Logout: '.$_SESSION['User']['login']);
    unset($_SESSION['User']);
}

function checkUserLoginExists($id) {
    return $GLOBALS['users']->findOne(array('id'=>$id));
}

function loadUser($login, $pass) {
    $crpass = crypt($pass, PASSWORD_SALT);
//  print $crpass;
//  $user = $GLOBALS['usersCol']->findOne(array('_id' => $login, 'crypPassword' => $crpass));
    if (!$user['_id'])
        return False;
    $userObj = new User($user);
    return $userObj;
}

function loadUser2($id, $pass){

    if (!file_exists($GLOBALS['passFile'])){
	$_SESSION['errorData']['login'][] = 'Pass file not found';
	return FALSE;	
    }	
    //$crpass = crypt($pass, PASSWORD_SALT); 

   $line = exec("grep $login ".$GLOBALS['passFile']); // no cal carregar totsw els usuaris

   if(!empty($line)) {
    	$lineArr = explode(':', $line);
	if ($lineArr[0]==$login && ($lineArr[1]==crypt($pass,$lineArr[1]))){
		$uniqId  = (isset($lineArr[5]) ? $lineArr[5] : '');
	      	$user = array('_id' => $login, 'Surname' => $login, 'Email' => $login, 'Anon' => FALSE, 'uniqId' => $uniqId, 'crypPassword' => crypt($pass,$lineArr[1]) );
		$userObj = new User($user);
    		return $userObj;
	}
    }
    $_SESSION['errorData']['login'][] = 'Incorrect username or password';
    return FALSE;
}

function loadUserLdap($login, $pass){
	$ds=ldap_connect("localhost");
	ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
	if ($ds) {
		$dn   = "uid=$login,ou=People,dc=bsc,dc=es";
		$ok = ldap_bind($ds,$dn,$pass);
		if (!$ok){
			$_SESSION['errorData']['login'][] = "Cannot bind user $login to LDAP server";	
			return FALSE;
		}

		$search=ldap_search($ds, "dc=bsc,dc=es", "uid=$login"); 
		if (ldap_count_entries($ds,$search) != 1){
			$_SESSION['errorData']['login'][] = 'Internal LDAP error. No uid=$login found.';
			return FALSE;
		}
		$info = ldap_get_entries($ds, $search);
		$crypPassword = $info[0]["userpassword"][0];
		$uniqId       = end(split("/",rtrim($info[0]["homedirectory"][0],"/")));
		$user = array('_id' => $login, 'Surname' => $login, 'Email' => $login, 'Anon' => FALSE, 'uniqId' => $uniqId, 'crypPassword' => $crypPassword );
		$userObj = new User($user);
		ldap_close($ds);	
		return $userObj;
	}else {
	    $_SESSION['errorData']['login'][] = 'No se puede conectar al servidor LDAP';
	    return FALSE;
	}
}
function loadUser3($login, $pass){

    $ftpObj = new FTPClient();
    $ftpObj -> connect(FTP_HOST, $login, $pass);
	
    if (!($ftpObj -> connectionId) ){
        $_SESSION['errorData']['login'][]= $ftpObj -> getMessages();
        $_SESSION['errorData']['login'][] = 'Incorrect username or password';
	return FALSE;
    }else{
	$_SESSION['Ftp']=$ftpObj;
    	$line = exec("grep $login ".$GLOBALS['passFile']);
   	if(empty($line)) {
		$_SESSION['errorData']['login'][]= "{$login} not found in the local credential manager";
		return FALSE;
	}
	$lineArr = explode(':', $line);
	$uniqId  = (isset($lineArr[5]) ? $lineArr[5] : '');
    	$user = array('_id' => $login, 'Surname' => $login, 'Email' => $login, 'Anon' => FALSE, 'uniqId' => $uniqId, 'crypPassword' => crypt($pass,$lineArr[1]));
	$userObj = new User($user);
    	return $userObj;
    }
}


function userMenu() {
    if (checkLoggedIn()){
        return "<p><b>User: " . $_SESSION['User']->fullName(True, True) . "</b>"
            . "  [<a href=\"workspace.php?op=close\">Close Workspace</a>]</p>";
    }else{
        return '<p><b>User: <i>No current user set</i></b> [<a href="index.php">login</a>]</p>';
    }
}

function prepUserWorkSpace() {
    switch ($GLOBALS['fsStyle']) {
        case "fs" :
            //if (!file_exists($_SESSION['User']->uniqId))
            //    mkdir($_SESSION['User']->uniqId);
            //$_SESSION['User']->dataDir = $_SESSION['User']->uniqId . "/" . $GLOBALS['fsDirPrefix'];
            break;
        case "gridfs":
            //$_SESSION['User']->dataDir = $_SESSION['User']->uniqId;
            $GLOBALS['users']->update(
                    array('id' => $_SESSION['BNSid']), array('$set' => array(
                    					    'lastAccess' => moment())
                    )
            );
	    if (! isGSDirBNS($GLOBALS['cassandraIds'],$_SESSION['BNSId']) ){
		createGSDirBNS($GLOBALS['cassandraIds'],$_SESSION['BNSId']);
	    }
	    if (! isGSDirBNS($GLOBALS['cassandraIds'],$_SESSION['BNSId']) ){
		$_SESSION['errorData']['login'][] = "Cannot create home directory for ". $_SESSION['BNSId'];
	   }else{
		$_SESSION['User']['home']= $_SESSION['BNSId'];
	   }
           break;

        default:
            print "Error: fsStyle not set or unknown";
            exit(1);
    }
    if (! isset( $_SESSION['curDir']))
	$_SESSION['curDir'] = $_SESSION['BNSId'];
	//$_SESSION['curDir'] = $_SESSION['User']->dataDir;
}

function updateUserData($User) {
    //$GLOBALS['users']->update(array('id' => $User->_id), (array) $User);
}

function delUser($id){
    $files  =  $GLOBALS['cassandraIds']->find(array("owner"=>$id));
    $files  =  $GLOBALS['cassandraIds']->find(array(
			'owner' => $id,
                        '_id'  =>  array('$ne' => $id)
		));
    if ($files->count() == 0){
    	$GLOBALS['users']->remove(array('id' => $id));
	$GLOBALS['cassandraIds']->remove(array('_id'=> $fn));
    }else{
	print "ERR: Cannot remove ".$id." owns files\n";
    }
}
?>
