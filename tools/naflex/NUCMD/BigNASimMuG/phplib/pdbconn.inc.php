<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
#$pdbConn = new MongoClient("mongodb://mmb.pcb.ub.es");
#$pdbConn = new MongoClient("mongodb://localhost");
try {
	#$pdbConn = new MongoClient("mongodb://readAny:mdbrany2015@ms1.mmb.pcb.ub.es");
	$pdbConn = new MongoClient("mongodb://dataLoader:mdbwany2015@ms1.mmb.pcb.ub.es");
}
catch (MongoConnectionException $e){
	#die('Error connecting to MongoDB server');
	redirect("problems.php");
}
catch (MongoException $e) { 
	#die('Error: ' . $e->getMessage());
	redirect("problems.php");
}

$GLOBALS['db'] = $pdbConn->NUCMDANAL;
$GLOBALS['analData'] = $GLOBALS['db']->analData;
$GLOBALS['analDefs'] = $GLOBALS['db']->analDefs;
$GLOBALS['analCount'] = $GLOBALS['db']->analCount;
$GLOBALS['groupDef'] = $GLOBALS['db']->groupDef;
$GLOBALS['simData'] = $GLOBALS['db']->simData;
$GLOBALS['analFiles'] = $GLOBALS['db']->analFiles;
$GLOBALS['usersCol'] = $GLOBALS['db']->users;
$GLOBALS['paisesCol']= $GLOBALS['db']->paises;
$GLOBALS['labelsCol']= $GLOBALS['db']->labels;
$GLOBALS['errorsCol']= $GLOBALS['db']->errorMsg;
$GLOBALS['projectsCol'] = $GLOBALS['db']->projects;
$GLOBALS['ontoCol'] = $GLOBALS['db']->ontology;

#$GLOBALS['analFiles'] = $pdbConn->analFiles->getGridFS();

$GLOBALS['db2'] = $pdbConn->FlexPortal; 
$GLOBALS['sequencesCol'] = $GLOBALS['db2']->sequences;

$GLOBALS['cassandra'] = $pdbConn->restcastemp->getGridFS();
