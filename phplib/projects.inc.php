<?php
use League\OAuth2\Client\Token\AccessToken;

function prepUserWorkSpace($dataDir,$sampleData=""){
    if ($sampleData == "")
            $sampleData = $GLOBALS['sampleData_default'];

	$_SESSION['curDir'] = $dataDir;
	
	$dataDirId = setUserWorkSpace($dataDir,$sampleData);

	//$_SESSION['User']['dataDir'] = $dataDir;

	return $dataDirId;	
}

function setUserWorkSpace($dataDir,$sampleData="") {
    if ($sampleData == "")
        $sampleData = $GLOBALS['sampleData_default'];

	$_SESSION['errorData']['Info'][] = "Preparing user workspace named '$dataDir' with sample data '$sampleData'";
	
	$dataDirP  = $GLOBALS['dataDir']."/$dataDir";
	$dataDirId = getGSFileId_fromPath($dataDir);

	if (! isGSDirBNS($GLOBALS['filesCol'],$dataDirId) || ! is_dir($dataDirP)){

		//creating home directory

		$dataDirId  = createGSDirBNS($dataDir,1);
		$_SESSION['errorData']['Info'][] = "Creating main user directory: $dataDirP ($dataDirId)";

		if ($dataDirId == "0" ){
			$_SESSION['errorData']['Error'][] = "Cannot create main user directory $dataDir";
			return 0;
		}
		$r = addMetadataBNS($dataDirId,Array("expiration"=>-1,
						   "description"=> "Root user data"));
		if ($r == "0" ){
			$_SESSION['errorData']['Error'][] = "Cannot set main user directory $dataDir";
			return 0;
		}
		if (!is_dir($dataDirP))
			mkdir($dataDirP, 0775) or die("Cannot write main user directory $dataDirP");
		

		//creating uploads directory

		$upDirId  = createGSDirBNS($dataDir."/uploads",1);
		$_SESSION['errorData']['Info'][] = "Creating  uploads directory: $dataDir/uploads ($upDirId)";

		if ($upDirId == "0" ){
			$_SESSION['errorData']['Error'][] = "Cannot create uploads directory in $dataDir ($dataDirId) ";
			return 0;
		}
		$r = addMetadataBNS($upDirId,Array("expiration" => -1,
						   "description"=> "Uploaded personal data"));
		if ($r == "0" ){
			$_SESSION['errorData']['Error'][] = "Cannot set uploads directory $dataDir/uploads";
			return 0;
		}
		if (!is_dir("$dataDirP/uploads"))
			mkdir("$dataDirP/uploads", 0775);


		//creating repository directory

		$repDirId  = createGSDirBNS($dataDir."/repository",1);
		$_SESSION['errorData']['Info'][] = "Creating  repository directory: $dataDir/repository ($repDirId)";

		if ($repDirId == "0" ){
			$_SESSION['errorData']['Error'][] = "Cannot create repository directory in $dataDir ($dataDirId) ";
			return 0;
		}
		$r = addMetadataBNS($repDirId,Array("expiration" => -1,
						   "description"=> "Remote personal data"));
		if ($r == "0"){
			$_SESSION['errorData']['Error'][] = "Cannot set uploads directory $dataDir/repository";
			return 0;
		}
		if (!is_dir("$dataDirP/repository"))
			mkdir("$dataDirP/repository", 0775);


        // creating other directories not registered in mongo

		if (!is_dir("$dataDirP/.jbrowse") || !is_dir("$dataDirP/.tadkit") || !is_dir("$dataDirP/.tmp") ){
	                mkdir("$dataDirP/.jbrowse", 0775);
	                mkdir("$dataDirP/.tadkit", 0775);
        	        mkdir("$dataDirP/.tmp", 0775);
		}


		// injecting sample data
		
		$r = setUserWorkSpace_sampleData($sampleData,$dataDir);		
		if ($r=="0")
			$_SESSION['errorData']['Warning'][] = "Cannot fully inject sample data '$sampleData' into user workspace.";
		else
			$_SESSION['errorData']['Info'][] = "Sample data '$sampleData' successfully injected into user workspace.";
	}

	$GLOBALS['filesCol']->update(
		array('_id' => $dataDirId),
		array('$set' => array(
			  'lastAccess' => moment()
			)
		)
	);
	return $dataDirId;	
}







function setUserWorkSpace_sampleData($sampleData,$dataDir){

		$dataDirP   = $GLOBALS['dataDir']."/$dataDir";

		// path for sample data set
		$sampleData_rfn = $GLOBALS['sampleData']."/$sampleData/";

		// validate sample Data integrity
		//$_SESSION['errorData']['Info'][] = "Checking sample data integrity in '$sampleData_rfn'";
		$datafolders  = scanDir($GLOBALS['sampleData']."/$sampleData");
		if (!in_array("uploads",$datafolders)){
			$_SESSION['errorData']['Warning'][]="Sample data '$sampleData' has no 'uploads' folder";
			return 0;
		}
		$meta_rfn = $GLOBALS['sampleData']."/$sampleData/.sample_metadata.json";
		if (!is_file($meta_rfn) ){
			$_SESSION['errorData']['Warning'][]="Sample data '$sampleData' has no metadata (.sample_metadata.json) to load";
			return 0;
		}
		// read sample Data metadata
		$meta = json_decode(file_get_contents($meta_rfn),true);
		if (count($meta) == 0 ){
                        $_SESSION['errorData']['Warning'][]="Sample data '$sampleData' has malformated json in '$meta_rfn'";
			return 0;
                }

		foreach ($meta as $meta_folder){
			if (!isset($meta_folder['file_path']) ){
				$_SESSION['errorData']['Warning'][]="Wrong sample data '$sampleData' metadata contains elements without 'file_path' attribute. Ignoring them.";
				continue;
			}
			
			//$_SESSION['errorData']['Info'][] = "Creating sample data '".$meta_folder['file_path']."'";

			$r = save_fromSampleDataMetadata($meta_folder,$dataDir,$sampleData,"folder");
			if ($r == "0")
				$_SESSION['errorData']['Warning'][]="Failed to inject sample data '".$meta_folder['file_path']."'";
			//else
			//	$_SESSION['errorData']['Info'][]="Sample data '".$meta_folder['file_path']."' successfully injected";

			// looking for files in the folder

			$sample_rfn = $GLOBALS['sampleData']."/$sampleData/".$meta_folder['file_path'];		
			$metaF_rfn  = "$sample_rfn/.sample_metadata.json";

			if (!is_file($metaF_rfn) ){
				$_SESSION['errorData']['Warning'][]="Sample data '$sampleData' has no metadata in $sample_rfn to load. Empty directory.";
				continue;
			}
			$metaF = json_decode(file_get_contents($metaF_rfn),true);
			if (count($metaF) == 0 ){
                        	$_SESSION['errorData']['Warning'][]="Sample data '$sampleData' has malformated json in folder '$sample_rfn'";
				continue;
                	}

			foreach ($metaF as $meta_file){
				if (!isset($meta_file['file_path']) ){
					$_SESSION['errorData']['Warning'][]="Sample data '$sampleData' contains elements without 'file_path' attribute. Ignoring them.";
					continue;
				}
			
				//$_SESSION['errorData']['Info'][] = "Creating sample data file ".$meta_file['file_path'];

				$r = save_fromSampleDataMetadata($meta_file,$dataDir,$sampleData,"file");
				if ($r == "0")
					$_SESSION['errorData']['Warning'][]="Failed to inject sample data '".$meta_file['file_path']."'";
				//else
				//	$_SESSION['errorData']['Info'][]="Sample data '".$meta_file['file_path']."' successfully injected";
			}
        }
}		






