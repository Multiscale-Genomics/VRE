<?php

/*
 *  IN PROGRESS
 *
 *  Given a directory file id, lists the metadata of each of the child files/directories
 *  following the format of the sample_metadata files.
 *  No recursive.
 *
 */

require "phplib/genlibraries.php";

/*
 * IMPORTANT:
 * - execute with sudo to be able to 'rm' www-data data : sudo php applib/cleanUsersData.php
 * - make sure that mongo 'userscol'(phplib/db.inc.php) and 'shared' global are in agreement (dev/prod)
 *
 */


// params
array_shift($argv);
$parentId = $argv[0];
$metadata_rfn = ($argv[1]?$argv[1]:getcwd()."/sample_metadata.json");

if (!isset($parentId)){
    fwrite(STDERR, "\nUSAGE: applib/prepareSampleMetadata.php [FILE_ID]\n  FILE_ID: parent directory of the files/dirs whose metadata is to written\n\n");
    exit(0);
}

// disk
$GLOBALS['shared']     = "/orozco/services/MuG/"; // MuGdev/";
$GLOBALS['dataDir']    = $GLOBALS['shared']."MuG_userdata/";

// db
$GLOBALS['dbname_VRE']  = "MuGVRE_irb";
require "phplib/db.inc.php";


// identification
$u = checkUserIDExists("MuGUSER59e5ead574743");
if (!$u){
    fwrite(STDERR, "Admin user with ID=MuGUSER59e5ead574743 do not exists in ".$GLOBALS['dbname_VRE'].". Change it in this script!");
    exit(0);
}
if (!isset($_SESSION['User'])){
	$_SESSION['User'] = $u;
}

////////////////////////////////////


function fromVREfile_toMUGfile($file) {

        $mugfile        = array();
        $compressions   = $GLOBALS['compressions'];

        //path -> file_path
        if (isset($file['path'])){
	    if (preg_match('/'.$GLOBALS['AppPrefix'].'[^\/]*\/[^\/]*\/(.*)$/', $file['path'],$m)){
            	$mugfile['file_path'] = $m[1];
            }else{
                $mugfile['file_path'] = $file['path'];
            }
        }else{
            $mugfile['file_path'] = "";
	}

      	// type -> type
	if (isset($file['type'])){
		$mugfile['type'] = $file['type'];
	}else{
		$mugfile['type'] = "file";
	}

        if ($mugfile['type'] != "dir" ){

        	// format -> file_type
        	if (isset($file['data_type'])){
	            $mugfile['file_type'] = $file['format'];
	        }else{
	            $mugfile['file_type'] = "";
	        }
	        // data_type -> data_type
	        if (isset($file['data_type'])){
	            $mugfile['data_type'] = $file['data_type'];
	        }else{
	            $mugfile['data_type'] = "";
	        }
		// taxon_id -> taxon_id
	        if (isset($file['taxon_id'])){
		    $mugfile['taxon_id'] = (int)($file['taxon_id']);
		}else{
		    $mugfile['taxon_id'] = 0;
		}
	}

        // input_files -> source_id 
        if (isset($file['input_files'])){
	    if (basename($mugfile['file_path']) == "uploads" || dirname($mugfile['file_path']) == "uploads" ){
            	$mugfile['source_id'] = array(0);
	    }else{
		if (!is_array($file['input_files'])){
                	$file['input_files'] = array($file['input_files']);
		}
		$mugfile['source_id']=array();
		foreach ($file['input_files'] as $fn){
			$f = getAttr_fromGSFileId($fn,"path",1);
			if (preg_match('/'.$GLOBALS['AppPrefix'].'[^\/]*\/[^\/]*\/(.*)$/', $f,$m)){
				array_push($mugfile['source_id'],$m[1]);
			}else{
				array_push($mugfile['source_id'],$f);
			}
		}
	   }
        }else{
           $mugfile['source_id'] = array(0);
        }

        unset($file['_id']);
        unset($file['type']);
        unset($file['path']);
        unset($file['format']);
        unset($file['taxon_id']);
        unset($file['data_type']);
        unset($file['owner']);
        unset($file['mtime']);
        unset($file['atime']);
        unset($file['size']);
        unset($file['project']);
        unset($file['tracktype']);
        unset($file['input_files']);
        unset($file['owner']);
	unset($file['parentDir']);
	unset($file['associated_id']);
	unset($file['validated']);

        // other -> meta_data
        $mugfile['meta_data']  = $file;

        // compressed -> compressed
	if (isset($mugfile['file_path'])){
            $ext = pathinfo($mugfile['file_path'], PATHINFO_EXTENSION);
            $ext = preg_replace('/_\d+$/',"",$ext);
            $ext = strtolower($ext);
            if (in_array($ext,array_keys($compressions))){
                $mugfile['meta_data']['compressed'] = $compressions[$ext];
            }else{
                if (isset($mugfile['meta_data']['compressed'])){unset($mugfile['meta_data']['compressed']);}
	    }
        }

        // meta submission_file -> submission_file path
        if (isset($mugfile['meta_data']['submission_file'])){
		if (preg_match('/'.$GLOBALS['AppPrefix'].'[^\/]*\/__PROJ[^\/]*\/(.*)$/', $mugfile['meta_data']['submission_file'],$m)){
			if (! preg_match('/\.tmp/',$m[1])){
			    $mugfile['meta_data']['submission_file'] = $m[1];
			}else{
			    unset($mugfile['meta_data']['submission_file']);
			}
		}
	}

        // meta log_file -> log_file path
        if (isset($mugfile['meta_data']['log_file'])){
		if (preg_match('/'.$GLOBALS['AppPrefix'].'[^\/]*\/__PROJ[^\/]*\/(.*)$/', $mugfile['meta_data']['log_file'],$m)){
			if (! preg_match('/\.tmp/',$m[1])){
			    $mugfile['meta_data']['log_file'] = $m[1];
			}else{
			    unset($mugfile['meta_data']['log_file']);
			}
		}
	}

        // meta files -> []
        if (isset($mugfile['meta_data']['files'])){
		$mugfile['meta_data']['files'] = array();
	}
	// expiration -> -1
        if (isset($mugfile['meta_data']['expiration'])){
		$mugfile['meta_data']['expiration'] = -1;
	}
	// refGenome -> assembly
        if (isset($mugfile['meta_data']['refGenome'])){
		$mugfile['meta_data']['assembly'] = $mugfile['meta_data']['refGenome'];
		unset($mugfile['meta_data']['refGenome']);
	}
	// associated_files -> associated_files path
        if (isset($mugfile['meta_data']['associated_files'])){
		$arr_t=array();
		foreach ($mugfile['meta_data']['associated_files'] as $fn){
	  	    $f = getAttr_fromGSFileId($fn,"path",1);
		    if (preg_match('/'.$GLOBALS['AppPrefix'].'[^\/]*\/__PROJ[^\/]*\/(.*)$/', $f,$m)){
			array_push($arr_t,$m[1]);
		    }
		}
		$mugfile['meta_data']['associated_files']=$arr_t;
	}
        return $mugfile;
}



