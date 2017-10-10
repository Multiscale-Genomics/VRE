<?php


function DMP_http($method,$service,$data=false){
    
    $resp = 0;
    $info = array();

    $url_base = $GLOBALS['DMPserver_domain'].":".$GLOBALS['DMPserver_port'].$GLOBALS['DMPserver_address'];
    $headers  = array("Content-Type: application/json", "Authorization: Bearer ".$_SESSION['User']['token']);
print "HEADER : -H \"Content-Type: application/json\" -H \"Authorization: Bearer ".$_SESSION['User']['token']."\"\n";

    $url = $url_base."/".$service;
    switch ($method){
        case "get":
            $data = http_build_query($data);
            $url = $url."?".$data;
            print "URL ============> $url \n";
            list($resp,$info) = get($url,$headers);
            break;
        case "post":
            $data = json_encode($data);
            print "URL ============> $url \n";
            print "DATA ============> $data\n";
            list($resp,$info) = post($data,$url,$headers);
            break;
        case "put":
            $data = json_encode($data);
            print "URL ============> $url \n";
            print "DATA ============> $data\n";
            list($resp,$info) = put($data,$url,$headers);
            break;
        default:
            $_SESSION['errorData']['Error'][]="Method '$method' not implemented in the DMP_http handler";
            return $resp;
    }
    print "RESP ===========> $resp\n";
    #print "INFO ===========> "; var_dump($info); print "\n";

    if ($info['http_code'] != 200 && $info['http_code'] != 204){
        if ($resp){
            $err = json_decode($resp,TRUE);
            $_SESSION['errorData']['DMP'][]="MuG data manager (DM) returned error. [".$err['error']."]: ".$err['error_description'];
        }else{
            $_SESSION['errorData']['DMP'][]="MuG data manager (DM) returned HTTP code = ".$info['http_code'];
        }
        return false;
    } 
    $resp = json_decode($resp,TRUE);
    return $resp;
}

function isGSDirBNSXXX($fn) { ### !OJO: old params were ($col,$fn) !!!

    // get DMP file
    $user_id = ($asRoot?$asRoot:$_SESSION['User']['id']);
    $params = array("user_id" => $user_id,
                    "file_id" => $fn,
                    "chrom"   => 0,
                    "start"   => 0,
                    "end"     => 0);
    $fileDMP = DMP_http("get","track",$params);

    // convert DMP file to VRE file
    #list($fileData,$fileMeta) = getVREfile_fromFile($fileDMP);
    
    //check files attribute
    if (isset($fileDMP['files']))
        return false;
    else
        return true;
}

function getGSFileId_fromPathXXX($fnPath,$asRoot=0) {

    // get DMP file
    $user_id = ($asRoot?$asRoot:$_SESSION['User']['id']);
    $params = array("user_id"  => $user_id,
                    "file_path"=> $fnPath);
    $fileDMP = DMP_http("get","file",$params);

    // convert DMP file to VRE file
    list($file,$fileMeta) = getVREfile_fromFile($fileDMP);

	if (empty($file))
		return 0;
	else
		return $file['_id'];
	
}

function getGSFilesFromDirXXX($dataSelection=Array(),$onlyVisible=0){

    $files=Array();

    // get DMP dir
    if (count($dataSelection) == 0 ){
        if (!isset($_SESSION['curDir'])){
            $_SESSION['errorData']['internal'][]="Cannot retrieve files from the database. Given query is not valid. Please, try it later or mail <a href=\"mailto:".$GLOBALS['helpdeskMail']."\">".$GLOBALS['helpdeskMail']."</a>";
            return FALSE;
        }
        $dataSelection = Array(
            'user_id'  => $_SESSION['userId'],
            'file_path'=> $_SESSION['curDir']);
    }
    
    $fileDMP = DMP_http("get","file",$dataSelection);

    // convert DMP dir to VRE dir
    list($dirData,$dirMeta) = getVREfile_fromFile($fileDMP);

    // check dir
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

        if ($onlyVisible){
            #$fData = getGSFiles_filteredBy($d, array('visible'=> Array('$ne'=>false)) );	
            $fData = getGSFiles_filteredByXXX($d, array('visible'=> true));	
        }else{
            $fData = getGSFile_fromIdXXX($d);
        }
	    if ( $fData['path'] == $_SESSION['User']['id'] )
            continue;

	    $fData['mtime'] = $fData['mtime']->sec;
	    $files[$fData['_id']] = $fData; 

        if (isset($fData['files']) && count($fData['files'])>0 ){
	    	foreach ($fData['files'] as $dd) {
                if ($onlyVisible){
                    #$ffData = getGSFiles_filteredBy($dd, array('visible'=> Array('$ne'=>false)) );	
                    $ffData = getGSFiles_filteredByXXX($dd, array('visible'=> true));	
                }else{
                    $ffData = getGSFile_fromIdXXX($dd);
                }
       			if (is_object($ffData['mtime']))
                    $ffData['mtime'] = $ffData['mtime']->sec;
        		$files[$ffData['_id']] = $ffData; 
    		}
	    }
	}
	return $files;
}