function save_fromSampleDataMetadata($meta_folder,$dataDir,$sampleData,$type){

	//rfn from sample data
	$sample_rfn = $GLOBALS['sampleData']."/$sampleData/".$meta_folder['file_path'];

	//rfn to be created in the user workspace
	$dataDirP   = $GLOBALS['dataDir']."/$dataDir";
	$rfn        = $dataDirP."/".$meta_folder['file_path'];


	//
	// Saving to disk
	if ($type== "file"){
		// Creating file
		if (!is_file($sample_rfn)){
			if (is_dir($sample_rfn)){
				$_SESSION['errorData']['Warning'][]="Sample data file '".$meta_folder['file_path']."' is a subfolder. Not supported. Ignoring it.";
				return 0;
			}else{
				$_SESSION['errorData']['Warning'][]="Sample data file '".$meta_folder['file_path']."' not in Sample Data directory ($sample_rfn). Ignoring it.";
				return 0;
			}
		}
		if (!is_file($rfn))
			copy($sample_rfn,$rfn);
	}elseif	($type== "folder"){
        	// Creating folder and hidden files
		if (!is_dir($sample_rfn)){
			if (is_file($sample_rfn)){
				$_SESSION['errorData']['Warning'][]="Sample data folder '".$meta_folder['file_path']."' not grouped below any folder. Ignoring it.";
				return 0;
			}else{
				$_SESSION['errorData']['Warning'][]="Sample data folder '".$meta_folder['file_path']."' not in Sample Data directory. Ignoring it.";
				return 0;
			}
		}
        	if (!is_dir($rfn)){
			mkdir($rfn, 0775);
		}
		$sample_hidden = array_filter(scandir($sample_rfn), function($i) {return preg_match('/^\.\w+/',$i);});
		if (count($sample_hidden)){
			foreach ($sample_hidden as $h){
				if ($h == ".sample_metadata.json") {continue;}
				copy($sample_rfn."/".$h, $rfn."/".$h);
			}
		}

	}else{
		$_SESSION['errorData']['Internal'][]="Sample data '".$meta_folder['file_path']."' cannot be injected.";
		return 0;
	}
	//$_SESSION['errorData']['Info'][]="Sample data '<strong>".$meta_folder['file_path']."</strong>' saved in disk";
			
	//
	// Saving to mongo	
	if ($meta_folder['mongo'] === false){
		//$_SESSION['errorData']['Info'][]="Sample data '".$meta_folder['file_path']."' only in disk as 'mongo:false'";
		return 1;
	}
	// adapt sample data metadata
        $meta_folder['file_path'] = "$dataDir/".$meta_folder['file_path'];
    	$meta_folder['user_id']    = $dataDir;
        $meta_folder['meta_data']['validated'] = true;
        if (isset($meta_folder['meta_data']['shPath'])){
                $meta_folder['meta_data']['shPath']  = "$dataDirP/".$meta_folder['meta_data']['shPath'];
        }
        if (isset($meta_folder['meta_data']['logPath']))
                $meta_folder['meta_data']['logPath']  = "$dataDirP/".$meta_folder['meta_data']['logPath'];
        if (isset($meta_folder['meta_data']['associated_files'])){
                $t= array();
                foreach($meta_folder['meta_data']['associated_files'] as $assoc){
                        $assocPath = "$dataDir/$assoc";
                        $assocId   = getGSFileId_fromPath($assocPath,1);
                        array_push($t,$assocId);
                }
                $meta_folder['meta_data']['associated_files']=$t;
        }
        if (isset($meta_folder['source_id'])){
                $t = array();
                foreach($meta_folder['source_id'] as $source){
                        $sourcePath = "$dataDir/$source";
                        $sourceId   = getGSFileId_fromPath($sourcePath,1);
                        array_push($t,$sourceId);
                }
                $meta_folder['source_id'] = $t;
        }

        // validate sample data metadata
        if (preg_match('/uploads/',$meta_folder['file_path']) || preg_match('/repository/',$meta_folder['file_path']))
                list($r,$meta_folder_ok) = validateMugFile($meta_folder,false);
        else
                list($r,$meta_folder_ok) = validateMugFile($meta_folder,true);

	//if ($meta_folder_ok == "0"){
	if ($r == 0){
        	$_SESSION['errorData']['Warning'][]="Sample data '".$meta_folder['file_path']."' not injected. Its metadata is not valid. Ignoring it";
		return 0;
        }

        // register sample data
        $fileId   = getGSFileId_fromPath($meta_folder_ok['file_path'],1);
        if ($fileId){
                $_SESSION['errorData']['Info'][]= "Sample folder '<strong>".$meta_folder['file_path']."</strong>' already in user workspace";# registered as $fileId";
        }else{
                //convert metadata from MuGfile to VREfile
                list($file,$metadata) = getVREfile_fromFile($meta_folder_ok);
         //saving metadata
		if ($type=="folder"){
	                $newId = createGSDirBNS($meta_folder_ok['file_path'],1);
                	if ($newId == "0" ){
	                        $_SESSION['errorData']['Error'][] = "Cannot register data sample '".$meta_folder_ok['file_path']."'";
	                        return 0;
	                }
	                $r = addMetadataBNS($newId,$metadata);
                	if ($r == "0" ){
	                        $_SESSION['errorData']['login'][] = "Cannot register data sample '".$meta_folder_ok['file_path']."'";
		                return 0;
	                }
		}elseif ($type=="file"){
                    $newId = uploadGSFileBNS($meta_folder_ok['file_path'], $rfn, $file,$metadata,FALSE,1);
               		if ($newId == "0" ){
	                        $_SESSION['errorData']['Error'][] = "Cannot register data sample '".$meta_folder_ok['file_path']."'";
	                        return 0;
	                }
		}
		$_SESSION['errorData']['Info'][]="Sample data '<strong>".$meta_folder['file_path']."</strong>' successfully injected into user workspace"; #($newId)";
        }
	return 1;
}


function getFilesToDisplay($dirSelection,$filter_data_types=array()) {

	$filesAll=Array();

    // Register recent outputs & extract pending files
	$filesPending= processPendingFiles($_SESSION['User']['_id'],$files);


	// Retrieve files from Mongo

	$files = array();
	switch($GLOBALS['fsStyle']){
		case "fsMongo":
		    $files=getGSFilesFromDir($dirSelection,1);
		    break;
		case "mongo":
		    $files=getGSFilesFromDir($dirSelection,1);
		case "fs":
		    #TODO
		default:
		    $_SESSION['errorData']['internal'][]="Cannot update dashboard. Given fsStyle (".$GLOBALS['fsStyle'].") not set. Please, report to <a href=\"mailto:helpdesk@multiscalegenomics.eu\">helpdesk@multiscalegenomics.eu</a>";
		    return $filesAll;
	}
	if (!$files){
	    $_SESSION['errorData']['Error'][]="Cannot update dashboard.";
	    return $filesAll;
	}

	// Merge pending files and mongo data

	if ($filesPending){
		foreach ($filesPending as $r){
			// Update $files[parentId][files]
			if (!isset($filesPending[$r['_id']]['parentDir'])) {
				$_SESSION['errorData']['Error'][]="Pending file ".$filesPending[$r['_id']]['path']." has no parentDir";
				continue;
			}
			$parentId = $filesPending[$r['_id']]['parentDir'];
			if (!isset($files[$parentId])){
                if ($r['pending']){
                    $_SESSION['errorData']['Warning'][]="Cannot display '".$filesPending[$r['_id']]['path']."'. Its execution folder '".$r['title']."' does not exist anymore.";
    				unset($filesPending[$r['_id']]);
               }else{
                   $_SESSION['errorData']['Error'][] ="Cannot display '".$filesPending[$r['_id']]['path']."'. FS inconsistency. Its parent folder ($parentId) does not exist anymore or is unaccessible.";
    				unset($filesPending[$r['_id']]);		
			    }
			    continue;
			}
			array_push($files[$parentId]['files'],$r['_id']);
		}
		$filesAll=array_merge($files,$filesPending);
	}else{
		$filesAll=$files;
    }

    // Filter files by data_types

    if ($filter_data_types || is_array($filter_data_types)){
        $filesFiltered = array();
        $dirs_filtered = array();
        //filter out files with unselected data_types
        foreach ($filesAll as $fn => $file ){
            if (isset($file['data_type']) and  in_array($file['data_type'],$filter_data_types) ){
                $filesFiltered[$fn] = $filesAll[$fn];
                array_push($dirs_filtered,$file['parentDir']);
            }
        }
        //filter out empty dirs
        foreach ($filesAll as $fn => $file ){
            if (isset($file['parentDir']) and  in_array($file['_id'],$dirs_filtered) ){
                $filesFiltered[$fn] = $filesAll[$fn];
            }
        }
        $filesAll = $filesFiltered;
    }

	return $filesAll;

}

//add datatable tree nodes and hidden cols values
function addTreeTableNodesToFiles($filesAll){
	$n=1;
	foreach ($filesAll as $r){
	        // Add Tree Nodes
        	if (isset($r['files'])){
	            $filesAll[$r['_id']]['tree_id']     = $n;
	            $filesAll[$r['_id']]['size']        = calcGSUsedSpaceDir($r['_id']);
	            $filesAll[$r['_id']]['size_parent'] = $filesAll[$r['_id']]['size'];
	            $filesAll[$r['_id']]['mtime_parent']=(isset($r['atime'])? $r['atime']->sec : $r['mtime']);
	            $i=1;
	            foreach ($r['files'] as $rr){
	                $filesAll[$rr]['tree_id']       = "$n.$i";
	                $filesAll[$rr]['tree_id_parent']= $n;
	                $filesAll[$rr]['size_parent']   = $filesAll[$r['_id']]['size_parent'];
	                $filesAll[$rr]['mtime_parent']  = $filesAll[$r['_id']]['mtime_parent'];
	                $i++;
	            }
	            $n++;
		}else{
			if (isset($r['pending']) ){
				$dir = $r['parentDir'];
				$filesAll[$dir]['pending']="true";
			}	
		}
   	}

	return $filesAll;

}

function printTable($filesAll=Array() ) {

	$autorefresh=0;
	?>

	<table id="workspace" class="display" cellspacing="0" width="100%">

	<?php
		print parseTemplate($_REQUEST, getTemplate('/TreeTblworkspace/header.htm'));

		?>
		<tbody><?php

    foreach ($filesAll as $r) {
            // is dir
			if (isset($r['files'])){
				if (preg_match('/\/\./',$r['_id'])){
					continue;
				}
				if (isset($r['pending'])){
					print parseTemplate(formatData($r), getTemplate('/TreeTblworkspace/TR_folderPending.htm'));
				}elseif(basename($r['path']) == "uploads"){
					print parseTemplate(formatData($r), getTemplate('/TreeTblworkspace/TR_folder_uploads.htm'));
				}elseif(basename($r['path']) == "repository"){
					print parseTemplate(formatData($r), getTemplate('/TreeTblworkspace/TR_folder_repository.htm'));
				}elseif(count($r['files']) == 0){
					print parseTemplate(formatData($r), getTemplate('/TreeTblworkspace/TR_folder_empty.htm'));
				}else{
					print parseTemplate(formatData($r), getTemplate('/TreeTblworkspace/TR_folder.htm'));
                }
            // is job
			}elseif(isset($r['pending'])){
					print parseTemplate(formatData($r), getTemplate('/TreeTblworkspace/TR_filePending.htm'));
                    $autorefresh=1;
            // is file
			}elseif(isset($r['_id'])){
					if ($r['validated']){
						print parseTemplate(formatData($r), getTemplate('/TreeTblworkspace/TR_file.htm'));
					}else{
						print parseTemplate(formatData($r), getTemplate('/TreeTblworkspace/TR_fileDisabled.htm'));
					}
			}else{
				//empty mongo entry;
			}
		}
		?>
		</tbody>

	</table>

	<?php 
	if ($autorefresh){
		print "<input type=\"hidden\" id=\"autorefresh\" value=\"$autorefresh\"/>\n";
	}
}

