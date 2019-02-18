<?php
require "../phplib/genlibraries.php";
require "../phplib/admin.inc.php";


$r = checkLoggedIn();
if ($r){
    if (!checkAdmin()){
        $_SESSION['errorData']['Error'][]="Cannot impersonate a user. Permission denied.";
        //redirect("../home/redirect.php");
        die(0);
    }

    // Load requested user
    if ($_REQUEST['id']){
        $r = loadUser($_REQUEST['id'],99);
        if ($r === FALSE){
            $_SESSION['errorData']['Error'][]="Cannot impersonate a user. Load user returned error.";
            redirect("../home/redirect.php");
        }
    }
}
/*
print "<br/><br>";
var_dump($_SESSION['User']);
print "<br/><br>";
var_dump($_SESSION['errorData']);
print "<br/><br>";
unset($_SESSION['errorData']['Error']);
 */

redirect("../home/redirect.php");
