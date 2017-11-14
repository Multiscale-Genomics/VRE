<?php

$GLOBALS['fsStyle'] = "fsMongo"; # fsMongo,fs,mongo 

//VRE installation paths
$GLOBALS['shared']     = "/orozco/services/MuG_dev/";
$GLOBALS['dataDir']    = $GLOBALS['shared']."MuG_userdata/";
$GLOBALS['pubDir']     = $GLOBALS['shared']."MuG_public/";
$GLOBALS['appsDir']    = $GLOBALS['shared']."apps/soft/";
$GLOBALS['tmpDir']     = $GLOBALS['shared']."tmp/";
$GLOBALS['refGenomes'] = $GLOBALS['pubDir']."refGenomes/";
$GLOBALS['sampleData'] = $GLOBALS['shared']."sampleData";
$GLOBALS['logFile']    = $GLOBALS['shared']."VRE.log";

$GLOBALS['cloud']              = "mug-irb"; # options are any of $GLOBALS['clouds']
$GLOBALS['tmpUser_dir']       = ".tmp/";
$GLOBALS['sampleData_default'] = "basic";


//Html templates: footers, headers, datatables
$GLOBALS['htmlib'] = "/var/www/html/htmlib";

//File manager config
$GLOBALS['caduca']            = "90"; //days
//$GLOBALS['disklimit']         = 12*1024*1024*1024;
//$GLOBALS['disklimitAnon']     = 4*1024*1024*1024;
//$GLOBALS['limitFileSize']     = '900M';
//$GLOBALS['max_execution_time']= 2000;

//File names of tool executions
$GLOBALS['tool_config_file']     = ".config.json";
$GLOBALS['tool_submission_file'] = ".submit";
$GLOBALS['tool_log_file']        = ".tool.log";
$GLOBALS['tool_stageout_file']   = ".results.json";
$GLOBALS['tool_metadata_file']   = ".input_metadata.json";

//Visualizers
$GLOBALS['jbrowseURL']  = $GLOBALS['URL']."/visualizers/jbrowse/";


//Oauth2 authentification
$GLOBALS['authServer']             = 'https://inb.bsc.es/auth';
$GLOBALS['urlAuthorize' ]          = 'https://inb.bsc.es/auth/realms/mug/protocol/openid-connect/auth';     //get autorization_code
$GLOBALS['urlAccessToken']         = 'https://inb.bsc.es/auth/realms/mug/protocol/openid-connect/token';    //get token
$GLOBALS['urlResourceOwnerDetails']= 'https://inb.bsc.es/auth/realms/mug/protocol/openid-connect/userinfo'; //get user details
$GLOBALS['urlLogout']              = 'https://inb.bsc.es/auth/realms/mug/protocol/openid-connect/logout';   //close keyclok session   

//MuG DMP metdata API 
$GLOBALS['DMPserver_domain']       = 'localhost';
$GLOBALS['DMPserver_port']         = '5002';
$GLOBALS['DMPserver_address']      = '/mug/api/dmp';

//
//

// Reference Genomes
$GLOBALS['refGenomes_names'] = Array(
		'R64-1-1' => "Saccharomyces cerevisiae (R64-1-1)",
		'hg19'    => "Homo Sapiens (hg19 / GRCh37)",
		'hg38'    => "Homo Sapiens (hg38 / GRCh38)",
		'r5.01'   => "Drosophila Melanogaster (r5.01)",
		'UNK'     => "Other"
	);

// MuG file. Accepted values for 'compression' attribute
$GLOBALS['compressions'] = Array(
		"zip"  => "ZIP",
		"bz2"  => "BZIP2",
		"gz"   => "GZIP",
		"tgz"  => "TAR,ZIP",
		"tbz2" => "TAR,BZIP2"
	);

// MuG cloud infrastructures
$GLOBALS['clouds'] = Array(
		'mug-bsc' => array(
			"http_host"         => "multiscalegenomics.bsc.es" ,               // used in getCurrentCloud
			"dataDir_fs"        => "/data/cloud/apps/noroot/mug/MuG_userdata", //export path for NFS server
			"pubDir_fs"         => "/data/cloud/apps/noroot/mug/MuG_public",   //export path for NFS server
			"dataDir_virtual"   => "/MUG_USERDATA",
			"pubDir_virtual"    => "/MUG_PUBLIC",
			"PMESserver_domain" => "multiscalegenomics.bsc.es",
			"PMESserver_port"   => "80",
			"PMESserver_address"=> "pmes/"
			),
		'mug-irb' => array(
			"http_host"         => "dev.multiscalegenomics.eu",         // used in getCurrentCloud
			"dataDir_fs"        => "/NAmmb5/services/MuG/MuG_userdata", //export path for NFS server
			"pubDir_fs"         => "/NAmmb5/services/MuG/MuG_public",   //export path for NFS server
			"dataDir_virtual"   => "/orozco/services/MuG/MuG_userdata",
			"pubDir_virtual"    => "/orozco/services/MuG/MuG_public",
			"PMESserver_domain" => "192.168.11.236",
			"PMESserver_port"   => "8080",
			"PMESserver_address"=> "pmes/"
			),
		'mug-ebi' => array(
			"http_host"        => "what.ever.uk",
			"dataDir_virtual"   => "/MUG_USERDATA",
			"pubDir_virtual"    => "/MUG_PUBLIC"
			)
);
?>