function printLastJobs($filesAll=Array() ) {

	$timestamps = array();
	foreach ($filesAll as $key => $node) {
		$timestamps[$key] = $node["mtime"];
	}
	array_multisort($timestamps, SORT_DESC, $filesAll);

	?>

	<ul class="feeds">

	<?php
		$wehavejobs = false;
		foreach ($filesAll as $r) {
			if (isset($r['files'])){
				if (preg_match('/\/\./',$r['_id']))
					continue;
				if (isset($r['pending'])){
					if(basename($r['path']) != "repository") {
                        print parseTemplate(formatData($r), getTemplate('/LastJobsworkspace/LJ_folderPending.htm'));
						$wehavejobs = true;
					}
				}elseif((basename($r['path']) == "uploads") || (basename($r['path']) == "repository")){
					//$wehavejobs = false;
					//print parseTemplate(formatData($r), getTemplate('/TreeTblworkspace/TR_folder_uploads.htm'));
				}else{
					print parseTemplate(formatData($r), getTemplate('/LastJobsworkspace/LJ_folder.htm'));
					$wehavejobs = true;
					
				}
			}elseif(isset($r['pending'])){
			}elseif(isset($r['_id'])){
			}else{
				//empty mongo entry;
			}
		}
		
		if(!$wehavejobs) echo "You have not launched any job yet.";

		?>

	</ul>

	<?php 
}

function getToolsByDT($data_type, $status = 1) {

	$tl = $GLOBALS['toolsCol']->find(array('external' => true, 'status' => $status));

	$arrTools = array();

	foreach($tl as $tool){

		$combinations = $tool["input_files_combinations_internal"];

		//if($data_type == "hic_sequences") var_dump($combinations);

		if(isset($combinations)) {

			foreach($combinations as $comb){

				if(sizeof($comb) == 1) {

					foreach($comb[0] as $k => $v) {

						if($k == $data_type) {

							$aux = array($tool["_id"], $tool["name"]);

							$arrTools[] = $aux;

						}

					}	

					/*foreach($tool["input_files"] as $ti){

						if(($ti["name"] == $comb[0]) && in_array($data_type, $ti["data_type"])) {

							$aux = array($tool["_id"], $tool["name"]);

							$arrTools[] = $aux;

						}

					}*/
		
				}

			}

		} else if(sizeof($tool["input_files"]) == 1) {

			if(in_array($data_type, $tool["input_files"][0]["data_type"])) {

				$aux = array($tool["_id"], $tool["name"]);

				$arrTools[] = $aux;
			
			}

		}

	}

	return $arrTools;

}


