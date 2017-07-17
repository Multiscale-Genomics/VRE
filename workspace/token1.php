<?php

require "../phplib/genlibraries.php";


$aux['_id']= "laiacodo@gmail.com";
$aux['id'] = "MuGUSttttttt44";

$kc_token = get_keycloak_admintoken();


if ($kc_token  && isset($kc_token['access_token'])){
    $kc_user = get_keycloak_user($aux['_id'],$kc_token['access_token']);
    if ($kc_user && isset($kc_user['id'])){
            $attributes = array();
            if ($kc_user['attributes'])
                $attributes = $kc_user['attributes'];
            $attributes['mug_id'] = array($aux['id']);
            $data = array("attributes" => $attributes); 
            $r = update_keycloak_user($kc_user['id'],json_encode($data),$kc_token['access_token']);

            if (!$r)
                $_SESSION['errorData']['Warning'][]="User not valid to be used outside VRE. Could not inject 'mug_id' into Auth Server. Cannot update ".$aux['_id']." in its registry";
    }else{
        $_SESSION['errorData']['Warning'][]="User not valid to be used outside VRE. Could not inject 'mug_id' into Auth Server. Cannot get ".$aux['_id']." from its registry";
    }
}else{
    $_SESSION['errorData']['Warning'][]="User not valid to be used outside VRE. Could not inject 'mug_id' into Auth Server. Token not created";
}

var_dump($_SESSION['errorData']);
unset($_SESSION['errorData']);
