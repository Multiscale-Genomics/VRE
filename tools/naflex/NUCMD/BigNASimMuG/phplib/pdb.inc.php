<?php

function getNUCDBData($id, $extended = False) { // Database stuff
    $data = $GLOBALS['simData']->findOne(array("_id" => $id));
    if (!$data["_id"]) {
        print errorPageMMB("Error", "<h3>Nucleic Acid Trajectory code " . $id . " not found...</h3>");
        exit;
    }
    return prepNUCDBData($data);
}

function prepNUCDBData($data) { //Data formatting
//foreach (array_keys ($data) as $f){
//	echo "$f --> $data[$f]";
//}

        //if ($data['forceField']=="parm99"){
	//	$data['forceField']='parmBSC1';
	//}

        if ($data['forceField']=="parmbsc0"){
		$data['forceField']='parmBSC0';
	}

        if ($data['forceField']=="parmbsc1"){
		$data['forceField']='parmBSC1';
	}

        // HARDCODE FORCEFIELD for parmBSC1 NUCMD project
        //if (!preg_match('/BSC1-/',$data['_id']) and $data['forceField']!='parmBSC0'){
        //        $data['forceField'] = "parmBSC1";
        //}

        if (preg_match('/B/',$data['SubType'])){
                $data['SubType'] = "B";
        }

        if (preg_match('/Z/',$data['SubType'])){
                $data['SubType'] = "Z";
        }

        #if (preg_match('/[1-9]\w{3}/',$data['PDB'])){
	if($data['PDB']){
                $data['PDB'] = strtoupper($data['PDB']);
        }

        if (preg_match('/^No$/i',$data['PDB']) or preg_match('/initio/i',$data['PDB'])){
                $data['PDB'] = "NONE";
        }

	// Adding Temperature units (K) 
	if (!preg_match('/K$/',$data['Temperature'])){
		$data['Temperature'] = $data['Temperature']."K"; 
	}

	// Translating spanish words 
	$data['Parts'] = str_replace("iones","ions",$data['Parts']); 
	$data['IonicConcentration'] = str_replace("electroneutralidad","Electroneutrality",$data['IonicConcentration']); 
	$data['IonicConcentration'] = str_replace("electroneutral","Electroneutrality",$data['IonicConcentration']);

	// Getting total time of the simulation from number of frames and timestep
	$data['time'] = $data['time'] ? number_format($data['time']) : '-'; 

        // Showing just Author initials
        $expr = '/(?<=\s|^)[a-z]/i';
        preg_match_all($expr, $data['Author'], $matches);
        $result = implode('.', $matches[0]).".";
        $result = strtoupper($result);
        $data['Author'] = $result;

        // Triplex, quadruplexes...
        if( preg_match('/|/',$data['sequence']) ){
                $difSeqs = preg_split ('/\|/',$data['sequence']);
                if(count($difSeqs) >= 2){
                        foreach ($difSeqs as $k => $v) {
                                $index = $k+1;
                                $data['sequenceMulti'][$index] = $v;
                        }
                        $index = $index + 1;
                        #$data['sequenceMulti'][$index] = $data['rev_sequence'];
                }
                $data['sequence'] = str_replace('|',' | ',$data['sequence']);
        }


/*
	$frames = $data['Frames'];
	$frameStep = $data['FrameStep'];
	$time = "?";

	if(preg_match('/[np]s/',$frameStep,$matches)){
        	$units = $matches[0];
	        $fs = preg_replace('/$units/', '', $frameStep);
        	$time = $frames * $fs;
	        if ($units == "ps"){
        	        $time = $time/1000;
	                $time = "$time ns";
        	}
	        else{
        	        $time = "$time $units";
	        }
	}
	$data['time'] = $time;
*/

	$data['sequenceWeb'] = $data['sequence'];
	$data['rev_sequenceWeb'] = $data['rev_sequence'];
#	if (strlen($data['sequence']) > 15){
#		$newSeq = chunk_split($data['sequence'],15,'<br>');
#		$data['sequenceWeb'] = $newSeq;
#
#		if (strlen($data['rev_sequence']) > 15){
#			$newRevSeq = chunk_split($data['rev_sequence'],15,'<br>');
#			$data['rev_sequenceWeb'] = $newRevSeq;
#		}
#	}
	

/*    $data['resol'] = sprintf("%.2f", $data['resol']);
    if ($data['resol'] == 0)
        $data['resol'] = "N.A.";
    else
        $data['resol'] +=0;
    if (isUpper($data['header'], 20))
        $data['header'] = fixSpecialText(ucfirst(strtolower(trim($data['header']))));
    $data['compType'] = ucfirst(strtolower(trim($data['compType'])));
    if (isUpper($data['compound'], 20))
        $data['compound'] = ucfirst(strtolower(trim($data['compound'])));
    if ($data['sources'])
        $data['sourcesTxt'] = ucfirst(strtolower(trim(join(", ", $data['sources']))));
    else
        $data['sourcesTxt'] = 'N.A.';
    if ($data['authors'])
        $data['autsTxt'] = preg_replace("/ *(\.|,)/", "$1", ucwords(strtolower(preg_replace("/(,|\.)/", "$1 ", join(", ", $data['authors'])))));
    $data['expType'] = str_replace("_", " ", $data['expType']);
    if ($data['extended']) {
        foreach (array_values($data['chain']) as $chId) {
            if ($data['chainData'][$chId]['sqclusters']) {
                foreach (array_keys($data['chainData'][$chId]['sqclusters']) as $cl) {
                    if (preg_match('/^cl/', $cl))
                        $data['chainData'][$chId]['clclusters'][$cl] = $data['chainData'][$chId]['sqclusters'][$cl];
                    else
                        $data['chainData'][$chId]['bcclusters'][$cl] = $data['chainData'][$chId]['sqclusters'][$cl];
                }
            }
        }
    }
*/    return $data;
}


