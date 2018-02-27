<html>
<head>
  <meta charset="utf-8" />
  </head>
<?php
require "phplib/genlibraries.php";

//checkIfSessionUser(basename($_SERVER['PHP_SELF']));

// Check if PHP session exists.
$r = checkLoggedIn();

if (1){
    if ($_REQUEST['id']){
        // Recover guest user
        $r = loadUser($_REQUEST['id'],false);
    }else{
        // Get access creating an a anonymous guest account
        $r = createUserAnonymous();
        if (!$r)
            exit('Login error: cannot create anonymous VRE user');
    }
}
//redirect("../home/");
redirect("../home/redirect.php");
