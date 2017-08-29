<?php

class Tooljob {

    public $_id;
    public $title;
    public $project;           // User defined. Correspond to the execution folder name
    public $toolId;
    public $root_dir;          // User dataDir mounted to VMs. Path as seen by VRE 
    public $root_dir_virtual;  // User dataDir mounted to VMs. Path as seen by VMs
    public $pub_dir;           // Public dir mounted to VMs. Path as seen by VRE
    public $pub_dir_virtual;   // Public dir mounted to VMs. Path as seen by VMs  
    public $cloudName;         // Cloud name where tool should be executed. Available clouds set in GLOBALS['clouds']
    public $description;
    public $working_dir;
    public $launcher;

    // Paths to files genereted during ToolJob execution
    public $config_file;
    public $config_file_virtual;
    public $stageout_file;
    public $stageout_file_virtual;
    public $submission_file;
    public $metadata_file;
    public $metadata_file_virtual;
    public $log_file;

    public $stageout_data=Array();
    public $input_files = Array();
    public $arguments   = Array();
    public $metadata    = Array();
    public $pid          = 0;


    /**
     * Creates new toolExecutor instance
     * @param string $toolId Tool Id as appears in Mongo
    */
    public function __construct($tool,$project="0",$descrip="0"){
	
    	// Setting Tooljob
    	$this->toolId    = $tool['_id'];
    	$this->title     = $tool['name'] ." job";
    	$this->project   = $project;
        $this->root_dir  = $GLOBALS['dataDir']."/".$_SESSION['User']['id'];
    	$this->pub_dir   = $GLOBALS['pubDir'];

    	$this->set_cloudName($tool);

    	$this->root_dir_virtual = $GLOBALS['clouds'][$this->cloudName]['dataDir_virtual'];
    	$this->pub_dir_virtual  = $GLOBALS['clouds'][$this->cloudName]['pubDir_virtual'];
	    $this->launcher         = $tool['infrastructure']['clouds'][$this->cloudName]['launcher'];


    	// Creating project folder
    	if ($project != "0")
    		$this->__setWorking_dir($project,1);
    	else
    		$this->__setWorking_inTmp($tool['_id']);
    
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

    public function __setWorking_dir($project, $overwrite=0){

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


    	$this->working_dir    = $this->root_dir."/".$this->project;
	$this->config_file    = $this->working_dir."/".$GLOBALS['tool_config_file'];
	$this->stageout_file  = $this->working_dir."/".$GLOBALS['tool_stageout_file'];
        $this->submission_file= $this->working_dir."/".$GLOBALS['tool_submission_file'];
        $this->log_file       = $this->working_dir."/".$GLOBALS['tool_log_file'];
        $this->metadata_file  = $this->working_dir."/".$GLOBALS['tool_metadata_file'];

        $this->config_file_virtual    = $this->root_dir_virtual."/".$this->project."/".$GLOBALS['tool_config_file'];
        $this->stageout_file_virtual  = $this->root_dir_virtual."/".$this->project."/".$GLOBALS['tool_stageout_file'];
        $this->metadata_file_virtual  = $this->root_dir_virtual."/".$this->project."/".$GLOBALS['tool_metadata_file'];
    }


    public function __setWorking_inTmp($prefixDir=0){
	if ($prefixDir =="0")
		$prefixDir = "tool_";
	$project = $prefixDir."_".rand(10000, 99999);

    	$this->working_dir    = $this->root_dir."/".$this->project;
	$this->config_file    = $this->working_dir."/".$GLOBALS['tool_config_file'];
	$this->stageout_file  = $this->working_dir."/".$GLOBALS['tool_stageout_file'];
        $this->submission_file= $this->working_dir."/".$GLOBALS['tool_submission_file'];
        $this->log_file       = $this->working_dir."/".$GLOBALS['tool_log_file'];
        $this->metadata_file  = $this->working_dir."/".$GLOBALS['tool_metadata_file'];


        $this->config_file_virtual    = $this->root_dir_virtual."/".$this->project."/".$GLOBALS['tool_config_file'];
        $this->stageout_file_virtual  = $this->root_dir_virtual."/".$this->project."/".$GLOBALS['tool_stageout_file'];
        $this->metadata_file_virtual  = $this->root_dir_virtual."/".$this->project."/".$GLOBALS['tool_metadata_file'];
    }



    /**

    /**
     * Create working directory
    */
    public function createWorking_dir(){

	if (!$this->working_dir ){
		$_SESSION['errorData']['Internal Error'][]="Cannot create working_dir. Not set yet";
		return 0;
	}
	$dirfn = str_replace($GLOBALS['dataDir']."/","",$this->working_dir);

	// create working dir - disk and db
	if (!is_dir($this->working_dir)){
	
		if (preg_match('/\.tmp/',$this->working_dir)){
			$this->_id = 1;
		}else{
	        	$dirId = createGSDirBNS($dirfn);
	        	if ($dirId=="0"){
	                	$_SESSION['errorData']['Error'][]="Cannot create project folder: '$this->working_dir'";
				return 0;
        		}
			$this->_id = $dirId;
		}

        	mkdir($this->working_dir,0777);
        	chmod($this->working_dir, 0777);

	// if exists, recover working dir id
	}else{
		if (preg_match('/\.tmp/',$this->working_dir)){
			$this->_id = 1;
		}else{
			$dirId = getGSFileId_fromPath($dirfn);
			$_SESSION['errorData']['Error'][]=" already done dir from file_path is $dirfn . id is $dirId<br>";
			if ($dirId=="0")
				$_SESSION['errorData']['Error'][]="Cannot create project folder: alredy in disk but not in mongo. Try using a new project name other than ".basename($this->working_dir);
	
			$this->_id = $dirId;
		}
		
	}

	// set dir metadata
	if ($this->_id != 1){
		if (!is_dir($this->working_dir)){
	        	$_SESSION['errorData']['Error'][]="Cannot write and set new project directory: '$this->working_dir' with id '$this->_id'";
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
			'shPath'      => $this->submission_file,
			'logPath'     => $this->log_file,
        	'arguments'   => $this->arguments
		);
		$r = addMetadataBNS($this->_id, $projDirMeta);
		if ($r == "0"){
		        $_SESSION['errorData']['Error'][]="Project folder created. But cannot set metada for '$this->working_dir' with id '$this->_id'";
			return 0;
		}
	}
	return $this->_id;
    }



    /**
     * Creates tool configuration JSON
     * @param array $tool Fill in config file: input_files, arguments and output_files
    */
    public function setConfiguration_file($tool){
	
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
		),
		'output_files'=>Array()
	);
	// append input_files
	//foreach ($this->input_files as $input_file){
	//	array_push($data['input_files'], Array("name"=>$input_file->input_name, "value"=> $this->getPathRelativeToRoot($input_file->path)));
	foreach ($this->input_files as $k=>$vs){
	    foreach ($vs as $v){
            array_push($data['input_files'], Array(
                                                "name"          => $k,
                                                "value"         => $v,
                                                "required"      => $tool['input_files'][$k]['required'],
                                                "allow_multiple"=> $tool['input_files'][$k]['allow_multiple']
                                            )
                       );
	   }
	}
	// append arguments
	foreach ($this->arguments as $k=>$v){
		array_push($data['arguments'], Array("name"=>$k, "value"=> $v));
    }

