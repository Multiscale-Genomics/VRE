<?php

require "Tooljob.php";

function getTools_List() {
	
	$tools = $GLOBALS['toolsCol']->find(array('external' => true), array('name' => 1, 'title' => 1, 'short_description' => 1, 'keywords' => 1))->sort(array('title' => 1));

	return iterator_to_array($tools);	

}


function getTool_fromId($toolId,$indexByName=0) {
        $filterfields=array();
        if ($short){
                $filterfields = array(  'arguments'   => false,
                                        'input_files' => false,
                                        'output_files'=> false
                                     ); 
        }
        $tool = $GLOBALS['toolsCol']->findOne(array('_id' => $toolId), $filterfields);
        
        if (empty($tool))
                return 0;

        if ($indexByName){
		$toolIndexed=Array();
                foreach ($tool as $attribute => $value){
                        if (is_array($value)){
			    $t=0;
                            foreach ($value as $v){
                                if (isset($v['name'])){
					$t=1;
                                        $toolIndexed[$attribute][$v['name']]=$v;
				}
                            }
			    if (!$t){
				$toolIndexed[$attribute]=$value;
			    }   
                        }else{
			    $toolIndexed[$attribute]=$value;
			}
                }
		$tool = $toolIndexed;
        }      
	return $tool;


}

function hasTool_custom_visualizer($toolId){
        $has_custom_visualizer = $GLOBALS['toolsCol']->findOne(array('_id' => $toolId,
					            'output_files' =>array('$elemMatch' => array("custom_visualizer"=>true) )),
					       array('_id'=>1)
					     );
	return $has_custom_visualizer;
}

function launchToolInternal($toolId,$inputs=array(),$args=array(),$outs=array()){

	// Get tool.
        $tool = getTool_fromId($toolId,1);
        if (empty($tool)){
                $_SESSION['errorData']['Error'][]="Tool not specified or not registered. Please, register '$toolId'";
                return 0;
        }

	// Set Tool job - tmp working dir
	$jobMeta  = new Tooljob($tool);


	// Stage in (fake)  TODO

	// Checking files locally
	$files   = Array(); // distinct file Objs to stage in 
	foreach($inputs as $inName=>$inIds){
	    foreach($inIds as $inId){
		$file = getGSFile_fromId($inId);
		if (!$file){
		        $_SESSION['errorData']['Error'][]="Input file $inId does not belong to current user or has been not properly registered. Stopping internal tool execution";
			return 0;
		}
		$files[$file['_id']]=$file;
	    }
	}
	// Set input files
	$jobMeta->setInput_files($inputs,array(),array());
	if ($jobMeta->input_files == 0){
		$_SESSION['errorData']['Error'][]="Internal tool execution has no input files defined";
	        return 0;
	}
	
	// Set Arguments
	$args['working_dir']=$jobMeta->working_dir;

	$jobMeta->setArguments($args,$tool);

	// Create working_dir
	$jobId = $jobMeta->createWorking_dir();
	if (!$jobId){
		$_SESSION['errorData']['Error'][]="Cannot create tool temporal working dir";
	        return 0;
	}

	// Set outfiles metadata -- for register latter
	$jobMeta->setStageout_data($outs);

	// Setting Command line. Adding parameters

	$r = $jobMeta->prepareExecution($tool,$files);
	if($r == 0)
		return 0;

	// Launching Tooljob
	$pid = $jobMeta->submit($tool);
	if($pid == 0)
	        return 0;

	addUserJob($_SESSION['User']['_id'],(array)$jobMeta,$jobMeta->pid);

	return $jobMeta->pid;
}



function parse_configFile($configFile){
	$configParsed = array();

	// load config as json
	$config = json_decode(file_get_contents($configFile));

	// parse json
	$configParsed['input_files']= array();
	if ($config->input_files){
		foreach ($config->input_files as $input){
			if(!isset($configParsed['input_files'][$input->name]))
				$configParsed['input_files'][$input->name]=array();
			$input_fn = getAttr_fromGSFileId($input->value,'path');
			if ($input_fn)
				array_push($configParsed['input_files'][$input->name],str_replace($_SESSION['User']['id']."/","",$input_fn));
			else
				array_push($configParsed['input_files'][$input->name],$input->value);
		}
	}
	$configParsed['arguments']= array();
	if ($config->arguments){
		foreach ($config->arguments as $arg){
			$configParsed['arguments'][$arg->name] = $arg->value;
		}
	}
	return $configParsed;
}


