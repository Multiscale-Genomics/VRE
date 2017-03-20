<?php

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



function parse_config($json){
	return 1;
	
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

                $cmdsParsed[$n]['prgPrefix'] = "";      # toolId as appears in help
                $cmdsParsed[$n]['prgName']   = "";      # tool executable name for table title
                $cmdsParsed[$n]['params']    = array(); # paramName=>paramValue

                if (preg_match('/^#/',$cmd))
                        continue;
                if (preg_match('/^(.[^ ]*) (.[^>]*)(\d*>*.*)$/',$cmd,$m)){
                        $executable =  ($m[1]? basename($m[1]):"No information" );
                        $paramsStr  =  ($m[2]? $m[2]:"" );
                        $log        =  ($m[3]? $m[3]:"" );

                        $cmdsParsed[$n]['prgName']  = $executable;
                        foreach (split("--",$paramsStr) as $p){
                                trim($p);
                                if (!$p)
                                        continue;
                                list($k,$v) = split(" ",$p);
                                if (strlen($k)==0 && strlen($v)==0)
                                        continue;
                                if (!$v)
                                        $v="";

                                $v  = str_replace($GLOBALS['dataDir']."/".$_SESSION['User']['id']."/","",$v);
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

?>
