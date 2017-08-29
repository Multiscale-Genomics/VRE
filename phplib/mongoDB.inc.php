<?php

function isGSDirBNS($col, $fn) {
	$file = $col->findOne(array('_id'  => $fn,
			'files' => array('$exists' => true)
		   )
	);
	if (empty($file)){
		return false;
	}else{
		return true;
	}
}

function getGSFilesFromDir($dataSelection=Array(),$onlyVisible=0){

	$files=Array();

        if (count($dataSelection) == 0 ){
                if (!isset($_SESSION['curDir'])){
                        $_SESSION['errorData']['internal'][]="Cannot retrieve files from the database. Given query is not valid. Please, try it later or mail <a href=\"mailto:".$GLOBALS['helpdeskMail']."\">".$GLOBALS['helpdeskMail']."</a>";
                        return FALSE;
                }
                $dataSelection = Array(
                        'owner' => $_SESSION['userId'],
                        'path'  => $_SESSION['curDir']
                );
        }
        $dirData = $GLOBALS['filesCol']->findOne($dataSelection);


        if (!isset($dirData['_id'])){
                $_SESSION['errorData']['Error'][]="Data is not accessible or do not exist anymore. Please, try it later or mail <a href=\"mailto:".$GLOBALS['helpdeskMail']."\">".$GLOBALS['helpdeskMail']."</a>";
                return FALSE;
        }

        if (!isset($dirData['files']) || count($dirData['files'])==0 ){
                $_SESSION['errorData']['Warning'][]="No data to display in the given directory.";
                return FALSE;
	}

        // Retrieve File Data and Metada for each file in directory
        $count =count( $dirData['files']);

	foreach ($dirData['files'] as $d) {

	    if ($onlyVisible)
		    $fData = getGSFiles_filteredBy($d, array('visible'=> Array('$ne'=>false)) );	
	    else
	    	    $fData = getGSFile_fromId($d);

	    if ( $fData['path'] == $_SESSION['User']['id'] )
		    continue;
	    $fData['mtime'] = $fData['mtime']->sec;
	    $files[$fData['_id']] = $fData; 
	    if (isset($fData['files']) && count($fData['files'])>0 ){
		foreach ($fData['files'] as $dd) {

	    		if ($onlyVisible)
			    $ffData = getGSFiles_filteredBy($dd, array('visible'=> Array('$ne'=>false)) );	
			else
	    	   	     $ffData = getGSFile_fromId($dd);

			if (is_object($ffData['mtime']))
				$ffData['mtime'] = $ffData['mtime']->sec;
	    		$files[$ffData['_id']] = $ffData; 
		}
	    }
	}
	return $files;

}

function getGSFileId_fromPath($fnPath,$asRoot=0) {
	$col = $GLOBALS['filesCol'];
	if ($asRoot){
		$file = $col->findOne(array('path'  => $fnPath));
	}else{
		$file = $col->findOne(array('path'  => $fnPath,
				    'owner' => $_SESSION['User']['id']
		));
	}
	if (empty($file)){
		return 0;
	}else{
//		if (count($file) > 1){
//			$_SESSION['errorData']['mongoDB'][]="Multiple files objects pointing to the path $fnPath";
//		}
		return $file['_id'];
	}
}

function getGSFile_fromId($fn,$filter="",$asRoot=0) {
    if ($asRoot)
        $fileData = $GLOBALS['filesCol']->findOne(array('_id' => $fn) );
    else
    	$fileData = $GLOBALS['filesCol']->findOne(array('_id' => $fn, 'owner' => $_SESSION['User']['id']));
    $fileMeta = $GLOBALS['filesMetaCol']->findOne(array('_id' => $fn));

    if($filter == "onlyMetadata"){
        if (empty($fileMeta))
            return 0;
        return $fileMeta;
    
    }elseif($filter == "onlyData"){
        if (empty($fileData))
            return 0;
        return $fileData;
    
    }else{
        if (empty($fileData))
            return 0;
        if(!isset($fileMeta)) $fileMeta = array();
        return array_merge($fileData,$fileMeta);
    }

}

