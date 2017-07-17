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
        $file = $col->findOne(array('_id' => $fn,
				    'files'    => array('$exists' => true)
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
    $dirObj= $GLOBALS['cassandraIds']->findOne(array('_id' => $dir));
    if (empty($dirObj) || !isset($dirObj['files']) ){
	$_SESSION['errorData']['Error']= $dir ." directory has no files\n";
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

    $fileOld= $GLOBALS['cassandraIds']->findOne(array('_id' => $fn));
    if ( empty($fileOld)){
	print "Cannot move file ".$fn." . It does not exist\n";
	$_SESSION['errorData']['Error']= "Cannot move file ".$fn." . It does not exist\n";
        return 0;
    }
    $fileNew= $GLOBALS['cassandraIds']->findOne(array('_id' => $fnNew));
    if ( !empty($fileOld)){
	print  "Cannot move file ".$fn." to ".$fnNew." . New file already exists\n";
	$_SESSION['errorData']['Error']= "Cannot move file ".$fn." to ".$fnNew." . New file already exists\n";
        return 0;
    }
    //set new parent
    $parentNew = dirname($fnNew);
    if ( ! isGSDirBNS($GLOBALS['cassandraIds'],$parentNew) ){
	print "____new parent  $parentNew  needs creation______";
        createGSDirBNS($GLOBALS['cassandraIds'],$parentNew);
	if ( $r== 0 ){
		$_SESSION['errorData']['Error']= "Cannot move file ".$fn." to ".$fnNew." . Cannot create parent directory $parentNew \n";
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

    print $GLOBALS['cassandraIds'] ."->insert( (_id' => $fnNew) , INOFOLD, psert'=> 1) ";
    var_dump ($infoOld);
    print "____";
    print $GLOBALS['cassandra']. " update( filename'=> $fn), ( \$set : 'filename' : $fnNew) )______";
    exit(0);

    $GLOBALS['cassandraIds']->insert(
		array('_id' => $fnNew),
		$infoOld,
		array('upsert'=> 1)
	);
    $GLOBALS['cassandra']->update(
		array('filename'=> $fn),
		array( '$set' => array ('filename'=>$fnNew)
		     )
	);

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
	if ( $root != $_SESSION['curDir'] && preg_match('/^(\/)*'.$root.'(\/|$)/',$_SESSION['curDir'])){
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
	if ( $root != $_SESSION['curDir'] && preg_match('/^(\/)*'.$root.'(\/|$)/',$_SESSION['curDir'])){
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

	print "__creating dir $dir _";
	if ( isGSDirBNS($col,$dir) ){
		print "__ dir $dir already exists__";
        	return 0;
	}
	$dir   = absolutePathGSDir($dir);
	$parent= (($dir == $_SESSION['BNSId'] )? 0 : dirname($dir));

	print "_ dirAbs=$dir parent=$parent _";
	exit(0);

	if ($parent && $parent!="0" && !isGSDirBNS($col,$parent) ){
		print "___Create parent directory at ".$parent.".__\n";
		$r = createGSDirDNS($col, $parent);
		if ($r==0)
		    return 0;
	}
	print "__creating $dir into $parent _";
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

	// set parent 
	if (! isset($attributes['parentDir']))
		$parent= (($fn == $_SESSION['BNSId']) ? 0 : dirname($fn));

	//upload file 
	uploadGSFile($GLOBALS['cassandra'], $fn, $file );

	//register file metadata
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
	
        $GLOBALS['cassandraIds']->update (
	    array('_id' => $fn),
	    $attributes,
	    array('upsert'=> 1)
	    );
	if (! isset($attributes['parentDir']))
		$parent= (($fn == $_SESSION['BNSId']) ? 0 : dirname($fn));

	$GLOBALS['cassandraIds']->update (
		array("_id"=>$parent),
		array('$addToSet' => array("files" => $fn))
	);
}

function deleteGSFileBNS($fn){
    $file  = $GLOBALS['cassandraIds']->findOne(array('_id' => $fn, 'owner' => $_SESSION['BNSId']) );
    if ( isGSDirBNS($GLOBALS['cassandraIds'], $file)){
        return 0;
    }
    $parent = "";
    if (isset($file['parentDir'])){
        $parent= $file['parentDir'];
    }else{
        $parent= basename(dirname($fn));
    }
    if ( !$parent  or  !isGSDirBNS($GLOBALS['cassandraIds'], $parent)){
        $_SESSION['errorData']['error']= " Cannot remove $fn . Parent $parent is not a directory.";
        return 0;
    }   
    if ( $parent == "0"){
        $_SESSION['errorData']['error']= " Cannot remove home directory.";
        return 0;
    }   
    
    $GLOBALS['cassandraIds']->remove(array('_id'=> $fn));
    $GLOBALS['cassandra']->remove($fn);

    $GLOBALS['cassandraIds']->update(
		    array('_id'=> $parent),
		    array('$pull' => array("files"=>$fn))
		);

}

function deleteGSDirBNS($fn){
    $dir  = $GLOBALS['cassandraIds']->findOne(array('_id' => $fn, 'owner' => $_SESSION['BNSId']) );

    if (!isset($dir['parentDir'])){
        $_SESSION['errorData']['error']= " Cannot remove $fn . Is not there anymore?";
        return 0;
    }
    $parent= $dir['parentDir'];

    if ( $parent == "0"){
        $_SESSION['errorData']['error']= " Cannot remove home directory.";
        return 0;
    }   

    foreach ($dir['files'] as $f ){
    	if ( isGSDirBNS($GLOBALS['cassandraIds'], $f) ){
	    deleteGSDirBNS($f);
	}else{
	    deleteGSFileBNS($f);
	}
    }
    $GLOBALS['cassandraIds']->remove(array('_id'=> $fn));

    $GLOBALS['cassandraIds']->update(
    		    array('_id'=> $parent),
    		    array('$pull' => array("files"=>$fn))
    	  	);
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


function getGSFile($col, $fn) {
    $file = $col->findOne(array('filename' => $fn));
    if (!$file->file['_id']){
        print errorPage('File Not Found', 'File id ' . $fn . ' not found');
        exit;
    }else{
        return $file->getBytes();
    }
}

function saveGSFile($col,$fn,$outFn) {
    $file = $col->findOne(array('filename' => $fn));
    if (!$file->file['_id']) {
        print errorPage('File Not Found', 'File ' . $fn . ' not found');
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
//	   exec("cd $path;mongofiles -h mmb.pcb.ub.es -u dataLoader -p mdbwany2015 --authenticationDatabase admin -d restcastemp -r put $fn");
	$vv= file_get_contents("$sFile");
	$col->storeBytes(file_get_contents("$fsFile"), array('filename'=>$fn));
	   
   }else{
        print 'File NoFile ' . $fn . ' not stored. Temporal '. $fsFile . ' not found';
        #print errorPage('File Not Found', 'File ' . $fn . ' not stored. Temporal '. $fsFile . ' not found');
        exit;
   }
}