function getPDBData($id, $extended = False) { // Database stuff
    $data = $GLOBALS['PDB_EntryCol']->findOne(
            array("_id" => strtoupper($id))
    );
    if (!$data["_id"]) {
        print errorPageMMB("Error", "<h3>PDB code " . $id . " unknown</h3>");
        exit;
    }
    if ($extended) {
        $data['extended'] = True;
        foreach (array_values($data['chain']) as $chId) {
            $data['chainData'][$chId] = $GLOBALS['chainCol']->findOne(
                    array('_id' => $chId)
            );
            $data['chainData'][$chId]['seq'] = $GLOBALS['sequencesCol']->findOne(
                    array('_id' => $chId)
            );
# SwpHit via bc-100
            if (!$data['chainData'][$chId]['swpHit']['idHit']) {
    	    	$clref= $GLOBALS['chainCol']->findOne(array('_id'=>$data['chainData'][$chId]['sqclusters']['bc-100']));
		$data['chainData'][$chId]['swpHit']=$clref['swpHit'];
    	    }
        }
        if ($data['hetatm'])
            foreach (array_values($data['hetatm']) as $hetId) {
                $data['hetData'][$hetId] = $GLOBALS['monomersCol']->findOne(
                        array('_id' => $hetId)
                );
            }
        $data['dbxref'] = iterator_to_array(
                $GLOBALS['headersCol']->find(
                        array('dbxref.PDB' => $data['_id'])
                )
        );
        foreach (array_keys($data['dbxref']) as $xrefId) {
            $ss = $GLOBALS['sourcesCol']->findOne(
                    array('_id' => $data['dbxref'][$xrefId]['source'])
            );
            $data['dbxref'][$xrefId]['sourceTxt'] = $ss['source'];
        }
    }
    return prepPDBData($data);
}

function prepPDBData($data) { //Data formatting
    $data['resol'] = sprintf("%.2f", $data['resol']);
    if ($data['resol'] == 0)
        $data['resol'] = "N.A.";
    else
        $data['resol'] +=0;
    if (isUpper($data['header'], 20))
        $data['header'] = fixSpecialText(ucfirst(strtolower(trim($data['header']))));
    $data['compType'] = ucfirst(strtolower(trim($data['compType'])));
    if (isUpper($data['compound'], 20))
        $data['compound'] = ucfirst(strtolower(trim($data['compound'])));
    if ($data['sources'])
        $data['sourcesTxt'] = ucfirst(strtolower(trim(join(", ", $data['sources']))));
    else
        $data['sourcesTxt'] = 'N.A.';
    if ($data['authors'])
        $data['autsTxt'] = preg_replace("/ *(\.|,)/", "$1", ucwords(strtolower(preg_replace("/(,|\.)/", "$1 ", join(", ", $data['authors'])))));
    $data['expType'] = str_replace("_", " ", $data['expType']);
    if ($data['extended']) {
        foreach (array_values($data['chain']) as $chId) {
            if ($data['chainData'][$chId]['sqclusters']) {
                foreach (array_keys($data['chainData'][$chId]['sqclusters']) as $cl) {
                    if (preg_match('/^cl/', $cl))
                        $data['chainData'][$chId]['clclusters'][$cl] = $data['chainData'][$chId]['sqclusters'][$cl];
                    else
                        $data['chainData'][$chId]['bcclusters'][$cl] = $data['chainData'][$chId]['sqclusters'][$cl];
                }
            }
        }
    }
    return $data;
}

