<?php 

require "../phplib/genlibraries.php";
require "../phplib/mongoDMP.inc.php";

#$GLOBALS['dataDir'] = "/gpfs/MuG_userdata/"; 
$_SESSION['curDir'] = "test_user";
$_SESSION['User']['id'] = "test_user";
$_SESSION['User']['token'] = "";

#http://localhost:500/mug/api/dmp/track?file_id=59a81d658743651a977c98fc&user_id=test_user&chrom=1&start=1000&end=2000"
$f = getGSFile_fromIdXXX("59a81d658743651a977c98fc");

print "\n################ getGSFile_fromIdXXX\n";
var_dump($f);

$id = createGSDirBNSXXX("test_user/run000");
print "\n################ createGSDirBNSXXX\n";
var_dump($id);

print "\n################ ERROR SSESION\n";
var_dump($_SESSION['errorData']);