function getGSFiles_filteredBy($fn,$filters) {

	$filter_filesCol     = Array('_id' => $fn);
	$filter_filesMetaCol = Array('_id' => $fn);
	foreach ($filters as $attr => $v){
		if (in_array($attr, Array('owner', 'size', 'path', 'mtime', 'parentDir', 'expiration')) )
			$filter_filesCol[$attr] = $v;
		else
			$filter_filesMetaCol[$attr] = $v;
	}	
	$fileData = $GLOBALS['filesCol']->findOne($filter_filesCol);
	$fileMeta = $GLOBALS['filesMetaCol']->findOne($filter_filesMetaCol);
	$existMeta= $GLOBALS['filesMetaCol']->findOne(Array('_id' => $fn));

	if (empty($fileData))
		return 0;

	if (empty($existMeta))
		return $fileData;

	elseif (empty($fileMeta))
		return 0;
	else
		return array_merge($fileData,$fileMeta);	
	
}

function addAssociatedFiles($masterId,$assocIds) {

	$meta_master  = $GLOBALS['filesMetaCol']->findOne(array('_id' => $masterId));
	if (!isset($meta_master['associated_files']))
		$meta_master['associated_files']=array();

	// update associated files metadata
	foreach ($assocIds as $assocId){
		array_push($meta_master['associated_files'],$assoc);
		addMetadataBNS($assocId,array('associated_id'=>$masterId) );
	}
	// update master file metadata
	modifyMetadataBNS($masterId,$meta_master);
	return 1;
}

function getAssociatedFiles_fromId($fn,$assoc=Array()) {
	if (in_array($fn,$assoc))
		return $assoc;

	$f  = getGSFile_fromId($fn);
	if (isset($f['associated_files'])){
		foreach ($f['associated_files'] as $a){
			$assoc = getAssociatedFiles_fromId($a,$assoc);
			array_push($assoc,$a);
		}
		return $assoc;
	}else{
		return $assoc;
	}
}

function getAttr_fromGSFileId($fnId,$attr) {
	//$f = $GLOBALS['filesCol']->findOne(array('_id' => $fnId));
	$f = getGSFile_fromId($fnId);
	if (empty($f))
		return false;
	elseif (!isset($f[$attr]) )
		return false;
	else
		return $f[$attr];
}


function getSizeDirBNS($dir){
	$s=0;
	$dirObj= $GLOBALS['filesCol']->findOne(array('_id' => $dir, 'owner'=>$_SESSION['User']['id'] ));
	if (empty($dirObj) || !isset($dirObj['files']) ){
	$_SESSION['errorData']['mongoDB'][] = $dir ." directory has no files<br/>";
		return 0;
	}
	$files = $dirObj['files'];
	foreach ($files as $child){
	$childObj= $GLOBALS['filesCol']->findOne(array('_id' => $child));
	if (empty($childObj))
		continue;
	if ( isset($childObj['files']) ){
		$s += getSizeDirBNS($child);
	}else{
		$s += $childObj['size'];
	}
	}
	return $s; 
}

/*
function moveGSFileBNS($fn,$fnNew){

	list($f,$r)      = absolutePathGSFile($fn);
	list($fnNew,$r1) = absolutePathGSFile($fnNew);
	

	$fileNew= $GLOBALS['filesCol']->findOne(array('_id' => $fnNew));
	if ( !empty($fileNew))
		return 1;

	$fileOld= $GLOBALS['filesCol']->findOne(array('_id' => $fn, 'owner'=>$_SESSION['User']['id']) );
	if ( empty($fileOld)){
		$_SESSION['errorData']['mongoDB'][] = "Cannot move file ".$fn." . It does not exist in your workspace<br/>";
		return 0;
	}

	if (isset($fileOld['permissions']) && $fileOld['permissions']== "000" ){
		$_SESSION['errorData']['mongoDB'][]= "Not permissions to move $fn";
		return 0;
	}
	//set new parent
	$parentNew = dirname($fnNew);
	if ( ! isGSDirBNS($GLOBALS['filesCol'],$parentNew) ){
		$r = createGSDirBNS($parentNew);
		if ( $r== 0 ){
				$_SESSION['errorData']['mongoDB'][] = "Cannot move file ".$fn." to ".$fnNew." . Cannot create parent directory $parentNew <br/>";
				return 0;
		}
	}

	// change filename and _id
	$infoOld=Array();
	foreach($fileOld as $k => $v) {
		if($k == '_id') {
			$infoOld[$k] = $fnNew;
		}elseif ($k == 'parentDir'){
			$infoOld[$k] = $parentNew;
		}else{
			$infoOld[$k] = $v;
		}
	}
	if (! isset($infoOld['parentDir']))
		$infoOld['parentDir'] = $parentNew;


	$GLOBALS['filesCol']->update(
				array('_id' => $fnNew),
				$infoOld,
				array('upsert'=> 1)
		);
	$fileNew = $GLOBALS['filesCol']->findOne(array('_id' => $fnNew));
	if ( empty($fileNew) ){
		$err="";
		foreach($infoOld as  $k => $v) {
			$err .= "'$k'='$v', "; 
 		}
		//print "Cannot update metadata. Internal error: $err<br/>";
		$_SESSION['errorData']['mongoDB'][] = "Error moving file $fn to $fnNew . Cannot upsert the following metadata: $err\n";
		exit(0);
		return 0;
	}

	print "___".$GLOBALS['grid']	."->update( filename'=> $fn,  \$set : 'filename' : $fnNew )______";

	$GLOBALS['grid']->update(
				array('filename'=> $fn),
				array( '$set' => array ('filename'=>$fnNew)
					 )
		);
	$fileNew2=  $GLOBALS['grid']->findOne(array('filename' => $fnNew));
	if ( empty($fileNew) || empty($fileNew2)){
		print "Cannot update filename. Internal error\n";
		$_SESSION['errorData']['mongoDB'][] = "Error moving file $fn to $fnNew . Cannot update filename\n";
		exit(0);
		return 0;
	}

	$GLOBALS['filesCol']->remove(array("_id"=> $fn) );
	
	// change  parentDirs
	$GLOBALS['filesCol']->update (
		 array("_id"=>$parentNew),
		 array('$addToSet' => array("files" => $fnNew))
	);
	$parent	= dirname($fn);
	$GLOBALS['filesCol']->update(
		 array('_id'=> $parent),
		 array('$pull' => array("files"=>$fn))
	);
	return 1;
}
*/