function fixSpecialText($a) { //TODO completar    
    $a = preg_replace("/rna( |\.|,|\/|$)/i", "RNA$1", $a);
    $a = preg_replace("/dna( |\.|,|\/|$)/i", "DNA$1", $a);

    return $a;
}

// 
function filter($mode, $pdb, $filter = '',$not=false) {
    switch ($mode) {
        case FIRSTHET : // $filter = idMon
            $ress = "";
            $outpdb = "";
            foreach (explode("\n", $pdb) as $lin) {
                if (preg_match("/^HETATM/", $lin) and
                        preg_match("/" . $filter . "/", $lin)) {
                    $resid = substr($lin, 21, 5);
                    if (!$ress)
                        $ress = $resid;
                    if ($ress == $resid)
                        $outpdb .= "$lin\n";
                }
            }
            break;
        case HEAD:
            $outpdb = headers($pdb, True);
            break;
        case NOHEAD:
            $outpdb = headers($pdb, False);
            break;
        case GROUP:
            $outpdb = "";
            $filter = strtoupper($filter);
            if ($filter == 'HETATM')
                $filter = '(HETATM|CONNECT)';
            foreach (explode("\n", $pdb) as $lin) {
                if (preg_match('/^(MODEL|ENDMDL)/', $lin))
                    $outpdb .= "$lin\n";
                if (preg_match("/^" . $filter . "/", $lin))
                    $outpdb .= "$lin\n";
            }
            break;
        case ATOMSET:
//  [NomRes]NumRes:Chain.nomat/model Jmol like
            /*
             * ATOM     12  O   PRO A  47      47.248  16.227  63.502  1.00 61.58           O
             * 012345678901234567890123456789012345678901234567890123456789012345678901234567
             */
	    $outpdb='';
            $fd = parse_filter($filter);
//     print_r($fd);
            $pdbnh = headers($pdb, False);
            foreach (explode("\n", $pdbnh) as $lin) {
		$linok=false;
                if (preg_match('/^MODEL *([0-9]*)/', $lin, $match))
                    $model = $match[1];
                if (preg_match('/^(MODEL|ENDMDL)/', $lin) and (!$fd[5] or matchList($fd[5], $model))) {
                    $linok = true;
                } elseif (
                        (!$fd[5] or !$model or matchList($fd[5], $model)) and
                        (!$fd[1] or matchList($fd[1], substr($lin, 17, 3))) and
                        (!$fd[2] or matchList($fd[2], substr($lin, 22, 4))) and
                        (!$fd[3] or matchList($fd[3], substr($lin, 21, 1))) and
                        (!$fd[4] or matchList($fd[4], substr($lin, 12, 4)))
                ) {
                    $linok = true; 
                }
		if ((!$not and $linok) or ($not and !$linok))
			$outpdb .= "$lin\n";
            }
            break;
    }
    return $outpdb;
}

function parse_filter($a) {
    $d = array();
    $a = str_replace(' ', '', $a);
    #$a = str_replace('*', '', $a);
// afegir camps buits
    if (!preg_match('/\[/', $a))
        $a = '[]' . $a;
    if (!preg_match('/:/', $a))
        $a = preg_replace('/(\][0-9\-\,]*)/', "$1:", $a);
    if (!preg_match('/\./', $a))
        $a = preg_replace('/:([^\/]*)/', ":$1.", $a);
//
    $d = preg_split('/[\[\]:\.\/]/', $a);
// Mantenim * nomes a atom
    $d[2] = str_replace('*','',$d[2]);
    $d[3] = str_replace('*','',$d[3]);
    return $d;
}

function headers($pdb, $rethead) {
    $outpdb = "";
    $head = True;
    foreach (explode("\n", $pdb) as $lin) {
        if (preg_match("/^(ATOM|MODEL)/", $lin))
            $head = False;
        if ($head == $rethead)
            $outpdb .= "$lin\n";
    }
    return $outpdb;
}

