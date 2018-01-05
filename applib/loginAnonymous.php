<?php

require "../phplib/genlibraries.php";


// Check if PHP session exists.
$r = checkLoggedIn();

if (!$r){
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

redirect("../home/redirect.php");