    // append output_files from tool json
    if ($tool['output_files']){
        var_dump($tool['output_files']);
        foreach ($tool['output_files'] as $k => $v){
            $data['output_files'][] = $v;
        }
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
    public function setArguments($arguments,$tool=Array()){
	foreach ($arguments as $arg_name => $arg_value){
	    //checking  requirements
	    if (count($tool)){
		// checking coherence between JSON and REQUEST
		if (!isset($tool['arguments'][$arg_name])){
			$_SESSION['errorData']['Internal'][]="Argument '$arg_name' not found in tool definition. '$this->toolId' is not properly registered";
			return 0;
		}
		// checking arguments requirements (TODO create 'validateArguments')
		if ($arg_value==""){
		    if ($tool['arguments'][$arg_name]['required']){
			$_SESSION['errorData']['Error'][]="No value given for argument '$arg_name'";
		    	return 0;
		    }else{
			continue;
		    }
		}
		switch ($tool['arguments'][$arg_name]['type']){
		    case "enum":
			if (!isset($tool['arguments'][$arg_name]['enum_items']) || (!isset($tool['arguments'][$arg_name]['enum_items']['name']))){
			    $_SESSION['errorData']['Internal'][]="Invalid argument enum in tool definition. '$arg_name' has no 'enum_items' or 'enum_items['name].";
		 	    return 0;
			}
			if (!in_array($arg_value,$tool['arguments'][$arg_name]['enum_items']['name']) ){
	    		    $_SESSION['errorData']['Error'][]="Invalid argument. In '$arg_name' these values are accepted [".implode(", ",$tool['arguments'][$arg_name]['enum_items']['name'])."], but found $arg_value";
			    return 0;
			}
			break;
		    case "enum_multiple":
			if (!isset($tool['arguments'][$arg_name]['enum_items']) || (!isset($tool['arguments'][$arg_name]['enum_items']['name']))){
			    $_SESSION['errorData']['Internal'][]="Invalid argument enum in tool definition. '$arg_name' has no 'enum_items' or 'enum_items['name].";
		 	    return 0;
			}
			if (!is_array($arg_value))
				$arg_value=array($arg_value);
			foreach ($arg_value as $v){
				if (!in_array($v,$tool['arguments'][$arg_name]['enum_items']['name']) ){
			    		$_SESSION['errorData']['Error'][]="Invalid argument. In '$arg_name' these values are accepted [".implode(", ",$tool['arguments'][$arg_name]['enum_items']['name'])."], but found ".implode(", ",$arg_value);
					return 0;
				}
			}
			break;
			    if ($tool['arguments'][$arg_name]['type'] == "enum"){
			    	$_SESSION['errorData']['Internal'][]="Invalid argument enum in tool definition. '$arg_name' has no 'enum_items' or 'enum_items['name].";
				return 0;
			    }
			break;
		    case "boolean":
			if ($arg_value===true || $arg_value="on")
				$arg_value=true;	
			elseif ($arg_value===false || $arg_value="off")
				$arg_value=false;	
			else{
			    $_SESSION['errorData']['Error'][]="Invalid argument. In '$arg_name' a boolean was expected, but found: $arg_value";
		 	    return 0;
			}
			break;
		    case "integer":
			if (!is_numeric($arg_value)){
			    $_SESSION['errorData']['Error'][]="Invalid argument. In '$arg_name' an integer was expected, but found: $arg_value";
		 	    return 0;
			}
			$arg_value = intval($arg_value);
			break;
		    case "number":
			if (!is_numeric($arg_value)){
			    $_SESSION['errorData']['Error'][]="Invalid argument. In '$arg_name' a number was expected, but found: $arg_value";
		 	    return 0;
			}
			break;
		    case "string":
			if (is_array($arg_value)){
			    $_SESSION['errorData']['Error'][]="Invalid argument. In '$arg_name' a string was expected, but found an array: ".implode(",",$arg_value);
		 	    return 0;
			}
			$arg_value = strval($arg_value);
			break;
		    case "enum":
		    default:
			$_SESSION['errorData']['Internal'][]="Invalid argument type in tool definition. '$arg_name' is of type ".$tool['arguments'][$arg_name]['type'];
		 	return 0;
		}
	    }
	    // setting arguments 
	    $this->arguments[$arg_name]=$arg_value;
		
	}
        return 1;
    }



    /**
     * Set inputFiles
     * @param array $arguments Arguments as received from inputs.php
     * @param array $tool Tool array containing input_files type and requirements
     * @param array $metadata Files metadata extracted from DB
    */
    public function setInput_files($input_files,$tool=array(),$metadata=array()){

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
     * Store its metadata in Tooljob for recovering it latter, while stageout register
     * Needed when tool has not APP (internal), and no out_metadata is generated. 
     * @param array $outs Array of outputfiles
     * @param array $tool Tool array containing input_files type and requirements TODO
     * @param array $metadata Files metadata extracted from DB TODO
    */
    public function setStageout_data($out_files,$tool=array(),$metadata=array()){
	if (!isset ($out_files['output_files'])){
		$_SESSION['errorData']['Error'][]="Internal tool may have problems registering outfiles: Stageout_data mal formatted";
		return 0;
	}

	foreach ($out_files['output_files'] as $out_name => $info){
		//Validate out_files against tool document
		//TODO
		
		//Add output file metadata
		$this->stageout_data['output_files'][$out_name]=$info;
	}
	$this->stageout_file="";
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
		$fileMuG = $this->fromVREfile_toMUGfile($file);
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

	$launcher = $tool['infrastructure']['clouds'][$this->cloudName]['launcher'];

    //external tool
    if ($tool['external'] !== false){

		$r = $this->setConfiguration_file($tool);
		if ($r=="0")
		    return 0;
	
		$this->setMetadata_file($metadata);
		if ($r=="0")
		    return 0;
	
		if (!is_file($this->config_file) && !is_file($this->metadata_file) ){
			$_SESSION['errorData']['Internal Error'][]="Cannot set tool command line. It required configuration file ($this->config_file) and metadata file ($this->metadata_file)";
	            	return 0;
		}

		switch ($launcher){
		    case "SGE":
			$cmd  = $this->setBashCmd_SGE($tool);
			//print "<br><br>CMD = $cmd<br>";
			if (!$cmd)
				return 0;
	
			$submission_rfn = $this->createSubmitFile_SGE($cmd); 
			if (!is_file($submission_rfn))
				return 0;
			break;
	
		    case "PMES":
			$json_data = $this->setPMESrequest($tool);
			if (!$json_data)
				return 0;

			$submission_rfn = $this->createSubmitFile_PMES($json_data);
			print "<br>PMES JSON FILE IS<br> $submission_rfn";
			if (!is_file($submission_rfn))
				return 0;
			break;
	
		    default:
			$_SESSION['errorData']['Error'][]="Tool '$this->toolId' not properly registered. Launcher for '$this->toolId' is set to \"$launcher\". Case not implemented.";
			return 0;
		}

		return 1;	

	//internal tool
	}elseif ($tool['external'] === false){

		switch ($launcher){
	
		    case "SGE":
			$cmd = $this->setBashCmd_withoutApp($tool,$metadata);
			//print "<br><br>CMD WITHOUT APP= $cmd<br>";
			if (!$cmd)
				return 0;

			$submission_rfn = $this->createSubmitFile_SGE($cmd); 
			if (!is_file($submission_rfn))
				return 0;
			break;
		
		    case "PMES":
			//TODO
	
		    default:
			$_SESSION['errorData']['Error'][]="Internal Tool '$this->toolId' not properly registered. Launcher for '$this->toolId' is set to \"$launcher\". Case not implemented.";
			return 0;
		}
		return 1;
	}
    }

    protected function setBashCmd_SGE($tool){
	if (!isset($tool['infrastructure']['executable'])){
            $_SESSION['errorData']['Internal Error'][]="Tool '$this->toolId' not properly registered. Missing 'executable' property";
            return 0;
	}
	$cmd = $tool['infrastructure']['executable'] .
					" --config "         .$this->config_file_virtual .
					" --root_dir "       .$this->root_dir_virtual .
					" --public_dir "     .$this->pub_dir_virtual .
					" --in_metadata " 	 .$this->metadata_file_virtual .
					" --out_metadata "   .$this->stageout_file_virtual ;
				    //  " --log "            .$this->log_file
	return $cmd;
    }

    protected function setPMESrequest($tool){
	if (!isset($tool['infrastructure']['executable'])){
            $_SESSION['errorData']['Internal Error'][]="Tool '$this->toolId' not properly registered. Missing 'executable' property";
            return 0;
	}

	//Setting defaults from tool definition 
	if (!isset($tool['infrastructure']['wallTime']) )
		   $tool['infrastructure']['wallTime'] = "1440";// 24h
	if (!isset($tool['infrastructure']['memory']) )
		   $tool['infrastructure']['memory']   = "1.0"; // 1Gb per VM
	if (!isset($tool['infrastructure']['cpus']) )
		   $tool['infrastructure']['cpus']     = "1";   //1 core per VM

	$cloud   = $tool['infrastructure']['clouds'][$this->cloudName];
	if (!isset($cloud['minimumVMs']) )
		   $cloud['minimumVMs'] = "1"; // if workflow_type = "Single" -> 1
	if (!isset($cloud['maximumVMs']) )
		   $cloud['maximumVMs'] = "1"; // if workflow_type = "Single" -> 1
	if (!isset($cloud['limitVMs']) )
		   $cloud['limitVMs']   = "1"; // TODO OBSOLETE (=== maximumVMs)?
	if (!isset($cloud['initialVMs']) )
		   $cloud['initialVMs'] = "1"; // if workflow_type = "Single" -> 1
	if (!isset($cloud['disk']) )
		   $cloud['disk'] = "1.0";     // TODO OBSOLETE?

	//Setting PMES execution user (name,uid,gid)
	exec("stat  -c '%u:%g' ".$this->working_dir,$stat_out);
	list($user_uid,$user_gid) = split(":",$stat_out[0]);
	$user_name = "vre";

	//Setting executable as PMES requires
	$app_target =  dirname($tool['infrastructure']['executable']);
	$app_source = basename($tool['infrastructure']['executable']);

	//Building PMES json data
	$data = array(
		   array(
		"jobName"    => $this->project, 
		"wallTime"   => $tool['infrastructure']['wallTime'], 
		"memory"     => $tool['infrastructure']['memory'],
		"cores"      => $tool['infrastructure']['cpus'],
		"minimumVMs" => $cloud['minimumVMs'], 
		"maximumVMs" => $cloud['maximumVMs'],
		"limitVMs"   => $cloud['limitVMs'],
		"initialVMs" => $cloud['initialVMs'],
		"disk"       => $cloud['disk'],
		"inputPaths" => array(),
		"outputPaths"=> array(),
		"infrastructure" =>  $this->cloudName,
		"mountPoints"=> array(
				    array( "target"     => $this->root_dir_virtual,
					   "device"     => $GLOBALS['clouds'][$this->cloudName]['dataDir_fs']."/".$_SESSION['User']['id'],
					   "permissions"=> "rw"
				    ), 
				    array( "target"     => $this->pub_dir_virtual,
					   "device"     => $GLOBALS['clouds'][$this->cloudName]['pubDir_fs'],
					   "permissions"=> "r"
				    )
				), 
		"numNodes"   => "1",                                           //TODO OBSOLETE?
		"user"       => array (
              			"username"   => $user_name,                     // PMES creates /home/username/
				"credentials"=> array(
					"pem"   => "/home/pmes/certs/pmes.pem", // in PMES server path
					"key"   => "/home/pmes/certs/pmes.key", // in PMES server path
					"uid"   => $user_uid,                   // PMES writes outputs using this uid
					"gid"   => $user_gid,                   // PMES writes outputs using this gid
					"token" => ""
					)
				),
		"img"        => array(
				"imageName" => $cloud['imageName'], 
				"imageType" => "small",				// Not used. Formally required for rOCCY petition.
				),
           	"app"        => array(
				"name"   => $tool['_id'],
				"target" => $app_target,
				"source" => $app_source,
				"args"  => array(
						"config"      => $this->config_file_virtual,
						"root_dir"    => $this->root_dir_virtual,
						"public_dir"  => $this->pub_dir_virtual,
						"in_metadata" => $this->metadata_file_virtual,
						"out_metadata"=> $this->stageout_file_virtual
						),
        			"type" => $cloud['workflowType']    // COMPSs || Single
				),				
		"compss_flags" =>array( "flag" => " --summary")
		)
	);
	return $data;
    }



    protected function setBashCmd_withoutApp($tool,$metadata){
	if (!isset($tool['infrastructure']['executable'])){
            $_SESSION['errorData']['Internal Error'][]="Tool '$this->toolId' not properly registered. Missing 'executable' property";
            return 0;
	}
	$cmd = $tool['infrastructure']['executable'];
	// Add to Cmd: --input_name fn_path
	foreach ($this->input_files as $input_name => $fnIds){
 	    foreach ($fnIds as $fnId){
		$fn  = $metadata[$fnId]['path'];
		$rfn = $GLOBALS['dataDir']."/$fn";
		$cmd .= " --$input_name $rfn";
	    }
	}
	// Add to Cmd: --argument_name value
	foreach ($this->arguments as $k=>$v){
		$cmd .= " --$k $v";
	}
	return $cmd;
    }


    protected function createSubmitFile_SGE($cmd){

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

    protected function createSubmitFile_PMES($data){

	$json_rfn   = $this->submission_file;


	try{
	    $fout = fopen($json_rfn,"w");
	    if (!$fout) {
		throw new Exception('Failed to create tool configuration file: '.$json_rfn);
	    }
    	}
	catch (Exception $e){
		$_SESSION['errorData']['Error'][]="Failed to create queue submission file. ".$e->getMessage();
		return 0;
	}
	fwrite($fout, json_encode($data,JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
	fclose($fout);
	
	return $json_rfn;
    }

    /**
     * Submits 
     * @param string $inputs_request _REQUEST data from inputs.php form
    */
    public function submit($tool){
	switch ($tool['infrastructure']['clouds'][$this->cloudName]['launcher']){
	    case "SGE":
		return $this->enqueue($tool['infrastructure']);
		break;
	    case "PMES":
		return $this->callPMES();
		break;
	    default:
		$_SESSION['errorData']['Error'][]="Tool '$this->toolId' not properly registered. Launcher for '$this->toolId' is set to: \"".$tool['infrastructure']['clouds'][$this->cloudName]['launcher']."\". Case not implemented.";
		return 0;
	}
	return 1;
    }	    

    protected function enqueue($tool_infrastructure){
	
	logger("");
	$memory = $tool['infrastructure']['memory'];
	$cpus   = $tool['infrastructure']['cpus'];
	$queue  = $tool['infrastructure']['clouds'][$this->cloudName]['queue'];

	$pid  = execJob($this->working_dir, $this->submission_file, $queue, $cpus, $memory);
	logger("USER:".$_SESSION['User']['_id'].", ID:".$_SESSION['User']['id'].", LAUNCHER:SGE, TOOL:".$this->toolId.", PID:$pid");
	
	if (!$pid){
                $_SESSION['errorData']['Error'][]="Cannot enqueue job. Submission file was: $this->submission_file ";
                return 0;
        }

	$this->pid = $pid;
        return $pid;

    }


    protected function callPMES(){

	$data_string = file_get_contents($this->submission_file);
	$data = json_decode($data_string, true);

	$pid  = execJobPMES($this->cloudName,$data);

	print "<br><br>============= <br>TOOLJOB SEND TO '$this->cloudName'  RESULTED IN PID=$pid<br>";

	logger("USER:".$_SESSION['User']['_id'].", ID:".$_SESSION['User']['id'].", LAUNCHER:PMES, TOOL:".$this->toolId.", PID:$pid");

	if (!$pid){
                $_SESSION['errorData']['Error'][]="Cannot enqueue job. Submission file was: $this->submission_file ";
                return 0;
        }
	$this->pid = $pid;
        return $pid;
    }


    protected function getPathRelativeToRoot($path){
        if (preg_match('/^\//',$path)){
            return preg_replace('/^\//',"",str_replace($GLOBALS['dataDir']."/".$_SESSION['User']['id']."/","",$path));
        }else{
            return preg_replace('/^\//',"",str_replace($_SESSION['User']['id']."/","",$path)); 
        }
    }   

    /**
     * Convert internal VRE file format into DM MuG file  
     * @file  VRE file object, resulting from merging MuGVRE Mongo collections Files + FilesMetadata
    */
    protected function fromVREfile_toMUGfile($file) {

                $mugfile        = array();
		$compressions   = $GLOBALS['compressions'];
                $mugfile['_id'] = $file['_id'];

		//path -> file_path (relative to user_data_directory)
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

		// format -> file_type
                if (isset($file['format']))
                        $mugfile['file_type'] = $file['format'];
                else
                        $mugfile['file_type'] = "UNK";

		// data_type -> data_type
                if (isset($file['data_type']))
                        $mugfile['data_type'] = $file['data_type'];
                else
                        $mugfile['data_type'] = NULL;

		// compressed -> compressed
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

		// inPaths -> source_id (file_ids)
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
                        $mugfile['source_id'] = [];
		}

		// mtime -> creation_time
                if (isset($file['mtime']))
                        $mugfile['creation_time'] = $file['mtime'];
                else
                        $mugfile['creation_time'] = new \MongoDate();

		// taxon_id -> taxon_id
                if (isset($file['taxon_id']))
			$mugfile['taxon_id'] = $file['taxon_id'];
		else{
		 	if(!isset($file['refGenome']))
				$mugfile['taxon_id'] = 0;
			else{
				$refGenome_to_taxon = Array( "hg19"=>"9606", "R64-1-1"=>"4932", "r5.01"=>"7227");
				$mugfile['taxon_id'] = $refGenome_to_taxon[$file['refGenome']];
			}
		}

                unset($file['_id']);
                unset($file['path']);
                unset($file['mtime']);
                unset($file['format']);
                unset($file['trackType']); 
                unset($file['tracktype']); 
                unset($file['shPath']); 
                unset($file['logPath']); 
                unset($file['inPaths']);

		// other -> meta_data
                $mugfile['meta_data']  = $file;

		// refGenome -> assembly	
                if (isset($mugfile['meta_data']['refGenome']) ){
                        $mugfile['meta_data']['assembly'] = $mugfile['meta_data']['refGenome'];
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
    *  Set Cloudname to the default value, as specified in the tool definition
    *  TODO Choose cloud according where the data is.
    */
    protected function set_cloudName($tool=array()){
	$available_clouds = array_keys($GLOBALS['clouds']);
	if (!count($available_clouds)){
		$_SESSION['errorData']['Error'][] = "Internal Error: No cloud infrastructure available in the current VRE installation.";
		return 0;
	}
	
	if (isset($tool['infrastructure']['clouds'])){
		// 1, set cloudName from default cloud, as tool specifies
		foreach ($tool['infrastructure']['clouds'] as $name=>$toolInfo){
			if ($toolInfo['default_cloud'] === true){
				if (in_array($name,$available_clouds)){
					$this->cloudName = $name;
					break;
				}
			}
		}
		// 2, set cloudName from clouds list in tool specification, the first found available
		if (! $this->cloudName){
			foreach ($tool['infrastructure']['clouds'] as $name=>$cloudInfo){
				if (in_array($name,$available_clouds)){
					$this->cloudName = $name;
					$_SESSION['errorData']['Warning'][] = "Tool has no the default cloud infrastructure set or available. Taking instead '$this->cloudName', but the tool execution may fail.";
					break;
				}
			}
		}
	}
	if (! $this->cloudName){
		// 3, set cloudName from the server available_clouds, the first
		$this->cloudName = $available_clouds[0];
		$_SESSION['errorData']['Warning'][] = "Tool has no the cloud infrastructure set. Taking '$this->cloudName', but the tool execution may fail.";
	}
	return 1;
    }



    /**
     * Parse submission File
    */
    public function parseSubmissionFile(){
	return 1;
	
    }
}
?>