function matchList($l, $a) {
    $m = false;
    foreach (explode(',', $l) as $t) {
        if (preg_match('/-/', $t)) {
            list ($min, $max) = explode('-', $t);
            $m = ($m or ((trim($a) >= $min) and (trim($a) <= $max)));
        }
        else {
            $m = ($m or matchStr(trim($t),trim($a)));
        }
    }
    return $m;
}

function matchStr($a,$b) {  
	return (fnmatch ($a,$b) or fnmatch($b,$a));
}
  
function calcHBonds($pdb,$inter=false, $type=false) {
      	$pdbnh = headers($pdb, False);
	$outpdb='';
	header('Content-type:text/plain');
  	$i=0;
      	foreach (explode("\n", $pdbnh) as $lin) {
 		$nat[$i] = substr($lin, 5, 6)+0;
        	$atid[$i] = trim(substr($lin, 12, 5));
        	$resid[$i] = trim(substr($lin, 17, 4));
        	$chid[$i] = substr($lin, 21, 2);
		$resn[$i] = substr($lin,23,4);
		$residstr[$i] = $resid[$i].' '.$chid[$i].trim($resn[$i]);
        	$atx[$i] = substr($lin, 30,8)+0;	
        	$aty[$i] = substr($lin, 38,8)+0;	
        	$atz[$i] = substr($lin, 46,8)+0;	
        	if (preg_match('/^ATOM|HETA/',$lin))
			$i++;
	}
	$totat=$i-1;
	for ($i=0;$i<$totat-1;$i++) {
		$hbs[$i] = array();
		for ($j=$i+1;$j<$totat;$j++) {
			if (($residstr[$i] != $residstr[$j]) and (!$inter or ($chid[$i] != $chid[$j]))) {
				$d = calcDist2($atx[$i],$aty[$i],$atz[$i],$atx[$j],$aty[$j],$atz[$j]);
				if ($d <= $GLOBALS['HBDIST2']) 
					$hbs[$i][] = array($j,$d);
			}
		}
		foreach (array_values($hbs[$i]) as $hb) {	 			
			$tipHB = $GLOBALS['HBondDef'][$resid[$i].' '.$atid[$i].' '.$resid[$hb[0]].' '.$atid[$hb[0]]].$GLOBALS['HBondDef'][$resid[$hb[0]].' '.$atid[$hb[0]].' '.$resid[$i].' '.$atid[$i]];
			if (!$type or ($type == $tipHB))
				$outpdb .= "$residstr[$i] $atid[$i] - ".$residstr[$hb[0]]." ".$atid[$hb[0]]." : ".sprintf ("%5.2f",sqrt($hb[1]));
			if ($tipHB)	
				$outpdb .= "($tipHB)";
		$outpdb .= "\n";
		}
	}
	return $outpdb;
} 

function calcDist2($x1,$y1,$z1,$x2,$y2,$z2) {
	return ($x1-$x2)*($x1-$x2)+($y1-$y2)*($y1-$y2)+($z1-$z2)*($z1-$z2);
}

function checkDuplex($curvesFile) {

	$check = 0;

	#  Strand  1 has  10 bases (5'-3'): GGGGGGGGGG
	#  Strand  2 has  20 bases (3'-5'): GGGGGGGGGGCCCCCCCCCC
	#  NucType: DNA

        $cmd = "grep 'Strand  1' $curvesFile";
        $out = exec($cmd,$strand1);

        $cmd = "grep 'Strand  2' $curvesFile";
        $out = exec($cmd,$strand2);

        $l1 = preg_split ("/:/",$strand1[0]);
        $seq1 = $l1[sizeof($l1)-1];
        $l2 = preg_split ("/:/",$strand2[0]);
        $seq2 = $l2[sizeof($l2)-1];
        $seq1 = preg_replace("/ /","",$seq1);
        $seq2 = preg_replace("/ /","",$seq2);

        $arrSeq1 = preg_split("//",$seq1);
        array_shift($arrSeq1);
        array_pop($arrSeq1);
        $arrSeq2 = preg_split("//",$seq2);
        array_shift($arrSeq2);
        array_pop($arrSeq2);

        $length1 = count($arrSeq1);
        $length2 = count($arrSeq2);

	return ($length1 == $length2);	
}