function getGSFile_fromIdXXX($fn,$filter="",$asRoot=0) {
    // get DMP file
    $user_id = ($asRoot?$asRoot:$_SESSION['User']['id']);
    $params = array("user_id" => $user_id,
                    "file_id" => $fn,
                    "chrom"   => 0,
                    "start"   => 0,
                    "end"     => 0);
    $fileDMP = DMP_http("get","track",$params);

    // convert DMP file to VRE file
    list($fileData,$fileMeta) = getVREfile_fromFile($fileDMP);

    // return VRE file according filter
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

function getGSFiles_filteredByXXX($fn,$filters) {

    $filter = array_merge(array('user_id' => $_SESSION['User']['id']),$filters);

    $fileDMP = DMP_http("get","file",$filter);

    // convert DMP file to VRE file
    list($fileData,$fileMeta) = getVREfile_fromFile($fileDMP);

    // return VRE file according filter

	if (empty($fileData))
		return 0;

	elseif (empty($fileMeta))
		return $fileData;
	else
		return array_merge($fileData,$fileMeta);	
}

function getAttr_fromGSFileIdXXX($fnId,$attr) {
	$f = getGSFile_fromIdXXX($fnId);
	if (empty($f))
        return false;

	elseif (!isset($f[$attr]) )
		return false;
	else
		return $f[$attr];
}

function getSizeDirBNSXXX($dir){
	$s=0;
	$dirObj = getGSFile_fromIdXXX($dir);
    if (empty($dirObj) || !isset($dirObj['files']) ){
        $_SESSION['errorData']['mongoDB'][] = $dir ." directory has no files<br/>";
        return 0;
    }
	$files = $dirObj['files'];
	foreach ($files as $child){
	    $childObj = getGSFile_fromIdXXX($child);
    	if (empty($childObj))
    		continue;
    	if ( isset($childObj['files']) ){
    		$s += getSizeDirBNSXXX($child);
        }elseif(!isset($childObj['size'])){
    		$s +=0;
    	}else{
    		$s += $childObj['size'];
    	}
	}
	return $s; 
}

// create new directory registry

function createGSDirBNSXXX($dirPath,$asRoot=0) {
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

	//check owner
	$owner = $_SESSION['User']['id'];
	if ($asRoot){
		if ( preg_match('/^'.preg_quote($GLOBALS['dataDir'], '/').'(\/)*([^\/.]+)/',$dirPath,$m) ){
			$owner = $m[2];
		}elseif (preg_match('/^([^\/.]+)/',$dirPath,$m) ){
			$owner = $m[1];
		}
	}

	// already there?
	$r = getGSFileId_fromPathXXX($dirPath,1); # OJO TODO: asroot=1 
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
		$parentId   = getGSFileId_fromPathXXX($parentPath,1); # OJO TODO: asroot=1
		if ($parentId == "0"){
			$r = createGSDirBNS($parentPath);
			if ($r=="0")
				return 0;
		}
	}
	if ($parentId && $parentId!="0"){
        $parentObj = getGSFile_fromIdXXX($parentId,"",$asRoot);
		if (isset($parentObj['permissions']) && $parentObj['permissions']== "000" ){
			$_SESSION['errorData']['mongoDB'][]= "Not permissions to modify parent directory $parent";
			return 0;
		}
    }

	//store
    $dirId = createLabel();

	$dataDMP = array(
			'user_id'       => $owner,
			'file_path'     => $dirPath,
            'creation_time' => new MongoDate(strtotime("now")),
            'meta_data' =>  array(
      			'size'       => 0,
    			'type'       => 'dir',
			    'atime'      => new MongoDate(strtotime("now")),
			    'files'      => array(),
                'parentDir'  => $parentId
            )
    );
    $dirId = DMP_http("post","track",$dataDMP);
    print "DIR ID ------------> $dirId";
    if ($dirId){
        if ($parentId && $parentId!="0"){

            #curl -X PUT -H "Content-Type: application/json" -d '{"type":"add_meta", "file_id":"<file_id>", "user_id":"test_user", "meta_data":{"citation":"PMID:1234567890"}}' http://localhost:5002/mug/api/dmp/track
            $params = array("file_id"  => $parentId,
                            "user_id"  => $owner,
                            "type"     => "add_meta",
                            "meta_data"=> array("files" => array($dirId))
            );
            $r = DMP_http("put","track",$params);
    	}
	    return $dirId;
    }else{
	    return 0;
    }
}
