<?php
require "phplib/global.inc.php";
require "phplib/aux.inc.php";
#

# Getting REQUEST parameters from Ajax -- php call.
$query = $_REQUEST;

$cond = array();

    if ($query['idBPS'] and $query['idBPS'] != "BPS"){
        $bps = $query['idBPS'];
        $idSubSeq = $bps;
    }
    if ($query['idSubSeq']){
        $idSubSeq = $query['idSubSeq'];
    }

    $flank = 0;
    if ($query['flank']){
        $flank = $query['flank'];
        $reg_flank = "{".$flank."}";
    }

    $idSubSeq = strtoupper($idSubSeq);
    if($idSubSeq and $idSubSeq != "SELECT BASE PAIR STEP") {
        if($reg_flank){
            $rex = new MongoRegex("/\w$reg_flank$idSubSeq\w$reg_flank/i");
        }
        else{
            $rex = new MongoRegex("/$idSubSeq/i");
        }
            $cond[] = array('sequence' => $rex);
    }
    else if ($idSubSeq == "SELECT BASE PAIR STEP"){
        $idSubSeq = '';
    }

# Search typeFrom 
$ajaxCode = $query['typeForm'];

        $arrayOnto = array();
        foreach ($ajaxCode as $onto => $v) {
		#echo "Onto $onto, v: $v<br/>";

		if ($onto == "Structure" and $v == "Structure") 
			continue;
		if ($onto == "TrajectoryType" and $v == "TrajectoryType") 
			continue;
		if ($onto == "HelicalConf" and $v == "OriginalHelicalConformation") 
			continue;
		if ($onto == "LocalStructures" and $v == "LocalStructures") 
			continue;
		#if ($onto == "SeqFeatures" and $v == "SequenceFeatures") 
		#	continue;

		# Checkboxes, more than one possible option ==> OR 
		if ($onto == "NAType" or $onto == "SequenceModifications" or $onto == "SystemType" or $onto == "SeqFeatures" or $onto == "LocalStructures"){

			if (isset($ajaxCode[$onto][$onto])){
				continue;
			}

			if (! empty($ajaxCode[$onto])){

				$condNAtype = array();
	        		foreach ($ajaxCode[$onto] as $natype => $v2) {
        	        		$codeOnto = $ontoHash2[$v2];
                			$codeOnto += 0;
			                $arrayOnto = new MongoRegex( "/^". $codeOnto ."/" );
        	        		$condNAtype[] =  array('ontology' => $arrayOnto);
				}
				$fcondNAtype = array('$or' => $condNAtype);
				$cond[] = $fcondNAtype;
			}
			continue;
		}
                if ($onto == "temperature"){
                        if ($v == "Select Temperature:")
                                continue;
                }
                if ($onto == "length"){
                        if ($v == "Select Simulation Length:")
                                continue;
                }
                if ($onto == "forceField"){
                        if ($v == "Select Force Field:")
                                continue;
                }
                if ($onto == "waterType"){
                        if ($v == "Select Water Type:")
                                continue;
                }
                if ($onto == "ionicConc"){
                        if ($v == "Select Ionic Concentration:")
				continue;
                }
                if ($onto == "ionsParams"){
                        if ($v == "Select Ions Parameters:")
                                continue;
                }
                $codeOnto = $ontoHash2[$v];
                #echo "-$v- $codeOnto <br/>";
                $codeOnto += 0;
                $arrayOnto = new MongoRegex( "/^". $codeOnto ."/" );
                $cond[] =  array('ontology' => $arrayOnto);

        }

    if(!empty($cond)){
        $fcond = array('$and' => $cond);
    }
    else {
        $fcond = array('_id' => array( '$exists' => 1));
    }

    //print "<pre>";
    //print json_encode($fcond);
    //print "</pre>";

    $count = $simData->find($fcond)->count();
    $list = $simData->distinct("_id",$fcond);

    $arr = Array();
    $arr['count'] = $count;
    $arr['list'] = $list;

    $ret = json_encode($arr);
    #echo "Results found: ($count)";

    echo "$ret";

?>
