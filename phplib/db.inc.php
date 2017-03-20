<?php

$conf = getConf(dirname(__DIR__)."/../conf/conf.conf");

try {
	$MuGVREConn = new MongoClient("mongodb://".$conf[0].":".$conf[1]."@".$conf[2]);
}
catch (MongoConnectionException $e){
    die('Error connecting to MongoDB server');
}
catch (MongoException $e) {
    die('Error: ' . $e->getMessage());
}

$GLOBALS['db'] = $MuGVREConn->MuGVRE;
$GLOBALS['usersCol'] = $GLOBALS['db']->users;
$GLOBALS['countriesCol']= $GLOBALS['db']->countries;
$GLOBALS['filesCol']    = $GLOBALS['db']->files;
$GLOBALS['filesMetaCol']= $GLOBALS['db']->filesMetadata;
$GLOBALS['checkMail']= $GLOBALS['db']->checkMail;
$GLOBALS['toolsCol'] = $GLOBALS['db']->tools;
$GLOBALS['visualizersCol'] = $GLOBALS['db']->visualizers;


$GLOBALS['dbData'] = $MuGVREConn->MuGVREData;
$GLOBALS['studiesCol'] = $GLOBALS['dbData']->studies;
$GLOBALS['repositoriesCol']= $GLOBALS['dbData']->repositories;

$GLOBALS['dbPDB'] = $MuGVREConn->FlexPortal;
$GLOBALS['pdbCol'] = $GLOBALS['dbPDB']->PDB_Entry;
$GLOBALS['monomersCol']= $GLOBALS['dbPDB']->PDB_Monomers;

?>