function formatData($data) {
	//var_dump($data);
		//_id id_URL
		if (!isset($data['_id']))
			return $data;
		$data['_id_URL'] = urlencode($data['_id']);
		//mtime atime
		if (isset($data['mtime'])){
			if (is_object($data['mtime']))
				$data['mtime']=$data['mtime']->sec;
			$data['mtime'] = strftime('%Y/%m/%d %H:%M', $data['mtime']);
		}else{
			$data['mtime']="";
		}
		if (isset($data['atime'])){
			if (is_object($data['atime']))
				$data['atime'] =$data['atime']->sec;
			$data['atime'] = strftime('%Y/%m/%d %H:%M', $data['atime']);
			$data['mtime'] = $data['atime'];
		}
		//format
		if (!isset($data['format']))
			$data['format']="";
		//expiration
		if (isset($data['expiration'])){
			$days2expire = intval(( $data['expiration']->sec  -time() ) / (24 * 3600));
			if ($days2expire < 7)
				$data['expiration'] ="<span style=\"color:#b30000;font-weight:bold;\">".$days2expire."</span>";
			else
				$data['expiration'] =$days2expire;
		}else{
			$data['expiration'] ="";
		}
		//size
		if (isset($data['files']) && !isset($data['size']) ){
			$data['size'] = calcGSUsedSpaceDir($data['_id']);
		}
		if (isset($data['size'])){
			$sz = 'BKMGTP';
			$factor = floor((strlen($data['size']) - 1) / 3);
			$data['size']	= sprintf("%.2f %s", $data['size'] / pow(1024, $factor),@$sz[$factor]);
		}else{
			$data['size']="";
		}
		//project
		if (isset($data['parentDir'])){
			$data['parentDir'] = getAttr_fromGSFileId($data['parentDir'],'path');
			$projectName = array_pop(split("/",$data['parentDir']));
			if($projectName == 'uploads') $projectName = "<span style='display:none;'>0</span>uploads";
			$data['project'] = $projectName;
		}
		// description
		if (isset($data['description'])){
			if(strlen($data['description']) > 50) $data['description'] = substr($data['description'], 0, 50).'...';
		}
		//filename 
		if (isset($data['pending'])){
			if (!isset($data['files'])){
				$data['filename']=$data['title'];
				$data['longfilename']= $data['title'];
				#$viewLog_state="enabled";
				#if ($data['pending']=="HOLD" || $data['pending']=="PENDING"){
				#	$viewLog_state = 'disabled';
				#}elseif(!is_file($GLOBALS['dataDir']."/".$data['logPath']) && !is_link($GLOBALS['dataDir']."/".$data['logPath'])){
				#	$viewLog_state = 'disabled';
				#}
				#$data['viewLog'] = "<tr><td>Log file:</td><td><a target=\"_blank\" href=\"workspace/workspace.php?op=openPlainFileFromPath&fnPath=".urlencode($data['logPath'])."\" class=\"$viewLog_state\">View</a></td></tr>";
				#$data['logPath'] = basename($data['logPath']);
			}else{
				$data['filename']= maxlength(basename($data['path']), 25);
				$data['longfilename']= basename($data['path']);
			}
		}else{
			$data['filename']= maxlength(basename($data['path']), 25);
			$data['longfilename']= basename($data['path']);
		}
		// TODO for debug. Temporal. To delete
        if ($data['filename']){
            if (!is_url($data['path'])){
    			$rfn      = $GLOBALS['dataDir']."/".$data['path'];
    			if (!is_file($rfn) && !is_dir($rfn)){
    				$data['filename']="ERROR-".$data['filename'];
    			}
            }
		}
		if(isset($data['shPath'])){
			$data['execDetails'] = "<tr><td>Execution details:</td><td><a href=\"javascript:callShowSHfile('".$data ['tool']."','".$data['shPath']."');\">Analysis parameters</a></td></tr>";
		}else{
			$data['execDetails'] = "";
		}
		if(isset($data['logPath'])){
			if (preg_match('/^\//',$data['logPath'])){
				$data['logPath'] = str_replace($GLOBALS['dataDir']."/","",$data['logPath']);
			}
			$viewLog_state="enabled";
			if ($data['pending']=="HOLD" || $data['pending']=="PENDING"){
				$viewLog_state = 'disabled';
			}elseif(!is_file($GLOBALS['dataDir']."/".$data['logPath']) && !is_link($GLOBALS['dataDir']."/".$data['logPath'])){
				$viewLog_state = 'disabled';
			}
			$data['viewLog'] = "<tr><td>Log file:</td><td><a target=\"_blank\" href=\"workspace/workspace.php?op=openPlainFileFromPath&fnPath=".urlencode($data['logPath'])."\" class=\"$viewLog_state\">View</a></td></tr>";
		}else{
			$data['viewLog'] = "";
		}

		$data['tools_button'] = 'none';

		// tools list
		if ( isset($data['data_type']) && ($data['data_type'] != "")){

			$tList = getToolsByDT($data['data_type'], 1);

			$data['tools_list'] = '<ul class="dropdown-menu pull-right" role="menu">';


			if(sizeof($tList) > 0) {

				foreach($tList as $t) {
					$data['tools_list'] .= '<li><a href="tools/'.$t[0].'/input.php?fn[]='.$data['_id_URL'].'" class="'.$t[0].'">'.file_get_contents('../tools/'.$t[0].'/assets/ws/icon.php').' '.$t[1].'</a></li>';
				}
				$data['tools_button'] = 'block';

			}else{
				$data['tools_list'] .= '<li><a href="javascript:;">No tools available for this Data Type</a></li>';
				$data['tools_button'] = 'none';
			}

			$data['tools_list'] .= '</ul>';


		}

		//data_type
		if ($data['data_type']){
			$dt = $GLOBALS['dataTypesCol']->findOne(array('_id' => $data['data_type']));
			$data['file_data_type'] = $dt['name'];
			$data['data_type'] = "<tr><td>Data type:</td><td>".$dt['name']."</td></tr>";
		}else{
			$data['data_type']="";
		}
		//notes
		if(isset($data['notes']) && strlen($data['notes']) ){
			$data['notes'] = "<tr><td>Notes:</td><td>".$data['notes']."</td></tr>";
		}else{
			$data['notes'] = "";
		}
		//paired sorted refGenome
		if(isset($data['paired']) ||  isset($data['sorted']) ){
			$row = "<tr><td>BAM properties:</td><td>";
			if (isset($data['paired']))
				$row.=$data['paired'];
			if (isset($data['sorted']))
				$row.= "&nbsp;" . $data['sorted'];
			$row.= "</td></tr>";
			$data['paired']=$row;
		}else{
			$data['paired'] = "";
		}
		if (isset($data['refGenome'])){
			$data['refGenome'] = "<tr><td>Assembly:</td><td>".$data['refGenome']."</td></tr>";
		}else{
			$data['refGenome']="";
		}
		//state and metadataLink  
		if (isset($data['validated']) && $data['validated'] ){
			$data['state'] = 'enabled';
			$data['metadataLink'] = "<li><a href=\"/getdata/editFile.php?fn[]=".$data['_id_URL']."\"><i class=\"fa fa-pencil\"></i> Edit file metadata</a></li>";
		}else{
			$data['state'] = 'disabled';
			$data['metadataLink'] = "<li><a href=\"/getdata/editFile.php?fn[]=".$data['_id_URL']."\"><i class=\"fa fa-exclamation-triangle\"></i> Validate file</a></li>";
		}

		//tools list (old school version :) delete
	
		//visualization
		if ( isset($data['format']) ){

			$data['vis_button'] = 'block';

			

			switch($data['format']){
				case 'PDB':
				case 'GRO':
					$ext = 'pdb';
					if ($pos = strrpos($data['filename'], '.')) {
						$name = substr($data['filename'], 0, $pos);
						$ext = substr($data['filename'], $pos);
					} else {
						$name = $data['filename'];
					}

					$e = ltrim($ext, ".");

					//if((strtolower($ext) == '.pdb') || (strtolower($ext) == '.gro')){
						$data['PDBView'] = "<li><a href=\"javascript:openNGL('".$data['_id']."', '".$name."', '".$e."');\"><i class=\"fa fa-window-maximize\"></i> Preview in 3D</a></li>";
						$data['NGLView'] = "<li><a href=\"visualizers/ngl/?user=".$_SESSION['User']['id']."&fn[]=".$data['_id']."\" target='_blank'><i class=\"fa fa-codepen\" ></i> View in NGL</a></li>";
					//}
					break;
				case 'BAM':
				case 'GFF':
				case 'GFF3':
				case 'BW':
					$data['jbrowseLink'] = "<li><a target=\"_blank\" href=\"/visualizers/jbrowse/index.php/?user=".$_SESSION['User']['id']."&fn[]=".$data['_id']."\"><i class=\"fa fa-align-right\"></i> View in JBrowse</a></li>";
					break;
				case 'JSON':
					if ($pos = strrpos($data['filename'], '.')) {
						$name = substr($data['filename'], 0, $pos);
						$ext = substr($data['filename'], $pos);
					} else {
						$name = $data['filename'];
					}

					$data['PDBView'] = "<li><a href=\"javascript:openTADbit('".$data['_id']."', '".$name."');\"><i class=\"fa fa-window-maximize\"></i> Preview in 3D</a></li>";
					$data['tadkitLink'] = "<li><a target=\"_blank\" href=\"visualizers/tadkit/index.php/?user=".$_SESSION['User']['id']."&fn=".$data['_id']."\"><i class=\"fa fa-cubes fa-rotate-180\"></i> View in TADkit</a></li>";
					break;
				default:
					$data['plainText'] = "<li><a href=\"javascript:;\">No visualization for this file type</a></li>";
					$data['vis_button'] = 'none';
			}
		}
		//inPaths and 
		if (isset($data['inPaths'])){
			$ins =$data['inPaths'];
			$data['inPaths']="<tr><td>Input files:</td><td>";
			if (count($ins)){
				foreach ($ins as $in){
				    if (is_array($in) && isset($in['path'])){
					$in = $in['path'];
				    }
				    $data['inPaths'].= "<div>";
				    $inFolders=split("/",dirname($in));
				    for ($i=count($inFolders)-1;$i>=1;$i--){
					$data['inPaths'].= "<span class=\"text-info\" style=\"font-weight:bold;\">".$inFolders[$i]."/</span>";
				    }
				    $data['inPaths'].= basename($in)."</div>";
				}
			}
			$data['inPaths'].="</td></tr>";
		}
		if (isset($data['input_files'])){
			$ins =$data['input_files'];
			$data['inPaths']="<tr><td>Input files:</td><td>";
			if (count($ins)){
				foreach ($ins as $in){
				    $f = getGSFile_fromId($in);	
				    $data['inPaths'].= "<div>";
				    $inFolders=split("/",dirname($f['path']));
				    for ($i=count($inFolders)-1;$i>=1;$i--){
					$data['inPaths'].= "<span class=\"text-info\" style=\"font-weight:bold;\">".$inFolders[$i]."/</span>";
				    }
				    $data['inPaths'].= basename($f['path'])."</div>";
				}
			}
			$data['inPaths'].="</td></tr>";
		}
		//rerunLink
		/*if (isset($data['inPaths']) && isset($data['tool'])){
			$tool = $GLOBALS['toolsCol']->findOne(array('_id' => $data['tool']));
			if (!empty($tool)){
				$formPath  = "/tools/".$data['tool']."/input.php";
			    $data['rerunLink'] ="<li><a href=\"$formPath?rerunDir=".$data['_id_URL']."\"><i class=\"fa fa-share\"></i> Rerun Project</a></li>";
			}
		}*/
		//viewResultsLink
		if (isset($data['tool']) ){
			$tool = $GLOBALS['toolsCol']->findOne(array('_id' => $data['tool']));
			//var_dump($tool);
			$data['toolname'] = $data['tool'];
			if (!empty($tool)){
				if (isset($tool['has_custom_viewer']) && $tool['has_custom_viewer'] === false){
				}else{
					$data['viewResultsLink']="
<div class=\"btn-group\" style=\"float:left; position:absolute; margin-top:-10px!important;margin-left:38px;\">
    	<button class=\"btn btn-xs purple-intense dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\" aria-expanded=\"false\"> 
						<i class=\"fa fa-eye\"></i>
						<i class=\"fa fa-angle-down\"></i>
		</button>
	  <ul class=\"dropdown-menu pull-right\" role=\"menu\">
						<li><a href=\"javascript:viewResults('".$data['_id_URL']."','".$data['tool']."');\"><i class=\"fa fa-file-text\"></i> View Results</a></li>
		</ul>
		</div>";
				}
			}
		}
		//analyses tool
		if (isset($data['tool'])){
			$tool = $GLOBALS['toolsCol']->findOne(array('_id' => $data['tool']));
			if (!empty($tool))
				$data['tool']="<tr><td>Tool:</td><td>".$tool['name'] ."</td></tr>";
		}
		//compressed
		$ext = pathinfo($data['path'], PATHINFO_EXTENSION);
		$ext = preg_replace('/_\d+$/',"",$ext);
		$content_type  = ( array_key_exists($ext, mimeTypes()) ? mimeTypes()[$ext] : "application/octet-stream");
		$data['openFunction'] = ($content_type == "text/plain" || $ext=="pdf" || preg_match('/image/',$content_type) || preg_match('/(e|o)\d+/',$ext) || in_array($data['format'],array("FASTQ","FASTA")) ? "openPlainFile" : "downloadFile");
		$data['compressionLink'] = "";
		if (! in_array($data['format'],array("BAM","PNG","JPG") ) ){
			switch (strtolower($ext)) {
				case 'tar':
					$func   = "untar";
					$img    = "fa fa-expand";
					$linkTxt= "Uncompress";
					break;
				case 'gz':
				case 'zip':
					$func   = "unzip";
					$img    = "fa fa-expand";
					$linkTxt= "Uncompress";
				case 'tgz':
					$func   = "untar";
					$img    = "fa fa-expand";
					$linkTxt= "Uncompress";
					break;
				case 'bz2':
					$func   = "bzip2";
					$img    = "fa fa-expand";
					$linkTxt= "Uncompress";
				default :
					$func   = "zip";
					$img    = "fa fa-file-zip-o";
					$linkTxt= "Compress";
			}
			$data['compressionLink'] = "<li><a  href=\"workspace/workspace.php?op=$func&fn=".$data['_id_URL']."\" class=\"enabled\"><i class=\"$img\"></i> $linkTxt</a></li>";
			//$data['compressionLink'] = "<li><a  href=\"javascript:;\" class=\"disabled\"><i class=\"$img\"></i> $linkTxt</a></li>";
		}

		return $data;
}

//update Mongo lastjobs
function updatePendingFiles($sessionId,$singleJob=Array()){
	$SGE_updated = Array(); // jobs to be monitored in next round. Stored in SESSION. Updated by checkPendingJobs.php (called by ajax)

    // get jobs from mongo[users][lastjobs]
    $lastjobs = getUserJobs($sessionId);

	if (count($lastjobs)){

      //classify jobs
      foreach ($lastjobs as $job){

        if (!isset($job['_id'])){
            continue;
        }
		$pid	 = $job['pid'];

        //get qstat info
        $jobProcess = getRunningJobInfo($pid,$job['launcher'],$job['cloudName']);

        // TODO: PMES will redirect log info to log_file. Now, info extracted from $jobProcess
        updateLogFromJobInfo($job['log_file'],$pid,$job['launcher']);
        
        //job keeps running: maintain original job data 
        if (count($jobProcess)){
            //keep monitoring
            $job['state']  = $jobProcess['state'];
            $SGE_updated[$pid]= $job;

        //job not running : edit SGE_updated to register the change
        // and consequently reload workspace (checkPendingJobs.php)
        }else{
            $SGE_updated[$pid]=$job;
            $SGE_updated[$pid]['state']="NOT_RUNNING";
        }
      }
    }

    //update session and save to mongo 
    saveUserJobs($sessionId,$SGE_updated);
    return 1;
}



