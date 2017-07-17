<?php
#$pdbConn = new MongoClient("mongodb://localhost");
try {
//       $pdbConn = new MongoClient("mongodb://readAny:mdbrany2015@mmb.pcb.ub.es");
        $pdbConn = new MongoClient("mongodb://dataLoader:mdbwany2015@mmb.pcb.ub.es");
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
$GLOBALS['groupDef'] = $GLOBALS['db']->groupDef;
$GLOBALS['simData']  = $GLOBALS['db']->simData;
$GLOBALS['ontology'] = $GLOBALS['db']->ontology;
$GLOBALS['users']    = $GLOBALS['db']->users;
$GLOBALS['submissions'] = $GLOBALS['db']->submissions;
$GLOBALS['analFiles'] = $GLOBALS['db']->analFiles;

$GLOBALS['db2'] = $pdbConn->FlexPortal; 
$GLOBALS['sequencesCol'] = $GLOBALS['db2']->sequences;

$GLOBALS['db3'] = $pdbConn->restcastemp;
$GLOBALS['cassandra']    = $GLOBALS['db3']->getGridFS();
$GLOBALS['cassandraIds'] = $GLOBALS['db3']->ids;
$GLOBALS['fileIdsCol']   = $GLOBALS['db3']->fileIds;


//

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

function getSizeDirBNS($dir){
    $s=0;
    $dirObj= $GLOBALS['cassandraIds']->findOne(array('_id' => $dir, 'owner'=>$_SESSION['BNSId'] ));
    if (empty($dirObj) || !isset($dirObj['files']) ){
	$_SESSION['errorData']['mongoDB'][] = $dir ." directory has no files\n";
        return 0;
    }
    $files = $dirObj['files'];
    foreach ($files as $child){
	$childObj= $GLOBALS['cassandraIds']->findOne(array('_id' => $child));
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

function moveGSFileBNS($fn,$fnNew){

    $fn    = absolutePathGSFile($fn);
    $fnNew = absolutePathGSFile($fnNew);

    $fileNew= $GLOBALS['cassandraIds']->findOne(array('_id' => $fnNew));
    if ( !empty($fileNew))
        return 1;

    $fileOld= $GLOBALS['cassandraIds']->findOne(array('_id' => $fn, 'owner'=>$_SESSION['BNSId']) );
    if ( empty($fileOld)){
        print "Cannot move file ".$fn." . It does not exist in your workspace\n";
        $_SESSION['errorData']['mongoDB'][] = "Cannot move file ".$fn." . It does not exist in your workspace\n";
        return 0;
    }

    if (isset($fileOld['permissions']) && $fileOld['permissions']== "000" ){
        $_SESSION['errorData']['mongoDB'][]= "Not permissions to move $fn";
        return 0;
    }
    //set new parent
    $parentNew = dirname($fnNew);
    if ( ! isGSDirBNS($GLOBALS['cassandraIds'],$parentNew) ){
        $r = createGSDirBNS($GLOBALS['cassandraIds'],$parentNew);
        if ( $r== 0 ){
                $_SESSION['errorData']['mongoDB'][] = "Cannot move file ".$fn." to ".$fnNew." . Cannot create parent directory $parentNew \n";
		exit(0);
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

    print "___".$GLOBALS['cassandraIds'] ."->insert( _id' => $fnNew , INFO) ";

    $GLOBALS['cassandraIds']->update(
                array('_id' => $fnNew),
                $infoOld,
                array('upsert'=> 1)
        );
    $fileNew = $GLOBALS['cassandraIds']->findOne(array('_id' => $fnNew));
    if ( empty($fileNew) ){
	$err="";
        foreach($infoOld as  $k => $v) {
		$err .= "'$k'='$v', "; 
 	}
        print "Cannot update metadata. Internal error: $err\n";
        $_SESSION['errorData']['mongoDB'][] = "Error moving file $fn to $fnNew . Cannot upsert the following metadata: $err\n";
	exit(0);
        return 0;
    }

    print "___".$GLOBALS['cassandra']    ."->update( filename'=> $fn,  \$set : 'filename' : $fnNew )______";

    $GLOBALS['cassandra']->update(
                array('filename'=> $fn),
                array( '$set' => array ('filename'=>$fnNew)
                     )
        );
    $fileNew2=  $GLOBALS['cassandra']->findOne(array('filename' => $fnNew));
    if ( empty($fileNew) || empty($fileNew2)){
        print "Cannot update filename. Internal error\n";
        $_SESSION['errorData']['mongoDB'][] = "Error moving file $fn to $fnNew . Cannot update filename\n";
	exit(0);
        return 0;
    }

    $GLOBALS['cassandraIds']->remove(array("_id"=> $fn) );
    
    // change  parentDirs
    $GLOBALS['cassandraIds']->update (
         array("_id"=>$parentNew),
         array('$addToSet' => array("files" => $fnNew))
    );
    $parent    = dirname($fn);
    $GLOBALS['cassandraIds']->update(
         array('_id'=> $parent),
         array('$pull' => array("files"=>$fn))
    );
    return 1;
}


function absolutePathGSDir($dir){
        $root = $_SESSION['BNSId'];
        if ( $root != $_SESSION['curDir'] && ! preg_match('/^(\/)*'.$root.'(\/|$)/',$_SESSION['curDir'])){
                print errorPage('Login error', "Current directory ".$_SESSION['curDir']." is not under the home directory $root. Restart login, please\n");
                exit;
        }
        if ( !preg_match('/^(\/)*'.$root.'(\/|$)/',$dir)){
                return $_SESSION['curDir']."/".$dir;
        }else{
                return $dir;
        }

}

function absolutePathGSFile($fn){
        $root = $_SESSION['BNSId'];
        if ( $root != $_SESSION['curDir'] && ! preg_match('/^(\/)*'.$root.'(\/|$)/',$_SESSION['curDir'])){
                print errorPage('Login error', "Current directory ".$_SESSION['curDir']." is not under the home directory $root. Restart login, please\n");
                exit;
        }
        if ( !preg_match('/^(\/)*'.$root.'\//',$fn)){
                return $_SESSION['curDir']."/".$fn;
        }else{
                return $fn;
        }
}

function createGSDirBNS($col,$dir) {

	if (strlen($dir) == 0){
        	$_SESSION['errorData']['mongoDB'][]= "No directory name given";
	        return 0;
	}
        $dir   = absolutePathGSDir($dir);
        if ( isGSDirBNS($col,$dir) ){
                return 1;
        }
        $parent= (($dir == $_SESSION['BNSId'] )? 0 : dirname($dir));
    

        if ($parent && $parent!="0" && !isGSDirBNS($col,$parent) ){
                $r = createGSDirBNS($col, $parent);
                if ($r==0)
                    return 0;
        }
        if ($parent && $parent!="0"){
	    $parentObj   = $col->findOne(array('_id' => $parent, 'owner' => $_SESSION['BNSId']) );
	    if (isset($parentObj['permissions']) && $parentObj['permissions']== "000" ){
        	$_SESSION['errorData']['mongoDB'][]= "Not permissions to modify directory $parent";
	        return 0;
	    }
	}
        $col->update (
               array('_id' => $dir),
               array(
                    '_id'   => $dir,
                    'owner' => $_SESSION['BNSId'],
                    'size'  => 0,
                    'type' => 'dir',
                    'mtime' => new MongoDate(strtotime("now")),
                    'atime' => new MongoDate(strtotime("now")),
                    'files' => array(),
                    'parentDir' => $parent
               ),array('upsert'=> 1)
            );

        if ($parent && $parent!="0"){
                $col->update (
                    array("_id"=>$parent),
                    array('$addToSet' => array("files" => $dir))
                );
        }
        return 1;
}

function uploadGSFileBNS($fn, $file, $attributes=Array()){

	$fn     = absolutePathGSFile($fn);
	$parent = dirname($fn);
	if ($parent == ".")
		$parent=$_SESSION['BNSId'];


        if ($parent && $parent!="0" && !isGSDirBNS($GLOBALS['cassandraIds'],$parent) ){
                $r = createGSDirBNS($GLOBALS['cassandraIds'], $parent);
                if ($r==0)
                    return 0;
        }
        if ($parent && $parent!="0"){
            $parentObj   = $GLOBALS['cassandraIds']->findOne(array('_id' => $parent, 'owner' => $_SESSION['BNSId']) );
            if (isset($parentObj['permissions']) && $parentObj['permissions']== "000" ){
                $_SESSION['errorData']['mongoDB'][]= "Not permissions to write into directory $parent";
                return 0;
            }
        }

	//upload file 
	uploadGSFile($GLOBALS['cassandra'], $fn, $file );

	// set file metadata
	if (! isset($attributes['_id']))
		    $attributes['_id'] = $fn;
	if (! isset($attributes['owner']))
		    $attributes['owner'] = $_SESSION['BNSId'];
	if (! isset($attributes['mtime']))
		    $attributes['mtime'] = new MongoDate(filemtime($file));
	if (! isset($attributes['size']))
		    $attributes['size'] =filesize($file);
	if (! isset($attributes['parentDir']))
		    $attributes['parentDir'] =$parent;
	if (! isset($attributes['expiration'])){
		    $expiration = $GLOBALS['days2expire'] * 24 * 3600;
		    $attributes['expiration'] = new MongoDate(filemtime($file) + $expiration);
	}
	
        $GLOBALS['cassandraIds']->update (
	    array('_id' => $fn),
	    $attributes,
	    array('upsert'=> 1)
	    );

	// set parent metada
	$GLOBALS['cassandraIds']->update (
		array("_id"=>$parent),
		array('$addToSet' => array("files" => $fn))
	);
	return 1;
}

function modifyGSFileBNS($fn, $attribute, $value){

	$file  = $GLOBALS['cassandraIds']->findOne(array('_id' => $fn, 'owner' => $_SESSION['BNSId']) );
	if ( empty($file)){
        	$_SESSION['errorData']['mongoDB'][] = " Cannot set $attribute=$value into file $fn. File not found.";
		return 0;
	}

	if (is_string($attribute) && !is_array($value) ){
	        $GLOBALS['cassandraIds']->update (
		    array('_id' => $fn),
		    array('$set'=> array( $attribute => $value))
		    );
	}else{
        	$_SESSION['errorData']['mongoDB'][] = " Cannot set $attribute=$value into file $fn. Attribute expects a string. Value cannot be an array";
		return 0;
	}
	return 1;
}

function deleteGSFileBNS($fn,$user="session"){

    if ($user == 0){
	$file  = $GLOBALS['cassandraIds']->findOne(array('_id' => $fn) );
    }else{
    	if ($user == "session" ){
	    $user = $_SESSION['BNSId'];
	}
	$file  = $GLOBALS['cassandraIds']->findOne(array('_id' => $fn, 'owner' => $user) );
    }

    if (empty($file)){
        $_SESSION['errorData']['mongoDB'][]= " Cannot remove $fn . Is not there anymore? </br> <a href=\"javascript:history.go(-1)\">[ OK ]</a>";
        return 0;
    }	
    if (isset($file['permissions']) && $file['permissions']== "000" ){
        $_SESSION['errorData']['mongoDB'][]= " Not permissions to remove $fn";
        return 0;
    }
    if ( isGSDirBNS($GLOBALS['cassandraIds'], $file)){
        return 0;
    }
    $parent = "";
    if (isset($file['parentDir'])){
        $parent= $file['parentDir'];
    }else{
        $parent= dirname($fn);
    }
    if ( !$parent or !isGSDirBNS($GLOBALS['cassandraIds'], $parent)){
//        $_SESSION['errorData']['mongoDB'][] = " Cannot remove $fn . Parent $parent is not a directory.";
//        return 0;
    }   

    $GLOBALS['cassandraIds']->remove(array('_id'=> $fn));
    $GLOBALS['cassandra']->remove($fn);

if ( $parent and isGSDirBNS($GLOBALS['cassandraIds'], $parent)){
    $GLOBALS['cassandraIds']->update(
		    array('_id'=> $parent),
		    array('$pull' => array("files"=>$fn))
		);
}
   return 1;

}

function deleteGSDirBNS($fn,$user="session"){
    if ($user == 0){
    	$dir  = $GLOBALS['cassandraIds']->findOne(array('_id' => $fn) );
    }else{
    	if ($user == "session" ){
	    $user = $_SESSION['BNSId'];
	}
    	$dir  = $GLOBALS['cassandraIds']->findOne(array('_id' => $fn, 'owner' => $user) );
    }

    if (!isset($dir['parentDir'])){
        $_SESSION['errorData']['mongoDB'][]= " Cannot find parent directory attribute for $fn . </br> <a href=\"javascript:history.go(-1)\">[ OK ]</a>";
        return 0;
    }
    $parent= $dir['parentDir'];

    if ( $parent == "0"){
        $_SESSION['errorData']['mongoDB'][]= " Cannot remove home directory.";
        return 0;
    }   

    foreach ($dir['files'] as $f ){
    	if ( isGSDirBNS($GLOBALS['cassandraIds'], $f) ){
	    $r = deleteGSDirBNS($f);
	}else{
	    $r = deleteGSFileBNS($f);
	}
	if ($r == 0)
	    return 0;
    }

    $GLOBALS['cassandraIds']->remove(array('_id'=> $fn));

    $GLOBALS['cassandraIds']->update(
    		    array('_id'=> $parent),
    		    array('$pull' => array("files"=>$fn))
    	  	);
    return 1;
}


function saveGSDirBNS($dir,$outDir) {
	$dirObj = $GLOBALS['cassandraIds']->findOne(array('_id' =>$dir,  'owner'=>$_SESSION['BNSId'] ));
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
	    if (isGSDirBNS($GLOBALS['cassandraIds'], $f)){	
		$outDirSub = $outDir."/".basename($f);
		$r = saveGSDirBNS($f,$outDirSub);
		if ($r == 0)
			break;
	    }else{
	 	$outTmp = "$outDir/".basename($f);
		saveGSFile($GLOBALS['cassandra'],$f,$outTmp);
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
	        $buff = fread($stream, 1024) ;
		echo $buff;
	        ob_flush();
	        flush();
	}
    }
}

function saveGSFile($col,$fn,$outFn) {
    $file = $col->findOne(array('filename' => $fn));
    if (!$file->file['_id']) {
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
    $d = $GLOBALS['cassandraIds']->aggregate($ops);
    //print_r($d['result'][0]['size']);
    return $d['result'][0]['size']+0.;
}


function uploadGSFile($col,$fn,$fsFile) {
   $path= pathinfo($fsFile,PATHINFO_DIRNAME);
   chdir($path);
   if(file_exists($fsFile)){
   	$col->remove(array('filename' => $fn));
	//exec("cd $path;mongofiles -h mmb.pcb.ub.es -u dataLoader -p mdbwany2015 --authenticationDatabase admin -d restcastemp -r put $fn");
	//$col->storeBytes(file_get_contents("$fsFile"), array('filename'=>$fn));
	$col->storeFile($fsFile,array('filename'=>$fn));
	   
   }else{
        print errorPage('File Not Found', 'File ' . $fn . ' not stored. Temporal '. $fsFile . ' not found');
        exit;
   }
}

function file_get_contents_chunked($file,$chunk_size,$callback){
    try    {
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