///////////////////////////////////////////////

// get files from given parentId

print ">> Parent ID: $parentId\n";

$filesId = getAttr_fromGSFileId($parentId,"files",1);

if (!$filesId){
	fwrite(STDERR, "Given parent ID has no files");
    	$file = getGSFile_fromId($parentId,"",1);
	var_dump($file);
	exit(0);
}

// get file info from DB

print "\nBuilding files list\n";
$files = array();

foreach ($filesId as $fnId){
    $file = getGSFile_fromId($fnId,"",1);
    if (!$file){
        $_SESSION['errorData']['Error'][]="Input file $fnId does not belong to current user or has been not properly registered. Stopping execution";
	next;
    }
    $associated_files = getAssociatedFiles_fromId($fnId);
    foreach ($associated_files as $assocId){
        $assocFile = getGSFile_fromId($assocId,"",1);
        if (!$assocFile){
            $_SESSION['errorData']['Error'][]="File associated to ".basename($file['path'])." ($assocId) does not belong to current user or has been not properly registered. Stopping execution";
	    next;
        }
	if (!isset($files[$assocFile['_id']])){
	        $files[$assocFile['_id']]=$assocFile;
	    	print "-- ".$assocFile['_id']." : ".basename($assocFile['path'])."(associated file)\n";
	}
    }
    if (!isset($files[$file['_id']])){
	    print "-- $fnId : ".basename($file['path'])."\n";
	    $files[$file['_id']]=$file;
    }
}

// add input_files metadata

print "\nCreting metadata obj for each file\n";
$fileMuGs=Array();

foreach ($files as $fnId => $file){

	print "-- $fnId converting...\n";

	// convert metadata to DMP format
	$fileMuG = fromVREfile_toMUGfile($file);
	if (!$fileMuG){
		$_SESSION['errorData']['Error'][]="Cannot create MuG file for $fnId";
		continue;
	}
  	array_push($fileMuGs,$fileMuG);

	if (isset($file['files'])){
		print "   AFTERWARDS RUN: applib/prepareSampleMetadata.php $fnId ".basename($file['path'])."_sample_metadata.json\n";
	}
}

// write JSON

print "\nWritting metadata objs into file\n";
try{
     $F = fopen($metadata_rfn,"w");
     if (!$F) {
            throw new Exception('Failed to create metadata file for tool execution'.$metadata_rfn);
     }
}
catch (Exception $e){
    $_SESSION['errorData']['Internal Error'][]= $e->getMessage();
    exit(0);
}

fwrite($F, json_encode($fileMuGs,JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
fclose($F);

print "$metadata_rfn written\n\n";



// print errors

if (isset($_SESSION['errorData'])){
	print "\nERRORDATA:\n";
	var_dump($_SESSION['errorData']);
    	$_SESSION['errorData']=array();
}