function processPendingFiles($sessionId,$files){
	$SGE_updated = Array(); // jobs to be monitored. Stored in SESSION. Updated by checkPendingJobs.php (called by ajax)
	$filesPending= Array(); // files to be listed 
	$debug=0;

	// get files already in mongo
    $filesStored = Array();
    if ($files){
    	foreach ($files as $k => $v){
    		array_push($filesStored,$v['_id']);
        }
    }
	// get jobs from mongo[users][lastjobs]
	$lastjobs = getUserJobs($sessionId);

	if (!count($lastjobs)){
		return $filesPending;
	}

	if ($debug)
		print "<br><br/>JOBS DEL USER HAS [".count($lastjobs)."] JOBS <br/>\n";

	// classify jobs
	foreach ($lastjobs as $job){
		
		if (!isset($job['pid'])){
			continue;
		}
		$pid	 = $job['pid'];
		if ($debug)
			print "<br/>\nPID = [$pid] TOOL=".$job['toolId']." WORK_DIR=".$job['working_dir']." <br/>\n";			

	    //get qstat info
        $jobProcess = getRunningJobInfo($pid,$job['launcher'],$job['cloudName']);

        // TODO: PMES will redirect log info to log_file. Now, info extracted from $jobProcess
        updateLogFromJobInfo($job['log_file'],$pid,$job['launcher']);
	
	$title   = (isset($job['title'])?$job['title']:"Job ".$job['project']);
	$descrip = getJobDescription($job['description'],$jobProcess,$lastjobs);

	//
	//set as running job
	//
	if (count($jobProcess)){
		if ($debug)
		    print "RUNNING JOB";

            	//set dummy id
            	$dummyId  = $job['pid'];
            	//$dummyId  = createLabel()."_dummy";

		//get dummy parentDir
        	if ($job['hasProjectFolder']){
	                // show job in project dir
                	$parentDir = fromAbsPath_toPath($job['working_dir']);
        	}else{
                	// show job in output_dir (infered from stageout_data)
	                $parentDir= 0;
	                if($job['stageout_data']){
	                    $output_file_1 = $job['stageout_data']['output_files'][0];
	                    if ($output_file_1 && $output_file_1['file_path']){
	                        $parentDir = fromAbsPath_toPath(dirname($output_file_1['file_path']));
	                    }
	                }
	                if (!$parentDir)
	                    $parentDir = $_SESSION['User']['id']."/uploads"; 
            	}
            	//set dummy file
	   	$fileDummy = Array(
			'_id'     => $dummyId,
			'pid'     => $pid,
			//'path'  => outPath,
			'title'   => $title,
			'mtime'   => strtotime($jobProcess['submission_time']),
			'size'    => "",
			'visible' => 1,
			'tool'    => $job['toolId'],
			'parentDir'=> getGSFileId_fromPath($parentDir),
			'description'=> $descrip,
			'pending' => $jobProcess['state'],
			'shPath'  => fromAbsPath_toPath($job['submission_file']),
			'logPath' => fromAbsPath_toPath($job['log_file'])
           	);

	    	//list job in workspace
	  	$filesPending[$dummyId] = $fileDummy;

		//update job state in mongo
		$job['state'] = $jobProcess['state'];
		$SGE_updated[$pid]=$job;

	//    
        //processing job non running anymore
	//
	}else{
		unset($_SESSION['errorData']);
		$job_in_err=0;

		//get tool info
		$tool=getTool_fromId($job['toolId'],1);
		if (! isset($tool['_id'])){
			$_SESSION['errorData']['Internal'][]="toolId '".$job['toolId']."' received from JobTool not registered";
			$_SESSION['errorData']['Error'][]="Cannot obtain results from '$title' in folder '".basename($job['working_dir'])."'. Job metadata is not valid.";
			$job_in_err=1;
			continue;
		}
		if ($debug){
			print "<br>\nBuilding outsput from toolINFO + ".$job['stageout_file']." + stageout_data.\n<br/>STAGEOUT_DATA.<br>";
			var_dump($job['stageout_data']);		
		}

		// build output list merging: stageout_file + stageout_data + tool defintion data
		$outs_files = build_outputs_list($tool,$job['stageout_data'],$job['stageout_file']);
		if (count($outs_files)==0){
		    $job_in_err = 1;
		}
		// checking each expected job output
		foreach ($outs_files as $out_name => $outs_data){
            if ($debug){
                print "<br/>--------------------------------------------------------------<br/>";
                print "<br/>REGISTERING output_file with KEY NAME = $out_name DATA = <br/>\n";
			    var_dump($outs_data);
		    }
		    // evaluate output_file requirement
		    $out_def = $tool['output_files'][$out_name];
		    $is_required    = output_is_required($out_def);
		    $allow_multiple = output_allow_multiple($out_def);
			
		    //check requirement : allow multiple
		    if ($allow_multiple === false){
			if (count($outs_data)>1){
				$_SESSION['errorData']['Error'][]="Tool definition does not allow multiple instances for '$out_name', but the execution returned ".count($outs_data).". Registering only one of them.";
			}
			$outs_data = Array($outs_data[0]);
		    }
				
		    // start 	
		    foreach ($outs_data as $out_data){
		        if ($debug)
				print "<br/> START OUTPUT ITEM REGISTRATION<br/>\n";

		    	//check requirement : required
			if (!isset($out_data['file_path'])){
				if ($is_required){
					$_SESSION['errorData']['Error'][]="Expected tool output file '$out_name' not found.";
					$job_in_err=1;
				}
		       		if ($debug){
					print "<br/>file_path NO SET, but not required. Continuing. The merged metadata is:<br/>\n";
					var_dump($out_data);
				}
				continue;
			}

            // resolve virtual path to local absolute path
            $rfn = resolvePath_toLocalAbsolutePath($out_data['file_path'], $job);
/*
            if (preg_match('/^\//',$out_data['file_path'])){
	                    // file_path is an absolut path
        	            if (preg_match('/^'.preg_quote($job['root_dir_virtual'],'/').'/',$out_data['file_path'])){
	                        //PMES mounts dataDir/user_id as root_dir_virtual
	                        if ($job['launcher'] == "PMES"){
	                            $rfn = str_replace($job['root_dir_virtual'],$GLOBALS['dataDir'].$_SESSION['User']['id'],$out_data['file_path']);
        	        	     //SGE finds mounted dataDir as root_dir_virtual
                	        }elseif ($job['launcher'] == "SGE"){
                            	    $rfn = str_replace($job['root_dir_mug'],$GLOBALS['dataDir'],$out_data['file_path']);
				}
                     		// direct from file_path
  			    }else{
   				$rfn = $out_data['file_path'];
   			    }
  		 	}else{
	                    // file_path is only a file name (dataDir/userid)
	                    if (!preg_match('/\//',$out_data['file_path'])){
                        	$rfn = $GLOBALS['dataDir'].$_SESSION['User']['id']."/".$job['project']."/".$out_data['file_path'];
	                    // file_path is relative to user data directory (dataDir/userid)
        	            }elseif (preg_match('/^'.$job['project'].'/',$out_data['file_path'])){
	                        $rfn = $GLOBALS['dataDir'].$_SESSION['User']['id']."/".$out_data['file_path'];
	                    // file_path contains $(working_dir) tag
	                    }elseif(preg_match('/(working_dir)/',$out_data['file_path'])){
	                        $rfn = str_replace("$(working_dir)",$job['working_dir']."/",$out_data['file_path']);
	                    // file_path is relative to app working directory (dataDir/userid/proj)
	                    }else{
	                        $rfn = $job['working_dir']."/".$out_data['file_path'];
                  	    }
            }
 */

		$outPath  = fromAbsPath_toPath($rfn);
		$fileId   = getGSFileId_fromPath($outPath);
		if ($debug)
            print "PID = [$pid] file_path=".$out_data['file_path']." --> fn=$outPath rfn=$rfn . Has Id? $fileId <br/>\n";


                //convert stage out data into MuGFile

                //associated_files and associated_id/_master: convert to fileIds 
                $metaReferences=array();
                if (isset($out_data['meta_data']['associated_id']) || isset($out_data['meta_data']['associated_master']) ){
                    $assoc = (isset($out_data['meta_data']['associated_id'])?$out_data['meta_data']['associated_id']:$out_data['meta_data']['associated_master']);
                    $assoc_rfn = resolvePath_toLocalAbsolutePath($assoc, $job);
                    $assoc_fn  = fromAbsPath_toPath($assoc_rfn); 
                    $assoc_id  = getGSFileId_fromPath($assoc_fn);
                    if ($assoc_id =="0"){
                        $out_data['meta_data']['associated_id']= $assoc;
                    }else{
                        $metaReferences[$assoc_id] = "associated_id";
                        $out_data['meta_data']['associated_id']= $assoc_id;
                    }
                    if (isset($out_data['meta_data']['associated_master']) ){unset($out_data['meta_data']['associated_master']);}
                    if ($debug){
                        print "THIS META HAS FILE REFERENCES. Saving associated_id='$assoc_id' instead of the original '$assoc'. If no ID. adding to refs:<br/>\n";
                        var_dump($metaReferences);
                    }
                }
                if (isset($out_data['meta_data']['associated_files']) ){
                    $assocs=array();
                    foreach ($out_data['meta_data']['associated_files'] as $assoc){
                        $assoc_rfn = resolvePath_toLocalAbsolutePath($assoc, $job);
                        $assoc_fn  = fromAbsPath_toPath($assoc_rfn); 
                        $assoc_id  = getGSFileId_fromPath($assoc_fn);
                        if ($assoc_id == "0"){
                            array_push($assocs,$assoc);
                        }else{
                            array_push($assocs,$assoc_id);
                            $metaReferences[$assoc_id] = "associated_files";
                        }
                        if ($debug){
                            print "THIS META HAS FILE REFERENCES. Saving associated_files='$assoc_id' instead of the original '$assoc'. If no ID. adding to refs:<br/>\n";
                            var_dump($metaReferences);
                        }
                    }
                    $out_data['meta_data']['associated_files']= $assocs;
                }

        		//sources : convert to fileIds and rename to source_id
                if (isset($out_data['sources'])){
	        		$sources=array();
                    foreach ($out_data['sources'] as $source_path){
                        $source_rfn = resolvePath_toLocalAbsolutePath($source_path, $job);
                        /*
	                    if (preg_match('/^'.preg_quote($job['root_dir_virtual'],'/').'/',$source_path)){
        	                //PMES mounts dataDir/user_id as root_dir_virtual
	                        if ($job['launcher'] == "PMES"){
	                            $source_rfn = str_replace($job['root_dir_virtual'],$GLOBALS['dataDir'].$_SESSION['User']['id'],$source_path);
	                        //SGE finds mounted dataDir as root_dir_virtual
	                        }elseif ($job['launcher'] == "SGE"){
	                            $source_rfn = str_replace($job['root_dir_virtual'],$GLOBALS['dataDir'],$source_path);
	                        }
	                     // direct from file_path
	                    }else{
        	                $source_rfn = $source_path;
                        }
                         */
                        $source_fn = fromAbsPath_toPath($source_rfn);
        			    $source_Id = getGSFileId_fromPath($source_fn);

                        array_push($sources,$source_Id);

                        if ($debug){
                            print "SOURCES ORI = $source_path RFN = $source_rfn  FN = $source_fn ID = $source_Id <br/>";
                        }
    			    }
	            	$out_data['source_id'] = $sources;
                    unset($out_data['sources']);
    		    }
                    

        		//validate new file data
		        list($out_validation_score,$out_mug) = validateMugFile($out_data,true);
        		if ($debug)
            print "<br>Out file model validation returned VALIDATION_SCORE= $out_validation_score and MuG file has ".count($out_mug)." fields<br/>\n";


				// job successfully finished and already in mongo. Update medatada

				if ($fileId){
					$_SESSION['errorData']['Warning'][] = "Expected tool outfile '".basename($rfn)."' is already registered. Updating metadata only.";
			        if ($debug)
				        print "Already in mongo. Adding metadata if there is any<br>";
					logger("JOB $pid FINISHED SUCCESSFULLY");
					
					if ($out_mug){
						//save metadata
                        list($out_vre,$metadata) = getVREfile_fromFile($out_mug);
                        if ($out_validation_score == 1 || $out_validation_score == 0){ 
                            $metadata['validated'] = false;
                        }
				        if ($debug){
							print "<br>VRE METADATA SEND TO addMetadata IS:<br/>\n";
							var_dump($metadata);
						}
						$ok = addMetadataBNS($fileId,$metadata);
						if ($ok == "0")
							$_SESSION['errorData']['Warning'][] = "Sorry, could update '".basename($rfn)."' metadata.";
					}

				// job successfully finished but not yet on mongo. Save output

				}elseif (is_file($rfn) && $out_mug && $out_validation_score!=0) {
			        	if ($debug)
						print "<br>JOB $pid FINISHED AND NOT YET IN MONGO ($outPath). Saving!<br/>\n";

					//register file and save updated metadata
					list($out_vre,$metadata) = getVREfile_fromFile($out_mug);
			        if ($debug){
						print "<br>VRE METADATA SEND TO saveResults IS:<br/>\n";
						var_dump($metadata);
                    }
                    if ($out_validation_score == 1 || $out_validation_score == 0){ 
                        $metadata['validated'] =false;
                    }

			        if ($debug){
						print "<br>VRE METADATA SEND TO saveResults IS:<br/>\n";
                        var_dump($metadata);
                    }
					$fileInfo = saveResults($outPath,$metadata,$job); 

					//list new metadata in table
                    if (is_array($fileInfo)){
                        $fileId = $fileInfo['_id'];
					    if(!$metadata['visible'] === false)
    						$filesPending[$fileId]=$fileInfo;
					    if ($debug)
    						print "\n<br/><br/>SAVED successfully ".$fileInfo['_id']."!<br/>\n";
					}
					if($fileInfo == "0"){
						$_SESSION['errorData']['Error'][]="Expected tool outfile '$out_name' found (".basename($rfn)."), but not correctly registered";
						$job_in_err = 1;
					}else{
						logger("JOB $pid FINISHED SUCCESSFULLY");
					}
					
				// job successfully finished but file metada not valid. Setting error mode 
					
				}elseif (is_file($rfn) && !$out_mug) {
			       	if ($debug){
                            print "<br/>JOB $pid FINISHED BUT INVALID FILE ($outPath). SET ERROR <br>";
	                		print "<br/><br>The invalid file is:<br/>\n";
				            var_dump($out_mug);
					}
					$_SESSION['errorData']['Error'][]="Expected tool outfile '$out_name' found (".basename($rfn)."), but not registered. Missing required metadata.";
					$job_in_err=1;

				}else{
					$_SESSION['errorData']['Error'][]="Expected tool outfile '$out_name' not found (".basename($rfn).").";
			        if ($debug){
						print "<br/>JOB $pid FINISHED BUT NO EXPECTED OUTFILE '$rfn' FOUND  IN DISK. Set ERROR<br>";
					}
					$job_in_err=1;
                }
                
                // Update metadata of other files referring current fileId  (associated files)
                if ($job_in_err==0 &&  count($metaReferences)){
                    if ($debug)
                        print "<br/>Update metadata of other FILE REFERENCES afecting current fileId<br/>\n";

                    foreach ($metaReferences as $assoc_id => $assoc_type){
                        $file_assoc = getGSFile_fromId($assoc_id,"onlyMetadata");
                        if ($assoc_type == "associated_files")
                            $file_assoc['associated_id'] = $fileId;
                        if ($assoc_type == "associated_id"){
                            $assocs=array();
                            foreach ($file_assoc['associated_files'] as $a){
                                if ($a == $outPath || $a == $rfn){
                                    array_push($assocs,$fileId);
                                }else{
                                    array_push($assocs,$a);
                                }
                            }
                            $file_assoc['associated_files']=$assocs;
                        }
                        $ok = addMetadataBNS($assoc_id,$file_assoc);
                        if ($ok == "0")
                            $_SESSION['errorData']['Warning'][] = "Sorry, could not add reference to '".basename($rfn)."' in the metadata of the associated file '$assoc_id'";
                        $ff = getGSFile_fromId($assoc_id);
                        if ($debug){
                            print "<br>Updating  metadata for assoc file $assoc_id done. This is:<br>\n";
                            var_dump($ff);
                        }
                    }
                }


			    }
            }
	// OJO  Uncomment only for debugging output_files registry
	//$SGE_updated[$job['pid']]=$job;
	//$job_in_err=0;


	// jobs nor finished nor running: in error OR deleted OR SESSION[sge] not updated

	if ($debug)
		print "<br/>IS JOB IN ERR? ($job_in_err)<br/>\n";


	if ($job_in_err){
                logger("JOB $pid FINISHED but with errors");
                $logFileP = $job['log_file'];
		$logFile  = fromAbsPath_toPath($job['log_file']);

				// job has log
                if (is_file($logFileP) ){
                    // move and redefine log and SH file if internalTool
                    if ($job['hasProjectFolder'] === false ){
                        // right now, redifinition done inside saveResults
                    }
					if ($debug)
						print "<br>JOB IN ERROR $fileId storing LOG $logFile <br>";
					
					$logId  = getGSFileId_fromPath($logFile);
					if (!$logId){
						$logMeta['description'] = "Job log file";
						$logMeta['format']      = "ERR";
						$metaDataLog = prepMetadataLog($logMeta,$logFile);
	
						$logInfo = saveResults($logFile,$metaDataLog,$job);
						if (is_array($logInfo))
							$filesPending[$logInfo['_id']]=$logInfo;
					}
					
				// job has neither log nor all outfiles
				}else{
					if ($debug)
						print "<br>JOB $pid NO log (".$logFile.") NO output ($outPath) <br>";
					/*
					$proj =  $GLOBALS['dataDir']."/".dirname($outPath);
						if (is_dir($proj)){
							$projContent = glob($proj.'/*.e[0-9]*', GLOB_BRACE);
							if (count($projContent)){
								$errFile = $projContent[0];
								if (is_file($errFile)){
									$err_fn = dirname($outPath) ."/". pathinfo($errFile,PATHINFO_BASENAME);
									$metaDataErr = prepMetadataLog($metaData,$err_fn,"ERR");
									$errInfo = saveResults($err_fn,$metaDataErr,$job);
									$filesPending[$errInfo['_id']]=$errInfo;
								}
							}else{
								$_SESSION['errorData']['Error'][]="Execution ".$job['title']." '".basename($outPath)."' failed with neither log nor error file.";
							}
							}else{
							$_SESSION['errorData']['Error'][]="Execution ".$job['title']." '".basename($outPath)."' failed with neither log nor error file.";
						}
					*/
		 		}
			}
		}
	}
	
	if ($debug){
		print "<br/><br/>FINAL FILES PENDING yes? Num=<br/>\n";
		var_dump(count($filesPending));
		print "<br/><br/>\n";
	}

	//update session and save to mongo
	saveUserJobs($sessionId,$SGE_updated);
	return $filesPending;
}



