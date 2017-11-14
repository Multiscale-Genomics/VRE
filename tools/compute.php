<?php

require "../phplib/genlibraries.php";
require "../phplib/tools.inc.php";

redirectOutside();


$SGE_updated = getUserJobs($_SESSION['User']['id']);
$jobMeta     = Array();


print "REQUEST FILES ARE:</br>";
var_dump($_REQUEST['fn']);
print "<br/>";

print "RAW PARAMS ARE<br/>";
var_dump($_REQUEST['params']);
print "<br/>";


//
// Checking Input Files
if (!isset($_REQUEST['fn'])){
    $_SESSION['errorData']['Error'][]="Select at least one input file required.";
    ?><script>window.history.back();</script><?php //var_dump($_SESSION['errorData']);unset($_SESSION['errorData']);
    //exit(0);
}
$jobMeta['inPaths'] = Array();
foreach($_REQUEST['fn'] as $fn){
        array_push($jobMeta['inPaths'],getAttr_fromGSFileId($fn,'path'));
}


//
// Check tool
$tool = $GLOBALS['toolsCol']->findOne(array('_id' => $_REQUEST['tool']));
if (empty($tool)){
	$_SESSION['errorData']['Error'][]="Tool not specified or not registered. Please, register '".$_REQUEST['tool']."'";
	?><script>window.history.back();</script><?php //var_dump($_SESSION['errorData']);unset($_SESSION['errorData']);
	//exit(0);
}
$jobMeta['tool'] = $_REQUEST['tool']; 


//
// Setting description
if (!isset($_REQUEST['description'])){
	$_REQUEST['description'] = "Execution directory for tool ".$jobMeta['tool'];
}
$jobMeta['description']=$_REQUEST['description'];


//
// Setting working directory name

$project = $_REQUEST['project'];
$wd      = $GLOBALS['dataDir']."/".$_SESSION['User']['id']."/$project";
$wdFN    = $_SESSION['User']['id']."/$project";

$prevs   = $GLOBALS['filesCol']->find(array('path' => $wdFN, 'owner' => $_SESSION['User']['id']) );
if ($prevs->count() > 0){
    for ($n=1;$n<99;$n++){
        $projectN= $project. "_$n";
        $wdFN    = $_SESSION['User']['id']."/$projectN";
        $prevs   = $GLOBALS['filesCol']->find(array('path' => $wdFN, 'owner' => $_SESSION['User']['id']));
        if ($prevs->count() == 0){
            $project= $projectN;
            $wd     = $GLOBALS['dataDir']."/$wdFN";
            break;
        }
    }
}
$jobMeta['outDir'] = $wdFN; 
$jobMeta['project'] = $project;
echo "<br/>WD IS : $wdFN (PROJ NAME = $project)<br/>";


//
// Adding common form parameters to params
foreach ($_REQUEST['params'] as $analysisId => $analisisParms) {
    $_REQUEST['params'][$analysisId]['project']    = $jobMeta['project'];
    $_REQUEST['params'][$analysisId]['description']= $jobMeta['description'];
}

//
// Setting Command line. Adding parameters

$cmdParams=Array();
foreach ($_REQUEST['params'] as $analysisId => $analisisParms) {

    print "<br/>PREPARING CMD for Analysys Id: $analysisId<br/>";

    if (! in_array($analysisId,$tool['analyses']) ){
	$_SESSION['errorData']['Error'][]="Analysis '$analysisId' not defined for tool '".$_REQUEST['tool']."'. Available are: ".implode(", ",$tool['analyses']);
	?><script>window.history.back();</script><?php //var_dump($_SESSION['errorData']);unset($_SESSION['errorData']);
	//exit(0);
    }
    $cmd =createCmd($analysisId,$analisisParms);
    if($cmd == "0"){
    	$_SESSION['errorData']['Error'][]="Cannot set the tool command line. Execution stopped.";
	?><script>window.history.back();</script><?php //var_dump($_SESSION['errorData']);unset($_SESSION['errorData']);
	 //exit(0);
		
    }
    $cmdParams[$analysisId]=$cmd;
    var_dump($cmd);
    echo "<br/>";
}

