<?php
/*
 * 
 */

require "phplib/genlibraries.php";


# list all users

$users = array();

foreach ( array_values(iterator_to_array($GLOBALS['usersCol']->find(array(),array('Name'=>1, 'Type'=>1, 'Status'=>1, 'registrationDate'=>1, 'dataDir'=>1 )) )) as $v){
    $users[$v['_id']] = array($v['Type'], $v['dataDir'], $v['registrationDate']);
    $rdir =  $GLOBALS['dataDir']."/".$v['dataDir'];

    # clean Mongo users not present in disk
    if (! is_dir($rdir)){
        if ($v['Type'] == 3){
            print "rm -rf ".escapeshellarg($rdir)."\n";
            #system("rm -rf ".escapeshellarg($rdir));
        }else{
            print "User ".$v['Name']." ".$v['_id']. " has not user directory $rdir. Check manually !!\n";
        }
    }
    print $v['_id']. " --> $rdir\n";
}



# get user's files


# get file path
$_SESSION['User']['id']="MuGUSER5a0c0314c20d1";
$filePath = getAttr_fromGSFileId("MuGUSER5a0c0314c20d1_5a0c0314e3abe7.44042947",'path'); 
$rfn      = $GLOBALS['dataDir']."/$filePath";

print "PATH = $filePath  ---  ABS PATH = $rfn\n";

# check file path