function saveResults($filePath,$metaData=array(),$job=array(),$rfn=0,$asRoot=0){

	// NOT saving internal or temporal files
        //if (in_array($ext,$GLOBALS['internalResults']) || preg_match('/^\./',basename($filePath)) ){
        //        return 1;
        //}

	// check given filePath
	if ($rfn == 0)
		$rfn  = $GLOBALS['dataDir']."/".$filePath;
	if (preg_match('/^\//',$filePath)){
		$rfn      = $filePath;
		$filePath = str_replace($GLOBALS['dataDir']."/","",$rfn);
	}
	if (!is_file($rfn) || !filesize($rfn)){
		if (!is_dir($rfn)){
			$_SESSION['errorData']['Error'][]="Execution result '$rfn' does not exist or has size 0. Cannot save it into database";
			return 0;
		}
	}

	# prepare file metaData
	$metaData = prepMetadataResult($metaData,$filePath,$job);

    # prepare Parent
    $parentPath = dirname($filePath);
    $parentId = getGSFileId_fromPath($parentPath,$asRoot);
    if (!$parentId){
        if (isset($job['hasProjectFolder']) &&  $job['hasProjectFolder']===false){
            $parentPath = fromAbsPath_toPath($job['output_dir']);
            $parentId = getGSFileId_fromPath($parentPath,$asRoot);
        }
        if (!$parentId){
            $_SESSION['errorData']['Error'][]="Cannot save result '".basename($filePath)."' at '$parentPath'. This parent directory does not exist or is unaccessible";
            return 0;
        }
    }

	#save Data
	$fileId = createLabel();
	
	$insertData=array(
		'_id'   => $fileId,
		'owner' => $_SESSION['User']['id'],
		'size'  => filesize($rfn),
		'path'  => $filePath,
		'mtime' => new MongoDate(filemtime($rfn)),
		'parentDir' => $parentId
	);

	#save to MONGO
	$fnId = uploadGSFileBNS($filePath, $rfn, $insertData,$metaData, FALSE,$asRoot);

	if ($fnId){
		$insertData['mtime'] = $insertData['mtime']->sec;
		return array_merge($insertData,$metaData);
	}else{
		$_SESSION['errorData']['mongoDB'][]="Cannot save execution result 'basename($filePath)' into database. Stored only on disk";
		return 0;
	}
}
function  build_outputs_list($tool,$stageout_job,$stageout_file){

	$outs_meta= Array();

	// check tool output_files

	if (!isset($tool['output_files']) || count($tool['output_files'])==0){
		$_SESSION['errorData']['Internal'][]="Tool ".$tool['name']." has not list of 'output_files'. Invalid tool registration";
		$_SESSION['errorData']['Error'][]="Cannot obtain results from project '".dirname($stageout_file)."'";
		return $outs_meta;
	}

	// parse stageout file
	$stageout_meta = Array();
        if (isset($stageout_file) && is_file($stageout_file) ){
        	$content = file_get_contents($stageout_file);
		$data    = json_decode($content, true);
		if (count($data) == 0 || count($data['output_files'])==0){
			$_SESSION['errorData']['Warning'][]="Tool stageout file '".basename($stageout_file)."' is empty or bad formatted";
		}
		//index by name
                foreach ($data['output_files'] as $out){
			if (isset($out['name'])){
				if (!isset($stageout_meta[$out['name']]))
					$stageout_meta[$out['name']]=Array();
				array_push($stageout_meta[$out['name']],$out);
                        }else{
				$_SESSION['errorData']['Warning'][]="Tool stageout file '".basename($stageout_file)."' is bad formatted. Missing 'name' in 'output_files' list";
				continue;
			}
		}
	}
	// check stageout data
	$stageout_data = Array();
	if ($stageout_job){
		if (!isset($stageout_job['output_files'])){
			$stageout_data=array();
		}else{
		    foreach ($stageout_job['output_files'] as $out){
			if (isset($out['name'])){
				if (!isset($stageout_data[$out['name']]))
					$stageout_data[$out['name']]=Array();
				array_push($stageout_data[$out['name']],$out);
                        }else{
				$_SESSION['errorData']['Warning'][]="Tool job has stageout data is bad formatted. Missing 'name' in 'output_files' list";
				continue;
			}
		    }   
		}
	}

	// merging stageout file and stageout data
	$stageout_meta = array_merge($stageout_data,$stageout_meta);

	// merging file data from tool and stageout_file

	foreach ($tool['output_files'] as $out_name => $out_data){
		$outs_meta[$out_name] = Array();
		if (!isset($out_data['file'])){
			$out_data['file']=Array();
			//print "Tool has no file attribute for output_file '$out_name'";
		}
                //print "<br/>META FROM JSON<br/>\n";
                //var_dump($out_data['file']);

		if (!isset($stageout_meta[$out_name])){
			//print "Tool stageout file/data has no metadata for output_file '$out_name'.";
			array_push($outs_meta[$out_name],$out_data);
			continue;
		}

		foreach ($stageout_meta[$out_name] as $stg_data){
                	//print "<br/>META FROM STG<br/>\n";
	                //var_dump($stg_data);

			//create  merged file data
			if (isset($out_data['file']['source_id']))
				unset($out_data['file']['source_id']);
			if (isset($stg_data['name']))
				unset($stg_data['name']);
			//$file_merged  = array_merge_recursive($out_data['file'],$stg_data);
			$file_merged  = array_merge_recursive_distinct($out_data['file'],$stg_data);

                	//print "<br/>RESULTING FILE <br/>\n";
	                //var_dump($file_merged);

			array_push($outs_meta[$out_name],$file_merged);
		}
	}
	return $outs_meta;
}