//
// Gathering File Info

$files   = Array();
for ($i=0;$i<count($_REQUEST['fn']);$i++){
    $files[$i]['id']=$_REQUEST['fn'][$i];
}

for ($i=0; $i<count($files); $i++){
    $fnId = $files[$i]['id'];
    $fn  = getAttr_fromGSFileId($fnId,'path');
    $rfn = $GLOBALS['dataDir']."/$fn";
    $fileData = $GLOBALS['filesMetaCol']->findOne(array('_id' => $fnId));
    if (empty($fileData) ){
        $_SESSION['errorData']['Error'][]="File '".basename($fn)."' not found in the database anymore. Stopping execution";
	?><script>window.history.back();</script><?php //var_dump($_SESSION['errorData']);unset($_SESSION['errorData']);
	 //exit(0);
    }
    if (! is_file($rfn)){
        $_SESSION['errorData']['Error'][]="File '".basename($fn)."' selected as input is not found, or has size zero. Stopping execution";
	?><script>window.history.back();</script><?php //var_dump($_SESSION['errorData']);unset($_SESSION['errorData']);
	 //exit(0);
    }
    $files[$i]['fn']  = $fn;
    $files[$i]['rfn'] = $rfn;
}

//
// Creating working dir

if (!file_exists($wd)){
        $dirId = createGSDirBNS($wdFN);
        if ($dirId=="0"){
    		$_SESSION['errorData']['Error'][]="Cannot create project folder: '$wdFN'";
    		?><script>window.history.back();</script><?php //var_dump($_SESSION['errorData']);unset($_SESSION['errorData']);
		//exit(0);
	}
	mkdir($wd,0777);
	chmod($wd, 0777);

	//store directory metadata

        $projDirMeta=array('description' => $jobMeta['description'],
			'inPaths'    => $jobMeta['inPaths'],
			'tool'       => $jobMeta['tool'],
			'analyses'   => array_keys($_REQUEST['params']),
			'raw_params' => $_REQUEST['params']
	);
	$r = addMetadataBNS($dirId, $projDirMeta);
        if ($r == "0"){
    		$_SESSION['errorData']['Error'][]="Project folder created. But cannot set project folder: ".basename($wdFN);
    		?><script>window.history.back();</script><?php //var_dump($_SESSION['errorData']);unset($_SESSION['errorData']);
		//exit(0);
	}
}
chdir($wd);
echo "<br/>WD CREATED SCCESSFULLY AT: $wd<br/>";


echo "<br/>LAUNCH TOOL with JOB METADATA:<br/>";
var_dump($jobMeta);
echo "<br/> ... AND  TOOL CMD:<br/>";
var_dump($cmdParams);
print "<br/>";
## EL ERORR NO TE TITLE !!!! Q MES FALTA?¿!


$r = launchTool($jobMeta,$cmdParams);
if ($r == "0"){
	$_SESSION['errorData']['Error'][]="Cannot launch ".$jobMeta['tool'];
	print "ERROR DATA IS <br/>";
	var_dump($_SESSION['errorData']);
	var_dump($r);
	exit(0);
	redirect($_REQUEST['tool']."/input.php");
}

echo "<br/>AFTER LAUNCHING JOB META IS:<br/>";
var_dump($jobMeta);
print "<br/>";


#saveUserJobs($_SESSION['User']['id'],$jobMeta);
addUserJob($_SESSION['User']['_id'],$jobMeta,$jobMeta['_id']);

print "<br> ERROR DATA<br/>";
print "<br/>";
var_dump($_SESSION['errorData']);
unset($_SESSION['errorData']);
print "<br/>";

//redirect("/workspace/");

?>