function fromAbsPath_toPath($absPath){
	$path = str_replace($GLOBALS['dataDir'],"",$absPath);
	return preg_replace('/^\//',"",$path);
}


function absolutePathGSDir($dir,$asRoot=0){
	/*
	if ($asRoot){
		$root = $GLOBALS['dataDir'];
		if (preg_match('/^\//',$dir)){
			if (!preg_match('/^'.$root.'/',$dir)){
				$_SESSION['errorData']['mongoDB'][]= "There cannot be files ouside $root. Failing to stage '$dir'";
				return array($dir,0);
			}
			return array($dir,1);
		}else
			return array($GLOBALS['dataDir']."/".$dir,1);
	*/
	if ($asRoot){
		if (preg_match('/^\//',$dir)){
			$path = str_replace($GLOBALS['dataDir'],"",$dir);
			$path = preg_replace('/^\//',"",$path);
			return array($path,1);
		}else{
			return array($dir,1);
		}	
		
	}else{
		$root = $_SESSION['User']['id'];
		if ( $root != $_SESSION['curDir'] && ! preg_match('/^(\/)*'.$root.'(\/|$)/',$_SESSION['curDir'])){
			$_SESSION['errorData']['mongoDB'][]= "Current directory ".$_SESSION['curDir']." is not under the home directory $root. Restart login, please";
			return array($dir,0);
		}
		if (!preg_match('/^(\/)*'.$root.'(\/|$)/',$dir)){
			return array($_SESSION['curDir']."/".$dir,1);
		}else{
			return array($dir,1);
		}
	}
}

function absolutePathGSFile($fn,$asRoot){
	if ($asRoot){
                if (preg_match('/^\//',$fn)){
                        $path = str_replace($GLOBALS['dataDir'],"",$fn);
                        $path = preg_replace('/^\//',"",$path);
                        return array($path,1);
                }else{
                        return array($fn,1);
                }

	}else{
		$root = $_SESSION['User']['id'];
		if ( $root != $_SESSION['curDir'] && ! preg_match('/^(\/)*'.$root.'(\/|$)/',$_SESSION['curDir'])){
			$_SESSION['errorData']['mongoDB'][] = "Current directory ".$_SESSION['curDir']." is not under the home directory $root. Restart login, please";
			return array($fn,0);
		}
		if ( !preg_match('/^(\/)*'.$root.'\//',$fn)){
			return array($_SESSION['curDir']."/".$fn,1);
		}else{
			return array($fn,1);
		}
	}	
}


// create new directory registry

