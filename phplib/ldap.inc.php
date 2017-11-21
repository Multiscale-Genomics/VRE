<?php

$conf = getConf(dirname(__DIR__)."/../conf/ldap.conf");

$user = "cn=admin,dc=cloud,dc=local"; //0
$pass = "bsccns.01";
$serv = "192.168.122.55";
$dn   = "ou=people,dc=cloud,dc=local"; //3



try {
    $ldapConn = ldap_connect("ldap://".$serv);
}
catch (\Exception $e){
    header('Location: '.$GLOBALS['URL'].'/errors/errordb.php?msg='.$e->getMessage());	
}

ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
$login = ldap_bind($ldapConn,$user,$pass);

if (!$login){
    header('Location: '.$GLOBALS['URL'].'/errors/errordb.php?msg=Cannot connect to MuG user access protocol');
}

$GLOBALS['ldap']    = $ldapConn;
$GLOBALS['ldap_dn'] = $conf[3];


/*
$dn   = "ou=people,dc=cloud,dc=local";
$r = ldap_search($GLOBALS['ldap'],$dn , "cn=*");
$data = ldap_get_entries($GLOBALS['ldap'], $r);
var_dump($data);

for ($i=0; $i<$data["count"]; $i++) {
    echo "<h4><strong>Common Name: </strong>" . $data[$i]["cn"][0] . "</h4><br />";
    echo "<strong>Distinguished Name: </strong>" . $data[$i]["dn"] . "<br />";

    if (isset($data[$i]["description"][0])) 
        echo "<strong>Desription: </strong>" . $data[$i]["description"][0] . "<br />";
    else 
        echo "<strong>Description not set</strong><br />";
    
    //checking if email exists
    if (isset($data[$i]["mail"][0]))
        echo "<strong>Email: </strong>" . $data[$i]["mail"][0] . "<br /><hr />";
    else 
        echo "<strong>Email not set</strong><br /><hr />";
}
ldap_close($GLOBALS['ldap']);
*/
     
?>
