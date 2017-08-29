<?php

$GLOBALS['AppPrefix'] = "MuG";
$GLOBALS['BASEURL'] = "/";
$GLOBALS['URL'] = "http://multiscalegenomics.bsc.es/vre/";
$GLOBALS['NAME'] = "MuG Virtual Research Environment";
$GLOBALS['SITETITLE'] = "Multiscale Complex Genomics | Virtual Research Environment";
$GLOBALS['FROMMAIL'] = "irbinfo.mug@irbbarcelona.org";
$GLOBALS['FROMNAME'] = "MuG VRE";
$GLOBALS['ROOTPATH'] = $_SERVER['DOCUMENT_ROOT'];
$GLOBALS['helpdeskMail'] = "helpdesk@multiscalegenomics.eu";

$GLOBALS['logFile'] = "/gpfs/VRE.log";

// roles
//$GLOBALS['ROLES'] = array("0"=>"Admin", "1"=>"Tool Dev.", "2"=>"Common", 100=>"Premium Rq", 101=>"Premium Rj");
$GLOBALS['ROLES'] = array("0"=>"Admin", "1"=>"Tool Dev.", "2"=>"Common");
$GLOBALS['ROLES_COLOR'] = array("0"=>"blue", "1"=>"grey-cascade", "2"=>"", 100=>"red-haze", 101=>"yellow-haze");
$GLOBALS['STATES_COLOR'] = array("0"=>"font-red", "1"=>"font-green-meadow", "2"=>"font-blue-steel", 3=>"font-green-meadow", 4=>"font-yellow-mint");
$GLOBALS['FILE_MSG_COLOR'] = array("0"=>"note-danger", "1"=>"note-info", "2"=>"note-success", 3=>"note-info");
$GLOBALS['NO_GUEST'] = array(0,1,2,100,101);
$GLOBALS['PREMIUM'] = array(0,1);
$GLOBALS['ADMIN'] = array(0,1);
$GLOBALS['TOOLDEV'] = array(1);


//SGE
$GLOBALS['queueTask']= "srv.q"; //default
$GLOBALS['queueMaxMem']= "7.9G"; //not used. Default: no_max_mem
define ("QSUB", ". /usr/local/sge/environment/settings.sh; /usr/local/sge/bin/lx24-amd64/qsub -S /bin/bash" );
define ("QDEL", ". /usr/local/sge/environment/settings.sh; /usr/local/sge/bin/lx24-amd64/qdel ");
define ("QSTAT", ". /usr/local/sge/environment/settings.sh; /usr/local/sge/bin/lx24-amd64/qstat ");
define ("SGE_ROOT","/usr/local/sge");


//load in mongo?
//$GLOBALS['tools'] = Array(
//		'validation' => Array( "prefix" => "UP",  "title" => "Validation"         , "description"=>""),
//		'ND'         => Array( "prefix" => "ND",  "title" => "Nucleosome Dynamics", "description"=>""),
//		'NR'         => Array( "prefix" => "NR",  "title" => "NucleR positioning" , "description"=>""),
//		'NFR'        => Array( "prefix" => "NFR", "title" => "Nucleosome Free Regions", "description"=>""),
//		'P'          => Array( "prefix" => "TSS", "title" => "TSS classification" , "description"=>""),
//		'STF'        => Array( "prefix" => "P",   "title" => "Phasing analysis"   , "description"=>""),
//		'PP'         => Array( "prefix" => "STF", "title" => "Stiffness"          , "description"=>""),
//		''           => Array( "prefix" => "PP",  "title" => "Preprocessing"      , "description"=>""),
//	);

//datamanager
$GLOBALS['DISKLIMIT'] = 20*1024*1024*1024;
$GLOBALS['MAXSIZEUPLOAD'] = 15000;


?>