function createGSDirBNS($dirPath,$asRoot=0) {
	$col = $GLOBALS['filesCol'];
	//check dirPath
	if (strlen($dirPath) == 0){
		$_SESSION['errorData']['mongoDB'][]= "No directory path given";
		return 0;
	}
	list($dirPath,$r) = absolutePathGSDir($dirPath,$asRoot);
	if ($r == "0"){
		$_SESSION['errorData']['mongoDB'][]="Cannot create $dirPath . Target not under root directory ".$_SESSION['User']['id']." ?";
		return 0;
	}

	// already there?
	$r = getGSFileId_fromPath($dirPath,1);
	if ($r != "0"){
		return $r;
	}
	
	//check parent
	if ( $dirPath == $_SESSION['User']['id'] ){
		$parentId = 0;
	}elseif($asRoot && (preg_match('/^'.preg_quote($GLOBALS['dataDir'], '/').'(\/)*[^\/.]+$/',$dirPath))  ){
		$parentId = 0;
	}elseif($asRoot && (preg_match('/^[^\/.]+$/',$dirPath))  ){
		$parentId = 0;
	}else{
		$parentPath = dirname($dirPath);
		$parentId   = getGSFileId_fromPath($parentPath,1);
		if ($parentId == "0"){
			$r = createGSDirBNS($parentPath);
			if ($r=="0")
				return 0;
		}
	}
	if ($parentId && $parentId!="0"){
		$parentObj = $col->findOne(array('_id' => $parentId, 'owner' => $_SESSION['User']['id']) );
		if (isset($parentObj['permissions']) && $parentObj['permissions']== "000" ){
			$_SESSION['errorData']['mongoDB'][]= "Not permissions to modify parent directory $parent";
			return 0;
		}
	}
	//check owner
	$owner = $_SESSION['User']['id'];
	if ($asRoot){
		if ( preg_match('/^'.preg_quote($GLOBALS['dataDir'], '/').'(\/)*([^\/.]+)/',$dirPath,$m) ){
			$owner = $m[2];
		}elseif (preg_match('/^([^\/.]+)/',$dirPath,$m) ){
			$owner = $m[1];
		}
	}

	//store
	$dirId = createLabel();

	$col->update (
	   array('_id' => $dirId),
	   array(
			'_id'        => $dirId,
			'owner'      => $owner,
			'size'       => 0,
			'path'       => $dirPath,
			'type'       => 'dir',
			'mtime'      => new MongoDate(strtotime("now")),
			'atime'      => new MongoDate(strtotime("now")),
			'files'      => array(),
			'parentDir'  => $parentId
	   ),array('upsert'=> 1)
	);

	if ($parentId && $parentId!="0"){
		$col->update (
			array("_id"=>$parentId),
			array('$addToSet' => array("files" => $dirId))
		);
	}
	return $dirId;
}

// create new file registry
// load file content to GRID, if load2grid===TRUE
function uploadGSFileBNS($fnPath, $file, $attributes=Array(), $meta=Array(), $load2grid=false,$asRoot=0){

	$col = $GLOBALS['filesCol'];

	//check fn
	list($fnPath,$r) = absolutePathGSFile($fnPath,$asRoot);
	if ($r==0){
		$_SESSION['errorData']['mongoDB'][]="Cannot upload $fnPath . Check current directory". $_SESSION['curDir'];
                return $r;
	}
        $r = getGSFileId_fromPath($fnPath);
        if ($r != "0"){
		$_SESSION['errorData']['mongoDB'][]="Cannot upload $fnPath . File path already exists";
                return $r;
        }

	//check parent
	$parentPath  = dirname($fnPath);
	if ($parentPath == ".")
		$parentPath=$_SESSION['User']['id'];
	
	$parentId  = getGSFileId_fromPath($parentPath,$asRoot);
	if ($parentId == "0"){
		$r = createGSDirBNS($parentPath,$asRoot);
		if ($r=="0")
			return 0;
	}else{
		if (!isGSDirBNS($col,$parentId) ){
			$_SESSION['errorData']['mongoDB'][]="Cannot upload $fnPath. Parent '$parentPath' is not a directoryy";
			return 0;
	        }
		$parentObj = $col->findOne(array(
					'_id' => $parentId,
					'owner' => $_SESSION['User']['id']
					) );
		if (isset($parentObj['permissions']) && $parentObj['permissions']== "000" ){
			$_SESSION['errorData']['mongoDB'][]= "Not permissions to modify parent directory $parentPath";
			return 0;
		}
	}


	//load file content to grid
	if ($load2grid){
		$r = uploadGSFile($GLOBALS['grid'], $fnPath, $file );
	}

	// load File info to mongo

	$fnId = (!isset($attributes['_id'])? createLabel():$attributes['_id']);

	if ($attributes){
		//set default file attributes
		if (! isset($attributes['_id']))
				$attributes['_id'] = $fnId;
		if (! isset($attributes['owner']))
				$attributes['owner'] = $_SESSION['User']['id'];
		if (! isset($attributes['mtime']))
				$attributes['mtime'] = new MongoDate(filemtime($file));
		if (! isset($attributes['size']))
				$attributes['size'] =filesize($file);
		if (! isset($attributes['parentDir']))
				$attributes['parentDir'] =$parentId;
		if (! isset($attributes['path']))
				$attributes['path'] = $fnPath;
		if (! isset($attributes['expiration'])){
				$expiration = $GLOBALS['caduca'] * 24 * 3600;
				$t = filemtime($file);
				$attributes['expiration'] = new MongoDate($t + $expiration);
		}
		$GLOBALS['filesCol']->update (
			array('_id' => $fnId),
			$attributes,
			array('upsert'=> 1)
		);

		// set parent
		$GLOBALS['filesCol']->update (
			array("_id"=>$parentId),
			array('$addToSet' => array("files" => $fnId))
		);
		$timeObj = new MongoDate(strtotime("now"));
		modifyGSFileBNS($parentId,"atime", new MongoDate(filemtime($file)));

	}
	// add metadata file
	if (count($meta)){
		modifyMetadataBNS($fnId,$meta);
	}
	return $attributes['_id'];
}


