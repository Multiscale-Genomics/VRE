<?php

class Tooljob {

    public $_id;
    public $toolId;
    public $input_files = Array();
    public $arguments   = Array();
    public $metadata    = Array();
    public $description;
    public $title;
    public $project;
    public $pid;

    public $root_dir;
    public $working_dir;
    public $config_file;
    public $stageout_file;
    public $submission_file;
    public $metadata_file;
    public $log_file;
    /**
     * Creates new toolExecutor instance
     * @param string $toolId Tool Id as appears in Mongo
    */
    public function __construct($tool,$project="0",$descrip="0"){
	
	$this->toolId   = $tool['_id'];
    	$this->root_dir = $GLOBALS['dataDir']."/".$_SESSION['User']['id'];
	$this->title    = $tool['name'] ." job";

	if ($project != "0")
		$this->setWorking_dir($project,1);
	if ($descrip != "0")
		$this->setDescription($descrip,$tool['name']);

	return $this;
    }


    /**
     * Fetch tool entry in Mongo 
     * @param string $toolId Tool Id as appears in Mongo
    */
    protected function getTool($toolId){
	$tool   = $GLOBALS['toolsCol']->findOne(array('_id' => $toolId));
	if (empty($tool)){
                $_SESSION['errorData']['Tooljob'][]="Tool '$toolId' is not registered. Cannot submit execution. Please, contact <a href=\"mailto:".$GLOBALS['helpdeskMail']."\">us</a>";
                return 0;
	}
	//$this->tool= (object) $tool;
	$this->tool= $this->array_to_object($tool);
    }


    /**
     * Set description
     * @param string $descrip Short project description to annotate execution directory
    */
    public function setDescription($descrip,$toolName=0){
        if (strlen($descrip))
                $this->description=$descrip;
	elseif($toolName)
                $this->description="Execution directory for tool ".$toolName;
	else
                $this->description="Execution directory";
    }


    /**
     * Set working directory where log_file, submission_file and control_file will be located
     * @param string $project Project name used to set the working directory name
     * @param boolean $overwrite If false, an alternative name $project[_NN] for the working directory is set
    */

    public function setWorking_dir($project, $overwrite=0){

	$wd   = $GLOBALS['dataDir']."/".$_SESSION['User']['id']."/$project";
	$wdFN = $_SESSION['User']['id']."/$project";
	
	if (!$overwrite){
		$prevs = $GLOBALS['filesCol']->find(array('path' => $wdFN, 'owner' => $_SESSION['User']['id']) );
		if ($prevs->count() > 0){
		    for ($n=1;$n<99;$n++){
	        	$projectN= $project. "_$n";
			$wdFN    = $_SESSION['User']['id']."/$projectN";
			$prevs   = $GLOBALS['filesCol']->find(array('path' => $wdFN, 'owner' => $_SESSION['User']['id']));
			if ($prevs->count() == 0){
			    $project= $projectN;
	        	    $wd     = $GLOBALS['dataDir']."/$wdFN";
			    break;
        		}
	    	    }
		}
	}
	$this->project        = $project;
	$this->working_dir    = $this->root_dir."/".$this->project;
	$this->config_file    = $this->working_dir."/".$GLOBALS['tool_config_file'];
	$this->stageout_file  = $this->working_dir."/".$GLOBALS['tool_stageout_file'];
        $this->submission_file= $this->working_dir."/".$GLOBALS['tool_submission_file'];
        $this->log_file       = $this->working_dir."/".$GLOBALS['tool_log_file'];
        $this->metadata_file  = $this->working_dir."/".$GLOBALS['tool_metadata_file'];

    }



    /**
     * Create working directory
    */
    public function createWorking_dir(){

	if (!$this->working_dir ){
		$_SESSION['errorData']['Internal Error'][]="Cannot create working_dir. Not set yet";
		return 0;
	}
	$dirfn = str_replace($GLOBALS['dataDir']."/","",$this->working_dir);

	if (!is_dir($this->working_dir)){
        	$dirId = createGSDirBNS($dirfn);
        	if ($dirId=="0"){
                	$_SESSION['errorData']['Error'][]="Cannot create project folder: '$this->working_dir'";
			return 0;
        	}

        	mkdir($this->working_dir,0777);
        	chmod($this->working_dir, 0777);
		$this->_id = $dirId;
	}else{
		$dirId = getGSFileId_fromPath($dirfn);
		if ($dirId)
			$this->_id = $dirId;
		
	}
	if (!is_dir($this->working_dir)){
        	$_SESSION['errorData']['Error'][]="Cannot write and set new project directory: '$this->working_dir'";
		return 0;
	}

        $input_ids = array();
        array_walk_recursive($this->input_files, function($v, $k) use (&$input_ids){ $input_ids[] = $v; });
        $input_ids = array_unique($input_ids);

	$projDirMeta=array(
		'description' => $this->description,
	        //'inPaths'     => array_map(create_function('$o', 'return $o->path;'), $this->input_files),
	        'input_files' => $input_ids,
	        'tool'        => $this->toolId,
        	'arguments'   => $this->arguments
	);
	$r = addMetadataBNS($this->_id, $projDirMeta);
	if ($r == "0"){
	        $_SESSION['errorData']['Error'][]="Project folder created. But cannot set metada for '$this->working_dir'";
		return 0;
	}
	return $this->_id;
    }



