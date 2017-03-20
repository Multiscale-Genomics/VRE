<?php

require "../phplib/genlibraries.php";
require "../phplib/tools.inc.php";
require "../phplib/Tooljob.php";

redirectOutside();


$debug=0;

$SGE_updated = getUserJobs($_SESSION['User']['id']);

if ($debug){
	print "</br>REQUEST FILES ARE [FN]:</br>";
	var_dump($_REQUEST['fn']);
	print "<br/>";
	print "</br>RAW ARGUMENTS ARE<br/>";
	var_dump($_REQUEST['arguments']);
	print "<br/>";
	print "</br>RAW  INPUT_FILES ARE<br/>";
	var_dump($_REQUEST['input_files']);
	print "<br/>";
	foreach ($_REQUEST as $k=>$v){
	if ($k!="arguments" && $k!="input_files" && $k!="fn"){
		print "<br/><br/>REQUEST[$k]</br>";
		var_dump($v);
	}
	}
}

//
// Get tool.

$tool = getTool_fromId($_REQUEST['toolId'],1);
if (empty($tool)){
	$_SESSION['errorData']['Error'][]="Tool not specified or not registered. Please, register '".$_REQUEST['toolId']."'";
    	redirect('/workspace/');
}

//
// Set Tooljob


$jobMeta  = new Tooljob($tool,$_REQUEST['project'],$_REQUEST['description']); 

if ($debug){
	print "<br/>NEW TOOLJOB SET:</br>";
	var_dump($jobMeta);
}


//
// Check input file requirements

if (!isset($_REQUEST['input_files'])){
    $_SESSION['errorData']['Error'][]="Tool is not receiving input files. Please, select them from your workspace table.";
    redirect('/workspace/');
}



//
// Get medatada files (with associated_files)

$files   = Array(); // distinct file Objs to stage in 


$filesId = Array();
foreach($_REQUEST['input_files'] as $input_file){
    if (is_array($input_file))
	$filesId = array_merge($filesId,$input_file);
    else
	array_push($filesId,$input_file);
}
$filesId=array_unique($filesId);


foreach ($filesId as $fnId){
    $file = getGSFile_fromId($fnId);
    if (!$file){
        $_SESSION['errorData']['Error'][]="Input file $fnId does not belong to current user or has been not properly registered. Stopping execution";
    	redirect('/workspace/');
    }
    $files[$file['_id']]=$file;
    $associated_files = getAssociatedFiles_fromId($fnId);
	var_dump($associated_files);
    foreach ($associated_files as $assocId){
	$assocFile = getGSFile_fromId($assocId);
    	if (!$assocFile){
        	$_SESSION['errorData']['Error'][]="File associated to ".basename($file['path'])." ($assocId) does not belong to current user or has been not properly registered. Stopping execution";
		redirect('/workspace/');
	}
    	$files[$assocFile['_id']]=$assocFile;
    }
	
}

if ($debug){
	print "<br/></br>TOTAL number of FILES given as params : ".count($filesId);
	print "<br/></br>TOTAL number of FILES (including associated) : ".count(array_keys($files))."</br>";
}

//
// Stage in (fake)  TODO


//
// Checking files locally

foreach ($files as $fnId => $file) {

    $fn   = getAttr_fromGSFileId($fnId,'path');
    $rfn  = $GLOBALS['dataDir']."/$fn";
    if (! is_file($rfn)){
        $_SESSION['errorData']['Error'][]="File '".basename($fn)."' is not found or has size zero. Stopping execution";
    	redirect('/workspace/');
    }

}


//
// Set Arguments
$jobMeta->setArguments($_REQUEST['arguments']);


//
// Set InputFiles
$jobMeta->setInput_files($_REQUEST['input_files'],$tool,$files);

if ($debug){
	print "<br/>TOOL Input_files are:</br>";
	var_dump($jobMeta->input_files);
}
if ($jobMeta->input_files == 0)
    	redirect('/workspace/');


//
// Create working_dir
$jobId = $jobMeta->createWorking_dir();

if ($debug)
	echo "<br/></br>WD CREATED SCCESSFULLY AT: $jobMeta->working_dir<br/>";

if (!$jobId)
    	redirect('/workspace/');


//
// Setting Command line. Adding parameters


$r = $jobMeta->prepareExecution($tool,$files);

if ($debug)
	echo "<br/></br>PREPARE EXECUTION RETURNS ($r). <br/>";

if($r == 0)
    	redirect('/workspace/');


//
// Launching Tooljob

$pid = $jobMeta->submit($tool);		

if ($debug)
	echo "<br/></br>JOB SUBMITTED. PID = $pid<br/>";

if($pid == 0)
    	redirect('/workspace/');


if ($debug){
	print "<br/>ERROR_DATA<br/>";
	var_dump($_SESSION['errorData']);
	unset($_SESSION['errorData']);
	print "</br><br/>JOB_META END<br/>";
	var_dump((array)$jobMeta);
}


//TODO redirect to inputs.php instead of workspace

$jobMeta2= array(
            "inPaths" => Array("MuGUSER57ecf22d91df3/uploads/1diz_dna.pdb", "MuGUSER57ecf22d91df3/uploads/1diz_protein.pdb"),
            "tool"    =>  $jobMeta->toolId,
            "description" =>  $jobMeta->description,
            "outDir"  =>  str_replace($GLOBALS['dataDir']."/","",$jobMeta->working_dir),
            "project" =>  $jobMeta->project,
            "title"   =>  $jobMeta->tool->title,
            "outPaths"=>  Array(),
            "metaData"=>  Array(),
            "outCtrl" =>  str_replace($GLOBALS['dataDir']."/","",$jobMeta->stageout_file),
            "shPath"  =>  str_replace($GLOBALS['dataDir']."/","",$jobMeta->submission_file),
            "logPath" =>  str_replace($GLOBALS['dataDir']."/","",$jobMeta->log_file),
            "_id"     =>  $jobMeta->pid,
            "state"   =>  "PENDING"
);

#saveUserJobs($_SESSION['User']['id'],$jobMeta);
addUserJob($_SESSION['User']['_id'],(array)$jobMeta,$jobMeta->pid);

if ($debug)
	exit(0);

redirect("/workspace/");

?>