//insert metadata for a file
//overwrites all metadata
function modifyMetadataBNS($fn, $metadata){
	if (empty($GLOBALS['filesCol']->findOne(array('_id' => $fn))) ){
		$_SESSION['errorData']['mongoDB'][]= "Cannot modify metadata for $fn. File not in the repository";
		return 0;
	}
	$GLOBALS['filesMetaCol']->update (
			array('_id' => $fn),
			$metadata,
			array('upsert'=> 1)
	);
	//if ($GLOBALS['filesMetaCol']->lastError()){
	//	$err = $GLOBALS['filesMetaCol']->lastError();
	//	$_SESSION['errorData']['mongoDB'][] = $err['err'];
	//	return 0;
	//}else{
		return 1;
	//}
}


//insert metadata for a file
//add new metadata keys to previous metadata
function addMetadataBNS($fn, $metadata){
	if (empty($GLOBALS['filesCol']->findOne(array('_id' => $fn))) ){
		$_SESSION['errorData']['mongoDB'][]= "Cannot add metadata for $fn. File not in the repository";
		return 0;
	}
	foreach ($metadata as $k=>$v){
		$GLOBALS['filesMetaCol']->update (
			array('_id'   => $fn),
			array('$set'  => array($k => $v)),
			array('upsert'=> 1)
		);
		
	}
	return 1;
}

// edit file registry (update  mtime, permissions, etc)

function modifyGSFileBNS($fn, $attribute, $value){

	$file  = $GLOBALS['filesCol']->findOne(array('_id' => $fn) );
	if ( empty($file)){
		$_SESSION['errorData']['mongoDB'][] = " Cannot set $attribute=$value into file $fn. File not found.";
		return 0;
	}

	if (is_string($attribute) && !is_array($value) ){
		$GLOBALS['filesCol']->update (
			array('_id' => $fn),
			array('$set'=> array( $attribute => $value))
		);
	}else{
		$_SESSION['errorData']['mongoDB'][] = " Cannot set $attribute=$value into file $fn. Attribute expects a string. Value cannot be an array";
		return 0;
	}
	return 1;
}

// delete file registry