function topDir() {
	return ($_SESSION['curDir'] == $_SESSION['userId']);
}

function upDir() {
	if (!topDir())
		$_SESSION['curDir'] = dirname($_SESSION['curDir']);
}

function downDir($fn) {
	$fnData = $GLOBALS['filesCol']->findOne(array('_id' => $fn));
	if (! empty($fnData)) {
	if (isset($fnData['type']) && $fnData['type'] == "dir"){
		$_SESSION['curDir'] = $fn;
	}else{
		$_SESSION['errorData'][error][]="Cannot change directory. $fn is not a directory ";
	}
	}
}

// return sum of FS or Mongo directory (in bytes)

function getUsedDiskSpace($fn = '',$source="fs") {
    if (!$fn)
        $fn = $_SESSION['User']['id'];
    if ($source != "fs"){
    	if (!preg_match('/^\//',$fn) )
		 $fn = $GLOBALS['dataDir']."/".$fn;
	$data = explode("\t", exec("du -sb $fn"));
	return $data[0];
    }else{
	//$fnId= getGSFileId_fromPath($fn);
	return calcGSUsedSpace($fn);
    }
}

// return user diskquota from mongo

function getDiskLimit($login = '') {
        if (!$login){
                $login  = $_SESSION['User']['_id'];
        }
        $sp = getUser_diskQuota($login);
        if ($sp === false){
                return $GLOBALS['disklimit'];
        }else{
                return $sp;
        }
}



/*
function navigation() {
	$cdir = $_SESSION['curDir'];

	$fnData = $GLOBALS['filesCol']->findOne(array('_id' => $cdir));
	if (empty($fnData)){
		$_SESSION['errorData']['error'][]="Current directory is not found. Restart <a href=\"".$GLOBALS['managerDir']."/gesUser.php?op=loginForm\">login</a>, please";
		return false;
	}
	$d = (isset($fnData['parentDir'])? $fnData['parentDir'] : 0);
	
	$dirs = array();
	if (!topDir()) {
		while ($d and ( $d != $_SESSION['userId'] ) ) {
			$dirs[] = "<a href=\"".$GLOBALS['managerDir']."workspace.php?op=gotoDir&fn=$d\">" . basename($d). "</a>";
			$fnData = $GLOBALS['filesCol']->findOne(array('_id' => $d));
		if (empty($fnData))
			$_SESSION['errorData'][error][]="Directory $d not found. Error in navigation menu";
		$d = (isset($fnData['parentDir'])? $fnData['parentDir'] : 0);
		}
		$dirs[] = "<a href=\"".$GLOBALS['managerDir']."workspace.php?op=gotoDir&fn=$d\">".basename($d)."</a>";
	}
	return join(' > ', array_reverse($dirs)) . "> " . pathinfo($cdir, PATHINFO_FILENAME);
}
*/

function formatSize($bytes) {
	$types = array('B', 'KB', 'MB', 'GB', 'TB');
	for ($i = 0; $bytes >= 1024 && $i < ( count($types) - 1 ); $bytes /= 1024, $i++);
	return( round($bytes, 2) . "" . $types[$i] );
}


function downloadFile( $rfn ){
		$fileInfo      = pathinfo($rfn);
		$fileName      = $fileInfo['basename'];
		$fileExtension = $fileInfo['extension'];
		$fileExtension = preg_replace('/_\d+$/',"",$fileExtension);
		$content_type  = (array_key_exists($fileExtension, mimeTypes()) ? mimeTypes()[$fileExtension] : "application/octet-stream");
		$size = filesize($rfn);
		$offset = 0;
		$length = $size;

		if(isset($_SERVER['HTTP_RANGE'])){
			preg_match('/bytes=(\d+)-(\d+)?/', $_SERVER['HTTP_RANGE'], $matches);
			$offset = intval($matches[1]);
			$length = intval($matches[2]) - $offset;

			$fhandle = fopen($rfn, 'r');
			fseek($fhandle, $offset); // seek to the requested offset, this is 0 if it's not a partial content request
			$data = fread($fhandle, $length);
			fclose($fhandle);

			header('HTTP/1.1 206 Partial Content');
			header('Content-Range: bytes ' . $offset . '-' . ($offset + $length) . '/' . $size);
		}
		header("Content-Disposition: attachment;filename=".$fileName);
		header('Content-Type: '.$content_type);
		header("Accept-Ranges: bytes");
		header("Pragma: public");
		header("Expires: -1");
		header("Cache-Control: no-cache");
		header("Cache-Control: public, must-revalidate, post-check=0, pre-check=0");
		header("Content-Length: ".filesize($rfn));
		$chunksize = 8 * (1024 * 1024); //8MB (highest possible fread length)

		if ($size > $chunksize){
			$handle = fopen($rfn,'rb');
			$buffer = '';
			while (!feof($handle) && (connection_status() === CONNECTION_NORMAL)) {
				$buffer = fread($handle, $chunksize);
				print $buffer;
				ob_flush();
				flush();
			}
			if(connection_status() !== CONNECTION_NORMAL) {
				echo "Connection aborted";
			}
			fclose($handle);
		}else{
			ob_clean();
			flush();
			readfile($rfn);
		}
		exit(0);
}