function parse_submissionFile_SGE($rfn){
        $cmdsParsed = array();
///orozco/services/Rdata/Web/apps/nucleServ_MuG/nucleosomeDynamics_wf.py --config /orozco/services/Rdata/MuG/MuG_userdata//MuGUSER57ecf22d91df3/pyNewProj/.config.json --root_dir /orozco/services/Rdata/MuG/MuG_userdata//MuGUSER57ecf22d91df3 --metadata /orozco/services/Rdata/MuG/MuG_userdata//MuGUSER57ecf22d91df3/pyNewProj/.metadata.json --out_metadata /orozco/services/Rdata/MuG/MuG_userdata//MuGUSER57ecf22d91df3/pyNewProj/.results.json >> /orozco/services/Rdata/MuG/MuG_userdata//MuGUSER57ecf22d91df3/pyNewProj/.tool.log 2>&1


        $cmds = preg_grep("/^\//",file($rfn));
        $cwd  = str_replace("cd ","",join("",preg_grep("/^cd /", file($rfn))));

        $n=1;
        foreach ($cmds as $cmd){

                $cmdsParsed[$n]['cmdRaw']    = $cmd;
                $cmdsParsed[$n]['cwd']       = $cwd;

                $cmdsParsed[$n]['prgName']   = "";      # tool executable name for table title
                $cmdsParsed[$n]['params']    = array(); # paramName=>paramValue

                if (preg_match('/^#/',$cmd))
                        continue;
                if (preg_match('/^(.[^ ]*) (.[^>]*)(\d*>*.*)$/',$cmd,$m)){
                        $executable =  ($m[1]? basename($m[1]):"No information" );
                        $paramsStr  =  ($m[2]? $m[2]:"" );
                        $log        =  ($m[3]? $m[3]:"" );

                        // parse executable file
                        $cmdsParsed[$n]['prgName']  = $executable;
                
                        // parse cmd params
                        foreach (split("--",$paramsStr) as $p){
                                trim($p);
                                if (!$p)
                                        continue;
                                list($k,$v) = split(" ",$p);
                                if (strlen($k)==0 && strlen($v)==0)
                                        continue;
                                if (!$v)
                                        $v="";
                                // if paramValue is a file, show only 'project/filename'
                                $v  = str_replace($GLOBALS['dataDir']."/".$_SESSION['User']['id']."/","",$v);

                                // HACK; when rfn comes from sample data, filenames in cmd do not contain the right userId. Cutting filepath using explode
                                if (preg_match('/^\//',$v)){
                                        $project = explode("/",$rfn);
                                        $v = $project[count($project)-2]."/".basename($v);
                                }
                                $cmdsParsed[$n]['params'][$k]=$v;
                        }
                }
                $n++;
        }
        return $cmdsParsed;
}


function getVisualizers_List() {
	
	$visualizers = $GLOBALS['visualizersCol']->find(array('external' => true), array('name' => 1, 'title' => 1, 'short_description' => 1, 'keywords' => 1))->sort(array('title' => 1));

	return iterator_to_array($visualizers);	
	
}

function getFileTypes_List() {
	
	$tls = $GLOBALS['toolsCol']->find(array('external' => true), array('name' => 1, 'input_files' => 1))->sort(array('name' => 1));

	$tools = iterator_to_array($tls);

	sort($tools);

	$filetypes = array();

	$i = 0;

	foreach($tools as $t) {

		if(isset($t['input_files'])) {

			$filetypes[$i]['name'] = $t['name'];

			$types = array();

			foreach($t['input_files'] as $if) array_push($types, implode($if['file_type'])); 

			$filetypes[$i]['file_types'] = $types;

			$i ++;

		}

	}

	return $filetypes;

}



?>
