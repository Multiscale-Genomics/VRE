<?php

$conf = getConf(dirname(__DIR__)."/../conf/mongo.conf");

try {
	$MuGVREConn = new MongoClient("mongodb://".$conf[0].":".$conf[1]."@".$conf[2].":27017");
}
catch (MongoConnectionException $e){
    //die('Error Connecting Mongo DB: ' . $e->getMessage());
    header('Location: '.$GLOBALS['URL'].'errors/errordb.php?msg=Cannot connect to VRE MuG database');	
}
catch (MongoException $e) {
    die('Error: ' . $e->getMessage());
}

$GLOBALS['db'] = $MuGVREConn->MuGVRE_bsc;
$GLOBALS['usersCol'] = $GLOBALS['db']->users;
$GLOBALS['countriesCol']= $GLOBALS['db']->countries;
$GLOBALS['filesCol']    = $GLOBALS['db']->files;
$GLOBALS['filesMetaCol']= $GLOBALS['db']->filesMetadata;
$GLOBALS['checkMail']= $GLOBALS['db']->checkMail;
$GLOBALS['toolsCol'] = $GLOBALS['db']->tools;
$GLOBALS['visualizersCol'] = $GLOBALS['db']->visualizers;
$GLOBALS['fileTypesCol']    = $GLOBALS['db']->file_types;
$GLOBALS['dataTypesCol']    = $GLOBALS['db']->data_types;

$GLOBALS['dbData'] = $MuGVREConn->MuGVREData;
$GLOBALS['studiesCol'] = $GLOBALS['dbData']->studies;
$GLOBALS['repositoriesCol']= $GLOBALS['dbData']->repositories;

$GLOBALS['dbPDB'] = $MuGVREConn->FlexPortal;
$GLOBALS['pdbCol'] = $GLOBALS['dbPDB']->PDB_Entry;
$GLOBALS['monomersCol']= $GLOBALS['dbPDB']->PDB_Monomers;

?>
