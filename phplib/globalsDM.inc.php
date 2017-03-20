<?php

//$GLOBALS['logFile']  = "/orozco/services/Rdata/Web/NucDyn-dev.log";

$GLOBALS['fsStyle'] = "fsMongo"; # fsMongo,fs,mongo 


//lib paths
//$GLOBALS['baseDir']  = $_SERVER['DOCUMENT_ROOT']."/datamanager/";
//$GLOBALS['htmlib']   = $GLOBALS['baseDir']."/htmlib";
//$GLOBALS['classlib'] = $GLOBALS['baseDir']."/phplib/classes";

//file paths
$GLOBALS['shared']     = "/orozco/services/Rdata/MuG/";
$GLOBALS['dataDir']    = $GLOBALS['shared']."MuG_userdata/";

$GLOBALS['tmpDir']     = $GLOBALS['shared']."MuG_userdata/";
$GLOBALS['refGenomes'] = $GLOBALS['shared']."refGenomes";
$GLOBALS['sampleData'] = $GLOBALS['shared']."sampleData";

//jbrowse
$GLOBALS['jbrowseData']= $GLOBALS['shared']."/visualizers/jbrowse/data/";//"/orozco/services/Rdata/Web/JBrowse/data";
$GLOBALS['absURL']     = $GLOBALS['URL']."visualizers/jbrowse/";


//datamanger templates
$GLOBALS['htmlib'] = "/var/www/html/htmlib";

//
$GLOBALS['caduca']            = "40"; //days
$GLOBALS['disklimit']         = 12*1024*1024*1024;
$GLOBALS['disklimitAnon']     = 4*1024*1024*1024;
$GLOBALS['limitFileSize']     = '900M';
$GLOBALS['max_execution_time']= 2000;

// tool submission
$GLOBALS['tool_config_file']     = ".config.json";
$GLOBALS['tool_submission_file'] = ".enqueue.sh";
$GLOBALS['tool_log_file']        = ".tool.log";
$GLOBALS['tool_stageout_file']   = ".results.json";
$GLOBALS['tool_metadata_file']   = ".metadata.json";



$GLOBALS['refGenomes_names'] = Array(
		'R64-1-1' => "Saccharomyces cerevisiae (R64-1-1)",
	);

$GLOBALS['internalResults']  = Array(
		"RDATA",
		"COV",
		"BAI"
	);

?>