    /**
     * Creates tool configuration JSON
     * @param array $input_files  Input files as received from inputs.php
     * @param array $arguments Arguments as received from inputs.php
    */
    public function setConfiguration_file(){
	
	$config_rfn = $this->config_file;

	if (!$this->working_dir){
		$_SESSION['errorData']['Internal Error'][]="Cannot create tool configuration file. No 'working_directory' set";
		return 0;
	}

	// Set json base
	$data = Array(
		'input_files'=>Array(),
		'arguments'=>Array(
			Array("name"=>"project",     "value"=> $this->project),
			Array("name"=>"description", "value"=> $this->description),
		)
	);
	// append input_files
	//foreach ($this->input_files as $input_file){
	//	array_push($data['input_files'], Array("name"=>$input_file->input_name, "value"=> $this->getPathRelativeToRoot($input_file->path)));
	foreach ($this->input_files as $k=>$vs){
	    foreach ($vs as $v){
		array_push($data['input_files'], Array("name"=>$k, "value"=> $v));
	   }
	}
	// append arguments
	foreach ($this->arguments as $k=>$v){
		array_push($data['arguments'], Array("name"=>$k, "value"=> $v));
	}

	// write JSON
	try{
	    $F = fopen($config_rfn,"w");
	    if (!$F) {
		throw new Exception('Failed to create tool configuration file'.$config_rfn);
	    }
    	}
	catch (Exception $e){
		$_SESSION['errorData']['Internal Error'][]= $e->getMessage();
		return 0;
	}

	fwrite($F, json_encode($data,JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
	fclose($F);

	return $config_rfn;
    }


    /**
     * Set Arguments
     * @param array $arguments Arguments as received from inputs.php
    */
    public function setArguments($arguments){
        $this->arguments = $arguments;
        return 1;
    }



    /**
     * Set inputFiles
     * @param array $arguments Arguments as received from inputs.php
     * @param array $tool Tool array containing input_files type and requirements
     * @param array $metadta Files metadata extracted from DB
    */
    public function setInput_files($input_files,$tool,$metadata){

	foreach ($input_files as $input_name => $fns){

	    //checking  requirements
	    if (count($tool) && count($metadata)){
		    if (!is_array($fns))
			$fns=array($fns);
		    foreach ($fns as $fn){
			if (!$fn){
				$_SESSION['errorData']['Error'][]="No file given for '$input_name'";
				return 0;
			}
			// checking coherence between JSON and REQUEST
			if (!isset($tool['input_files'][$input_name])){
				$_SESSION['errorData']['Internal'][]="Input file '$input_name' not found in tool definition. '$this->toolId' is not properly registered";
				return 0;
			}
			// checking input_file has metadata
			if (!isset($metadata[$fn])){
				$_SESSION['errorData']['Error'][]="Given file in '$input_name' has no metadata";
				return 0;
			}
			// checking input_files requirements
			$ok = $this->validateInput_file($tool['input_files'][$input_name], $metadata[$fn]);
			if (! $ok){
				$_SESSION['errorData']['Error'][]="Input file '$input_name' not valid. Stopping '$this->toolId' execution";
				return 0;
			}
		    }   
	    }
	    // setting input_files 
	    $this->input_files[$input_name]=$fns;
	}
        return 1;
    }


    /**
     * Check input files requirements based on format and datatype
     * @param array $inputReq  Input_file as defined in tool collection (derived from tool JSON definition)
     * @param array $inputMetadata File metadata
    */
    protected function validateInput_file($inputReq, $inputMetadata){
	if (!isset($inputReq['file_type']) && !isset($inputReq['data_type']) ){
		$_SESSION['errorData']['Warning'][]="Ommitting format and type control for input file '".$inputReq['name'].". Tool has no 'file_type' nor 'data_type' set.";
		return 1;
	}
	if (!isset($inputMetadata['format']) && !isset($inputReq['data_type']) ){
		$_SESSION['errorData']['Warning'][]="Ommitting format and type control for input file '".$inputReq['name'].". Given file has no 'file_type' nor 'data_type' set.";
		return 1;
	}
	// checking format
	if (isset($inputReq['file_type']) &&  isset($inputMetadata['format'])){
		//if ($inputReq['file_type'] != $inputMetadata['format']){
		if (!in_array($inputMetadata['format'],$inputReq['file_type'])){
			$_SESSION['errorData']['Error'][]="Input file '".basename($inputMetadata['path'])."' in '".$inputReq['name']." has format '".$inputMetadata['format']."  and '".implode(", ",$inputReq['file_type'])."' was excepted.";
			return 0;

		}
	}
	// checking datatype
	if (isset($inputReq['data_type']) &&  isset($inputMetadata['data_type'])){
		//if ($inputReq['data_type'] != $inputMetadata['data_type']){
		if (!in_array($inputMetadata['data_type'],$inputReq['data_type'])){
			$_SESSION['errorData']['Error'][]="Input file '".basename($inputMetadata['path'])."' in '".$inputReq['name']." is a '".$inputMetadata['data_type']."  and '".implode(", ",$inputReq['data_type'])."' was excepted.";
			return 0;

		}
	}
	return 1;
    }


    /**
     * Creates metadata JSON
    */
    public function setMetadata_file($metadata){
	if (!$this->working_dir){
		$_SESSION['errorData']['Internal Error'][]="Cannot create metadata file. No 'working_dir' set";
		return 0;
	}

        $fileMuGs=Array();
	foreach ($metadata as $fnId => $file){
		$fileMuG = $this->getFile_from_VREfile($file);
		array_push($fileMuGs,$fileMuG);
	}
	$metadata_rfn = $this->metadata_file;

	// write JSON
	try{
	    $F = fopen($metadata_rfn,"w");
	    if (!$F) {
		throw new Exception('Failed to create metadata file for tool execution'.$metadata_rfn);
	    }
    	}
	catch (Exception $e){
		$_SESSION['errorData']['Internal Error'][]= $e->getMessage();
		return 0;
	}

	fwrite($F, json_encode($fileMuGs,JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
	fclose($F);

	return $metadata_rfn;
    }



    /**
     * Creates execution Command Line and Submission File
    */
    public function prepareExecution($tool,$metadata){

	$r = $this->setConfiguration_file();
	if ($r=="0")
	    return 0;

	$this->setMetadata_file($metadata);
	if ($r=="0")
	    return 0;


	if (!is_file($this->config_file) && !is_file($this->metadata_file) ){
		$_SESSION['errorData']['Internal Error'][]="Cannot set tool command line. It required configuration file ($this->config_file) and metadata file ($this->metadata_file)";
            	return 0;
	}
	switch ($tool['launcher']['type']){

	    case "SGE":
		$cmd  = $this->setCmd($tool);
		print "<br><br>CMD = $cmd<br>";
		if (!$cmd)
		    return 0;

		$submission_rfn = $this->createSubmitFile($cmd); 
		if (!is_file($submission_rfn))
		    return 0;
		break;

	    case "PMES":
		// TODO

	    default:
                $_SESSION['errorData']['Error'][]="Tool '$this->toolId' not properly registered. Launcher for '$this->toolId' is set to: \"".$tool['launcher']['type']."\". Case not implemented.";
                return 0;
	}
	return 1;	
    }

    protected function setCmd($tool){
	if (!isset($tool['launcher']['executable'])){
            $_SESSION['errorData']['Internal Error'][]="Tool '$this->toolId' not properly registered. Missing 'executable' property";
            return 0;
	}
	$cmd = $tool['launcher']['executable'] .
					" --config "         .$this->config_file .
					" --root_dir "       .$this->root_dir .
					" --metadata " 	     .$this->metadata_file .
					" --out_metadata "   .$this->stageout_file ;
				    //  " --log "            .$this->log_file
	return $cmd;
    }


    protected function createSubmitFile($cmd){

	$working_dir= $this->working_dir;
	$bash_rfn   = $this->submission_file;
	$log_rfn    = $this->log_file;


	try{
	    $fout = fopen($bash_rfn,"w");
	    if (!$fout) {
		throw new Exception('Failed to create tool configuration file: '.$bash_rfn);
	    }
    	}
	catch (Exception $e){
		$_SESSION['errorData']['Error'][]="Failed to create queue submission file. ".$e->getMessage();
		return 0;
	}
	fwrite($fout, "#!/bin/bash\n");
	fwrite($fout, "# Generated by MuG VRE\n");
	fwrite($fout, "cd $working_dir\n");
	
	fwrite($fout, "\n# Running $this->toolId tool ...\n");
	fwrite($fout, "\necho '# Start time:' \$(date) > $log_rfn\n");

	
	fwrite($fout, "\n$cmd >> $log_rfn 2>&1\n");
	fwrite($fout, "\necho '# End time:' \$(date) >> $log_rfn\n");
	fclose($fout);

	return $bash_rfn;
    }


    /**
     * Submits 
     * @param string $inputs_request _REQUEST data from inputs.php form
    */
    public function submit($tool){
	return $this->enqueue($tool['launcher']);
	
    }	    

    protected function enqueue($tool){
	
	//$pid  = execJob($GLOBALS['dataDir']."/".$jobMeta['outDir'],$GLOBALS['dataDir']."/".$jobMeta['shPath'],$tool);

	$pid  = execJob($this->working_dir,$this->submission_file,$tool);
	
	if (!$pid){
                $_SESSION['errorData']['Error'][]="Cannot enqueue job. Submission file was: $this->submission_file ";
                return 0;
        }

	$this->pid = $pid;
        return $pid;

	//$SGE_updated[$pid]= Array('_id' => $pid,
        //                          'out' => $files[$i]['nucleRFN'],
        //                          'log' => str_replace(".sh",".log","$wdFN/$cmdNucleR"),
        //                          'sh' => "$wdFN/$cmdNucleR",
        //                          'in'  => $files[$i]['fn']
        //                      );
    }

    protected function getPathRelativeToRoot($path){
        if (preg_match('/^\//',$path)){
            return preg_replace('/^\//',"",str_replace($this->root_dir,"",$path));
        }else{
            return preg_replace('/^\//',"",str_replace($_SESSION['User']['id'],"",$path)); 
        }
    }   

    protected function getFile_from_VREfile($file) {

                $mugfile=array();
		$compressions = Array("zip"=>"ZIP","bz2"=>"BZIP2","gz"=>"GZIP","tgz"=>"TAR,GZIP","tbz2"=>"TAR,BZIP2");

                $mugfile['_id'] = $file['_id'];

                if (isset($file['path'])){
			if (preg_match('/^\//', $file['path']) || preg_match('/^'.$_SESSION['User']['id'].'/', $file['path']) ){
                        	$path = explode("/",$file['path']);
                        	$mugfile['file_path'] = implode("/",array_slice($path,-2,2));
			}else{
                        	$mugfile['file_path'] = $file['path'];
			}
                }else{
                        $mugfile['file_path'] = NULL;
		}
                if (isset($file['format']))
                        $mugfile['file_type'] = $file['format'];
                else
                        $mugfile['file_type'] = "UNK";

                if (isset($file['data_type']))
                        $mugfile['data_type'] = $file['data_type'];
                else
                        $mugfile['data_type'] = NULL;

                if (isset($file['path'])){
			$ext = pathinfo($file['path'], PATHINFO_EXTENSION);
			$ext = preg_replace('/_\d+$/',"",$ext);
			$ext = strtolower($ext);
                        if (in_array($ext,array_keys($compressions)) ){
                                $mugfile['compressed'] = $compressions[$ext];
                        }else{
                                $mugfile['compressed'] = 0;
                        }
                }

                if (isset($file['inPaths'])){
			if (!is_array($file['inPaths'])){
				$file['inPaths']=array($file['inPaths']);
			}
			foreach ($file['inPaths'] as $inPath){
				if (preg_match('/\//', $inPath)){
					$mugfile['source_id'][] = getGSFileId_fromPath($inPath);
				}else{
					$mugfile['source_id'][] = $inPath;
				}
			}
                }else{
                        $mugfile['source_id'] = NULL;
		}

                if (isset($file['mtime']))
                        $mugfile['creation_time'] = $file['mtime'];
                else
                        $mugfile['creation_time'] = new \MongoDate();


                unset($file['_id']);
                unset($file['path']);
                unset($file['mtime']);
                unset($file['format']);
                unset($file['tracktype']); 
                unset($file['shPath']); 
                unset($file['logPath']); 
                unset($file['inPaths']);
                $mugfile['meta_data']  = $file;

                if (isset($mugfile['meta_data']['refGenome']) ){
                        $mugfile['meta_data']['taxon_id'] = $mugfile['meta_data']['refGenome'];
			unset($mugfile['meta_data']['refGenome']);
		}

                return $mugfile;
    }

    /**
    *
    */
    protected function array_to_object($array) {
	$obj = new stdClass;
	foreach($array as $k => $v) {
	    if(strlen($k)) {
        	if(is_array($v)) {
          	    $obj->{$k} = $this->array_to_object($v); //RECURSION
		} else {
		    $obj->{$k} = $v;
        	}
     	    }
	}
	return $obj;
    } 

    /**
     * Parse submission File
    */
    public function parseSubmissionFile(){
	return 1;
	
    }
}
?>

