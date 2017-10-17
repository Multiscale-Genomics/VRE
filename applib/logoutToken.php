<?php

require "../phplib/genlibraries.php";

//use ExtendedGenericProvider\ExtendedGenericProvider;
use MuG_Oauth2Provider\MuG_Oauth2Provider;

/*
print "<br/>ssssssssssssssssss<br/>";
var_dump($_SESSION['User']);
print "<br/>ssssssssssssssssss<br/>";
 */

if($_REQUEST){
//if($_POST){

    // End oauth2 session
    //$provider = new MuG_Oauth2Provider(['redirectUri'=> 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF']]);
    $provider = new MuG_Oauth2Provider(['redirectUri'=> $GLOBALS['URL'] . $_SERVER['PHP_SELF']]);

    /*
    $conf = getConf(__DIR__."/../../conf/oauth2.conf");
    $clientId     = $conf[0];
    $clientSecret = $conf[1];
    $provider = new MuG_Oauth2Provider([
        'clientId'                => $clientId,
        'clientSecret'            => $clientSecret,
        'redirectUri'             => 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'],
        'urlAuthorize'            => $GLOBALS['urlAuthorize'],
        'urlAccessToken'          => $GLOBALS['urlAccessToken'], 
        'urlLogout'               => $GLOBALS['urlLogout'],
        'urlResourceOwnerDetails' => $GLOBALS['urlResourceOwnerDetails']
    ]);
     */

    try{
        $refresh_token = $_SESSION['User']['Token']['refresh_token'];
        $r = $provider->logoutSession($refresh_token);
    } catch (\Exception $e){
        //exit("Cannot close authorization server session. Server returns: ".  $e->getMessage());
	    redirect($GLOBALS['URL']);
    }

    // End php session
    if ($r)
        logoutUser();
    
	echo '1';

}else{
	redirect($GLOBALS['URL']);
}
