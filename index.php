<html>
<head>
  <meta charset="utf-8" />
  </head>
<?php
 
require "phplib/genlibraries.php";


//checkIfSessionUser(basename($_SERVER['PHP_SELF']));

// Check if PHP session exists.
$r = checkLoggedIn();


// Recover guest user
if ($_REQUEST['id']){
    if(! checkUserLoginExists($_REQUEST['id'])){
        unset($_REQUEST['id']);
    }
    $r = loadUser($_REQUEST['id'],false);
}

// TEMPORAL IF !!!!!!!!!!!!!!!!!!!!1
if ($_REQUEST['guai_user']){
    $login = $_REQUEST["guai_user"];
    $user = $GLOBALS['usersCol']->findOne(array('_id' => $login));

    if (!$user['_id'] || $user['Status'] == 0 || $user['Type'] !="0") {
        redirect("../home/redirect.php");
    }
    $auxlastlog = $user['lastLogin'];
    $user['lastLogin'] = moment();
    updateUser($user);
    setUser($user,$auxlastlog);
    redirect("../home/redirect.php");
}
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!

// Create guest    
if (!$_REQUEST['id']){
   // Read requested tool, if any
    $tool = array();
    $sd   = "";
    if ($_REQUEST['from']){
       $tool = getTool_fromId($_REQUEST['from'],1);
       if (!isset($tool['_id'])){
          $_SESSION['userData']['Warning'][]="Cannot load '".$_REQUEST['from']."'. Tool not found";
          redirect("../home/redirect.php");
       }
       /*
       if (isset($tool['sampleData'])){
          $sd = $tool['sampleData'];
        }else{
          $sd = $tool['_id'];
        }
       //$sd = ( isset($_REQUEST['sd'])?$_REQUEST['sd']:"");
       */
    }
       
    // Get access creating an a anonymous guest account
    $r = createUserAnonymous($sd);
    if (!$r)
        exit('Login error: cannot create anonymous VRE user');

    // Redirect to WS with a welcome modal
    if ($_REQUEST['from']){
        redirect("../workspace/?from=".$_REQUEST['from']);
    }

}

redirect("../home/redirect.php");