function deleteGSFileBNS($fn,$asRoot=0){ //fn == fnId

	// check file
	if ($asRoot == 1)
		$file  = $GLOBALS['filesCol']->findOne(array('_id' => $fn) );
	else
		$file  = $GLOBALS['filesCol']->findOne(array('_id' => $fn, 'owner' => $_SESSION['User']['id'] ) );
	if (empty($file)){
		$_SESSION['errorData']['mongoDB'][]= " Cannot remove file with id=$fn. File not there anymore. Ignoring it</br> <a href=\"javascript:window.location=document.referrer\">[ OK ]</a>";
		return 0;
	}
	if (isset($file['permissions']) && $file['permissions']== "000" ){
		$_SESSION['errorData']['mongoDB'][]= " Not permissions to remove $fn";
		return 0;
	}
	if (isGSDirBNS($GLOBALS['filesCol'], $fn)){
		$_SESSION['errorData']['mongoDB'][]= " Expected file type, but directory type for $fn";
		return 0;
	}

	//check parent
	$parentId = "";
	$parentPath="";
	
	if (isset($file['parentDir']) && $file['parentDir'] != "0" ){
		$parentId= $file['parentDir'];
	}else{
		$filePath  = $file['path'];
		$parentPath = dirname($filePath);
		if ($parentPath == ".")
            $parentPath=$_SESSION['User']['id'];
        $parentId  = getGSFileId_fromPath($parentPath,$asRoot);

    }
	if (!$parentId or !isGSDirBNS($GLOBALS['filesCol'], $parentId)){
		$_SESSION['errorData']['mongoDB'][] = " Cannot remove $filePath. 'parentPath' ($parentId)  is not a directory.";
		return 0;
	}   
	if ( ($parentPath == $_SESSION['User']['id'] || $parentId == "0") && !$asRoot){
		$_SESSION['errorData']['mongoDB'][] = " Cannot remove home directory.";
		return 0;
	}   

	// delete
	if (!empty($file)){
		$GLOBALS['filesCol']->remove(array('_id'=> $fn));
		$GLOBALS['filesMetaCol']->remove(array('_id'=> $fn));
		//$GLOBALS['grid']->remove($fn);
	}

	$GLOBALS['filesCol']->update(
			array('_id'=> $parentId),
			array('$pull' => array("files"=>$fn))
		);
	return 1;
}

// delete directory registry

function deleteGSDirBNS($fn,$asRoot=0){
	if ($asRoot == 1)
		$dir  = $GLOBALS['filesCol']->findOne(array('_id' => $fn));
	else
		$dir  = $GLOBALS['filesCol']->findOne(array('_id' => $fn, 'owner' => $_SESSION['User']['id']) );

	if (!isset($dir['parentDir'])){
		$_SESSION['errorData']['mongoDB'][]= " Cannot find parent directory attribute for $fn . </br> <a href=\"javascript:history.go(-1)\">[ OK ]</a>";
	    return 0;
    }
    
	$parentId= $dir['parentDir'];

	if ( $parentId == "0" && !$asRoot){
		$_SESSION['errorData']['mongoDB'][]= " Cannot remove home directory.";
		return 0;
	}

	foreach ($dir['files'] as $f ){
		if ( isGSDirBNS($GLOBALS['filesCol'], $f) ){
			$r = deleteGSDirBNS($f,1);
		}else{
			$r = deleteGSFileBNS($f,1);
		}
		if ($r == 0)
			return 0;
	}

	$GLOBALS['filesCol']->remove(array('_id'=> $fn));
	$GLOBALS['filesMetaCol']->remove(array('_id'=> $fn));

	$GLOBALS['filesCol']->update(
				array('_id'=> $parentId),
				array('$pull' => array("files"=>$fn))
		  	);
	return 1;
}


function saveGSDirBNS($dir,$outDir) {
	$dirObj = $GLOBALS['filesCol']->findOne(array('_id' =>$dir,  'owner'=>$_SESSION['User']['id'] ));
	if (empty($dirObj) || !isset($dirObj['files']) ){
		$_SESSION['errorData']['mongoDB'][]="Cannot extract $dir from database. It is not a directory of your workspace";
		return 0;
	}
	if (! is_dir($outDir)){
	   exec("mkdir $outDir 2>&1",$output);
		   if ($output){
		   	$_SESSION['errorData']['mongoDB'][] = implode(" ", $output)."</br> <a href=\"javascript:history.go(-1)\">[ OK ]</a>";
		return 0;
	   }
	}
	
	foreach($dirObj['files'] as $f){
		if (isGSDirBNS($GLOBALS['filesCol'], $f)){	
		$outDirSub = $outDir."/".basename($f);
		$r = saveGSDirBNS($f,$outDirSub);
		if ($r == 0)
			break;
		}else{
	 	$outTmp = "$outDir/".basename($f);
		saveGSFile($GLOBALS['grid'],$f,$outTmp);
		if (! is_file($outTmp)){
			$_SESSION['errorData']['mongoDB'][]="Cannot extract $dir from database. Inner $f not written in temporal dir $outTmp . ";
			return 0;
		}
		}
	}
	return 1;
}


//
//

	
function printGSFile($col, $fn, $mime = '', $sendFn = False) {
	$file = $col->findOne($fn);
	if (!$file->file['_id'])
		return 1;
	if ($mime)
		header('Content-type: ' . $mime);
	if ($sendFn)
		header('Content-Disposition: attachment; filename="' . $fn . '"');
	print($file->getBytes());
	return 0;
}