function refresh_token($force=false){

    if (!$_SESSION['User']['Token']['access_token']){
       ob_clean();
       header('Location: '.$GLOBALS['URL'].'errors/errordb.php?msg=MuG Authentification Session Expired. <a href='.$GLOBALS['URL'].'>Login again</a>');
    }
    $existingTokenO = new AccessToken($_SESSION['User']['Token']);

    $provider = new MuG_Oauth2Provider\MuG_Oauth2Provider(['redirectUri'=> 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF']]);

    if ($force || $existingTokenO->hasExpired()) {
        try {
            $newTokenO = $provider->getAccessToken('refresh_token', [ 'refresh_token' => $existingTokenO->getRefreshToken()]);
            if ($newTokenO->getToken()){
                $newToken  = json_decode(json_encode($newTokenO),true);
            }
        }
        catch (Exception $e){
            $_SESSION['errorData']['Error'][] = "Cannot validate token from refresh token.";
            $_SESSION['errorData']['Error'][] = $e->getMessage();
            return false;
        }

        // save in mongo
        $user = $_SESSION['User']; 
        $user['Token'] = $newToken; 
        updateUser($user);
    
        // load new token in session
        $_SESSION['User']['Token'] = $newToken;
        return true;

    }else{
        $_SESSION['errorData']['Warning'][] = "Access token not expired yet. <a href='applib/refreshToken.php?force=1'>Force refresh</a>";
        return false;
    }

}


/*
function downloadFileSmall( $rfn ){
		$fileInfo	= pathinfo($rfn);
		$fileName  = $fileInfo['basename'];
		$fileExtension   = $fileInfo['extension'];
		$content_type = (array_key_exists($fileExtension, mimeTypes()) ? mimeTypes()[$fileExtension] : "application/octet-stream");

		header("Content-Disposition: attachment;filename=\"" . basename($rfn) . "\"");
		header('Content-Type: ' . $contentType);
		header("Content-Length: " .filesize($rfn));

		print passthru("/bin/cat \"$rfn\"");
}
*/


function mimeTypes() {
	$mime_types = array(
		"log" => "text/plain",
		"txt" => "text/plain",
		"md"  => "text/plain",
		"err" => "text/plain",
		"out" => "text/plain",
		"csv" => "text/plain",
		"gff" => "text/plain",
		"gff3"=> "text/plain",
		"wig"=> "text/plain",
		"bed"=> "text/plain",
		"bedgraph"=> "text/plain",
		//"sh" => "application/x-sh",
		"sh" => "text/plain",
		"pdb" => "chemical/x-pdb",
		"crd" => "chemical/x-pdb",
		"xyz" => "chemical/x-xyz",
		"cdf" => "application/octet-stream",
		"xtc" => "application/octet-stream",
		"trr" => "application/octet-stream",
		"gro" => "application/octet-stream",
		"dcd" => "application/octet-stream",
		"exe" => "application/octet-stream",
		"gtar" => "application/octet-stream",
		"bam"=> "application/octet-stream",
		"sam"=> "application/octet-stream",
		"tar" => "application/x-tar",
		"gz" => "application/application/x-gzip",
		"tgz" => "application/application/x-gzip",
		"z" => "application/octet-stream",
		"rar" => "application/octet-stream",
		"bz2" => "application/x-gzip",
		"zip" => "application/zip",
		"h" => "text/plain",
		"htm" => "text/html",
		"html" => "text/html",
		"gif" => "image/gif",
		"bmp" => "image/bmp",
		"ico" => "image/x-icon",
		"jfif" => "image/pipeg",
		"jpe" => "image/jpeg",
		"jpeg" => "image/jpeg",
		"jpg" => "image/jpeg",
		"rgb" => "image/x-rgb",
		"svg" => "image/svg+xml",
		"png" => "image/png",
		"tif" => "image/tiff",
		"tiff" => "image/tiff",
		"ps" => "application/postscript",
		"eps" => "application/postscript",
		"js" => "application/x-javascript",
		"pdf" => "application/pdf",
		"doc" => "application/msword",
		"xls" => "application/vnd.ms-excel",
		"ppt" => "application/vnd.ms-powerpoint",
		"tsv" => "text/tab-separated-values");
	return $mime_types;
}

/*
function check_key_repeats($key, $hash) {
	if (!isset($key) || !isset($hash)) {
		return NULL;
	}
	if (array_key_exists($key, $hash)) {
		$key++;
		$key = check_key_repeats($key, $hash);
		return $key;
	} else {
		return $key;
	}
}
*/

function return_bytes($val) {
	$val = trim($val);
	$last = strtolower($val[strlen($val)-1]);
	switch($last) {
		case 'g':
			$val *= 1024;
		case 'm':
			$val *= 1024;
		case 'k':
			$val *= 1024;
	}
	return $val;
}



// resolve virtual path (relative or absolutes) to local absolute path
function resolvePath_toLocalAbsolutePath($path,$job){

    $rfn ="";
    // file_path is an absolute path
    if (preg_match('/^\//',$path)){
        if (preg_match('/^'.preg_quote($job['root_dir_virtual'],'/').'/',$path)){
             //PMES mounts dataDir/user_id as root_dir_virtual
	         if ($job['launcher'] == "PMES"){
                 $rfn = str_replace($job['root_dir_virtual'],$GLOBALS['dataDir'].$_SESSION['User']['id'],$path);
                 
      	     //SGE finds mounted dataDir as root_dir_virtual
            }elseif ($job['launcher'] == "SGE"){
           	    $rfn = str_replace($job['root_dir_mug'],$GLOBALS['dataDir'],$path);
            }
        // direct from file_path
  		}else{
   		    $rfn = $path;
        }

    // file_path is relative
    }else{
        // file_path is only a file name (file)
	    if (!preg_match('/\//',$path)){
            $rfn = $GLOBALS['dataDir'].$_SESSION['User']['id']."/".$job['project']."/".$path;

        // file_path is relative to user data directory (prj/file)
        }elseif (preg_match('/^'.$job['project'].'/',$path)){
	        $rfn = $GLOBALS['dataDir'].$_SESSION['User']['id']."/".$path;
        
        // file_path is relative to root directory (userid/prj/file)
        }elseif (preg_match('/^'.$_SESSION['User']['id'].'/',$path)){
	        $rfn = $GLOBALS['dataDir']."/".$path;
        
        // file_path contains $(working_dir) tag
	    }elseif(preg_match('/(working_dir)/',$path)){
            $rfn = str_replace("$(working_dir)",$job['working_dir']."/",$path);

        // file_path is relative to app working directory (userid/proj/file)
	    }else{
	         $rfn = $job['working_dir']."/".$path;
        }
    }
    //clean slashes
    $rfn = preg_replace('#/+#','/',$rfn);

    //return absolute path
    return $rfn;
}

function deleteFiles($fns){
    if (!is_array($fns)){
       $fns = array($fns); 
    }
    $result  = true;
    foreach($fns as $fn){
        //print "<br/>DELTETING file with ID = $fn<br/>";
        $file     = getGSFile_fromId($fn);	
        if (!$file){
            $_SESSION['errorData']['Error'][]="Cannot delete file with id '$fn'. Entry not found";
            $result = false;
            continue;
        }

        // check file exists
        $file_fn  = $file['path'];
        $file_rfn = $GLOBALS['dataDir']."/$file_fn";
        //print " PATH = $file_fn  RFN = $file_rfn<br/>";
        if (!file_exists($file_rfn)){
            $_SESSION['errorData']['Error'][]="Cannot delete file with id '".basename($file_fn)."'. File not found.";
            $result = false;
            continue;
        }

        // delete file from DMP
        $r = deleteGSFileBNS($fn);
        if ($r == 0){
            $_SESSION['errorData']['Error'][]="Cannot delete file '".basename($file_fn)."'. Cannot delete entry from the repository.";
            $result = false;
            continue;
        }

        // delete file from disk
        unlink($file_rfn);
        if (error_get_last()){
            $_SESSION['errorData']['Error'][]="Errors encountered while deleting file '".basename($file_fn)."'.";
            $_SESSION['errorData']['Error'][]=error_get_last()["message"];
            $result = false;
            continue;
        }

        // if is an associated file, update master file
       if (isset($file['associated_id'])){
            $master_id = $file['associated_id'];
            $master    = getGSFile_fromId($master_id,"onlyMetadata");
            if ($master){
                //print "FILE IS an associated FILE! update $master_id<br/>";
                if (($k = array_search($fn, $master['associated_files'])) !== false) {
                    unset($master['associated_files'][$k]);
                    $r = addMetadataBNS($master_id,$master);
    		        if ($r == "0" ){
    			        $_SESSION['errorData']['Error'][] = "File '".basename($file_fn)."' successfully deleted, but cannot update its master file $master_id metadata";
    			        $result = false;
                        continue;
    		        }
                }
            }
    
       // if has associated files, delete them
       }elseif (isset($file['associated_files'])){

            //print "FILE  HAS  associated files! deleteing them ! <br/>";
            foreach ($file['associated_files'] as $assoc_id){
                $r = deleteFiles($assoc_id);
               if (!$r){
                   $_SESSION['errorData']['Warning'][]= "File '".basename($file_fn)."' successfully deleted, but  not its associated file ($assoc_id).";
			       $result = false;
               }
           }
       }
       //print "RESULT FOR ID = $fn  is: ";var_dump($result);print "<br/>";
    }
    return $result;
}





?>