function getGSFileSmall($col, $fn) {
	$file = $col->findOne(array('filename' => $fn));
	if (empty($file)){
		print errorPage('File Not Found', 'File id ' . $fn . ' not found');
		exit;
	}else{
		return $file->getBytes();
	}
}
function getGSFile($col, $fn) {
	$file = $col->findOne(array('filename' => $fn));
	if (empty($file)){
		print errorPage('File Not Found', 'File id ' . $fn . ' not found');
		exit;
	}else{
		$content ="";
		$stream = $file->getResource();
		while(!feof($stream)){
			$buff = fread($stream, 1024);
			print $buff;
			ob_flush();
			flush();
		}
	}
}

function saveGSFile($col,$fn,$outFn) {
	$file = $col->findOne(array('filename' => $fn));
	if (empty($file)) {
		print errorPage('File Not Found', 'Cannot save file ' . $fn . ' . File not found');
		exit;
	}
	$file->write($outFn);
	return 0;
}

function calcGSUsedSpace ($id) {
	$ops = array(
				array('$match' => array('owner' => $id)),
				array('$group'=> array(
					'_id'=>'$owner',
					'size'=> array('$sum'=>'$size')
				)
			)
		);
	$d = $GLOBALS['filesCol']->aggregate($ops);


	//print_r($d['result'][0]['size']);
	return $d['result'][0]['size']+0.;
}

// sums file sizes down from a given dir

function calcGSUsedSpaceDir ($fn) {
	$ops = array(
				array('$match' => array('parentDir' => $fn)),
				array('$group'=> array(
					'_id'=>'$parentDir',
					'size'=> array('$sum'=>'$size')
				)
			)
		);
	$d = $GLOBALS['filesCol']->aggregate($ops);
	if (!count($d['result']))
		return 0;
	else
	return $d['result'][0]['size']+0.;
}


// store file content into GRID

function uploadGSFile($col,$fn,$fsFile) {
   $path= pathinfo($fsFile,PATHINFO_DIRNAME);

   if(file_exists($fsFile)){
   	chdir($path);
   	$col->remove(array('filename' => $fn));
	//exec("cd $path;mongofiles -h mmb.pcb.ub.es -u dataLoader -p mdbwany2015 --authenticationDatabase admin -d restcastemp -r put $fn");
	//$col->storeBytes(file_get_contents("$fsFile"), array('filename'=>$fn));
	$col->storeFile($fsFile,array('filename'=>$fn));
	return 1;
   }else{
	$_SESSION['errorData']['mongoDB'][]= 'File ' . $fn . ' not stored. Temporal '. $fsFile . ' not found';
	return 0;
   }
}

function file_get_contents_chunked($file,$chunk_size,$callback){
	try	{
		$handle = fopen($file, "r");
		$i = 0;
		while (!feof($handle))
		{
			call_user_func_array($callback,array(fread($handle,$chunk_size),&$handle,$i));
			$i++;
		}
		fclose($handle);
	}
	catch(Exception $e) {
		 trigger_error("file_get_contents_chunked::" . $e->getMessage(),E_USER_NOTICE);
		 return false;
	}
	return true;
}

function syncWorkDir2Mongo($WD){
	$wdR="";
	$dataDir = $_SESSION['User']['id'];
	$dataDirP= $GLOBALS['dataDir'];
	
	//given wdR - full path
	if (preg_match('/^'.preg_quote($dataDirP,"/").'/',$WD)){
		$wdR = $WD;
	//given wd - from user dataDir
	}elseif(preg_match('/^'.preg_quote($dataDir,"/").'/',$WD) ){
		$wdR = "$dataDirP/".$WD;
	}else{
		$_SESSION['errorData']['mongoDB'][]="Invalid directory '$WD'. The file is not under dataDir ($dataDirP) nor is a valid Mongo FN (^$dataDir/...)";
		return false;
	}
	if (! is_dir($wdR)){
		$_SESSION['errorData']['mongoDB'][]="Cannot syncronize data. $wdR is not found or is not a directory.";
		return false;
	}

	$dir = scandir($wdR);
	$dirFiles=Array();
	foreach ($dir as $key => $file) {
		$fnR = $wdR."/".$file;
		$fn  = str_replace($dataDirP,"",$fnR);
		$fn  = preg_replace('/^\//',"",$fn);

		if (in_array($file,array(".","..")) || preg_match('/^\./',$file) ){
			continue;
		}
		//saving DIRS to Mongo based on disk files
		if (is_dir($fnR)){
			if (!$GLOBALS['filesCol']->findOne(array('_id' => $fn)) ){
				$r = createGSDirBNS($fn);
				if ( $r== 0 ){
					$_SESSION['errorData']['mongoDB'][] = "Error syncronizing data. Cannot create folder $fn <br/>";
					continue;
				}
			}
			$r = syncWorkDir2Mongo($fnR);
			if (!$r)
				return false;

		//saving FILES to Mongo based on disk files
		}else{
			//TODO: storing from uploads. Needs to consider validation state, etc.
			if (preg_match('/uploads/',$fn)){
				$fileExtension = strtoupper(pathinfo($fn, PATHINFO_EXTENSION));
				if (!in_array($fileExtension,array("COV","BAI","RDATA","LOG","SH","ERR")) ){
					if (preg_match('/tmp\.\d+.bam/',$fn)){
						continue;
					}
					array_push($dirFiles,$fn);

					$validation=1;
					$BAM_basename  = $wdR."/".pathinfo($file, PATHINFO_FILENAME);
					if ($fileExtension == "BAM" && !is_file($BAM_basename.".bam.RData") || !is_file($BAM_basename.".bam.bai") || !is_file($BAM_basename.".bam.cov") ){
						$validation = 0;
					}

					if (!$GLOBALS['filesCol']->findOne(array('_id' => $fn)) ){	
							$insertData=array(
									'_id'   => $fn,
									'owner' => $_SESSION['User']['id'],	
									'size'  => filesize($fnR),
									'mtime' => new MongoDate(filemtime($fnR))
							);
							$insertMeta=array(
									'format'	 => $fileExtension,
					 				'description'=> "Warning: File authomatically registered by 'syncWorkDir' func. Validation process omitted",
									'validated'  => $validation
							);
							$r = uploadGSFileBNS($fn, $fnR, $insertData,$insertMeta,FALSE);
							if (!$r){
								$_SESSION['errorData']['mongoDB'][]="Error syncronizing data. Cannot save file $fn to database";
							}
					}
			}

			//storing from execution directories
			}else{
				$fileExtension = strtoupper(pathinfo($fn, PATHINFO_EXTENSION));
				// LOG and SH files only saved if job FAILED ( in getPendingFiles)
				if (!in_array($fileExtension,array("LOG","SH","RDATA","ERR")) && !preg_match('/[O|E]\d+/',$fileExtension) && !preg_match('/\/monitor\.\d+/',$fn)){
					array_push($dirFiles,$fn);
					if (!$GLOBALS['filesCol']->findOne(array('_id' => $fn)) ){
						//saving
						$fileInfo = saveResults($fn);
						if (!$fileInfo)
							$_SESSION['errorData']['mongoDB'][]="Error syncronizing data. Cannot save file $fn into database";
						
					}
				}
			}
		}
	}
	// erasing from Mongo based on diskFiles
	$wd  = str_replace($dataDirP,"",$wdR);
	$wd  = preg_replace('/^\//',"",$wd);
	$mongoFiles = $GLOBALS['filesCol']->find(array(
			'owner'	=> $_SESSION['User']['id'],
			'files'	=> array('$exists' => false),
			'parentDir'=> $wd
	));
	
	if (count($mongoFiles) != count($dirFiles)){
		if (count($mongoFiles) > count($dirFiles)){
			foreach ($mongoFiles as $mongoF){
				if (!isset($dirFiles[$mongoF['_id']]) && !preg_match('/log/',$mongoF['_id'] ) && !preg_match('/[o|e]\d+/',$mongoF['_id'])  ) {
					$_SESSION['errorData']['mongoDB'][]="Error syncronizing data.'".$mongoF['_id']."' not on disk anymore. Do you want to delete it? <a href=\"workspace/workspace.php?op=delete&fn=".$mongoF['_id']."\">[ Yes ]</a>";
				}
			}
		}elseif (count($mongoFiles) < count($dirFiles)){
			$mongoFilesE = iterator_to_array($mongoFiles);
			foreach ($dirFiles as $dirF){
				if (!isset( $mongoFilesE[$dirF]) &&  !preg_match('/uploads/',$dirF)){
					$_SESSION['errorData']['mongoDB'][]="Error syncronizing data.'".$dirF."' found on disk but not indexed.";
				}
			}	
		}
	}
	return true;
}

function createLabel(){
        $label= uniqid($_SESSION['User']['id']."_",TRUE);
        if (! empty($GLOBALS['filesCol']->findOne(array('_id' => $label))) ){
                $label= uniqid($_SESSION['User']['id']."_",TRUE);
        }
        return $label;
}
