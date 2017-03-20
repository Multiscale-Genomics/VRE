<?php
require_once "phplib/constants.inc.php";
require_once "phplib/aux_.inc.php";

function generateSessId () {
	umask(0);
	$sessId = randomString(10);
	while (file_exists(TMPDIR."/".$sessId)) {
		$sessId = randomString(10);
	}
	
	return $sessId;
}

function checkBDParameters ($req) {
	if ($req["analysisType"]=="BD") {
		$itempsmax=$req["BDITempsMax"];
		$dt=$req["BDDT"];
		$itsnap=$req["BDITSnap"];
		
		if ($itempsmax>MAXBDSTEPS) {
			returnError(5);
		}
		
		$simulatedTime=$itempsmax*$dt;
		if ($simulatedTime>MAXBDTIME) {
			returnError(6);
		}
		
		$framesWritten=$itempsmax/$itsnap;
		if ($framesWritten>MAXBDFRAMES) {
			returnError(7);
		}
	}
}

function checkEMailPresence ($req) {
	if (FORCEEMAIL && (strcmp($req["email"], "") == 0)) {
			returnError(1);
	}
}

function generateVMDImage2 ($fpdb, $fout, $coarseGrained) {
	global $_MYSESSION;

	if ($coarseGrained) {
		$representation = "mol representation tube";
	} else {
		$representation = "mol representation newCartoon 0.2 20 5";
	}

	$ftmp = tempnam(".", "tga");
	$vmdScript =
	   "mol load pdb $fpdb
		display projection orthographic
		color Display Background white
		axes location off
		display nearclip set 0
		display height 4
		lappend auto_path $baseDir/soft/vmd/la1.0
		lappend auto_path $baseDir/soft/vmd/orient
		package require Orient
		namespace import Orient::orient
		set sel [atomselect top \"all\"]
		set I [draw principalaxes \$sel]
		set A [orient \$sel [lindex \$I 2] {1 0 0}]
		\$sel move \$A
		set I [draw principalaxes \$sel]
		set A [orient \$sel [lindex \$I 1] {0 1 0}]
		\$sel move \$A
		graphics top delete all
		mol delrep 0 0
		mol color structure
		mol material BrushedMetal
		$representation
		mol addrep top
		mol color colorid 4
		mol material BrushedMetal
		mol selection (within 5 of resname \\\"CY\\.\\\") and (resname \\\"CY\\.\\\")  and mass > 2
		mol representation Licorice 0.3 20 20
		mol addrep top
		render TachyonInternal $ftmp
		quit";

	$vmdScriptFile = tempnam (".", "vmdScript");
	$fvmd = sfopen ($vmdScriptFile, "w");
	fwrite ($fvmd, $vmdScript);
	fclose ($fvmd);

    # Not-queued script for quick calculus
	$noqueueScriptFile = tempnam (".", "noqueueScriptFile");
    $fq0 = sfopen ($noqueueScriptFile, "w");
    fprintf ($fq0, "#!/bin/bash\n\n");
	fprintf ($fq0, "%s\n", "cd ".$_MYSESSION["workDir"]."\n");
	fprintf ($fq0, "%s\n", "export VMDDIR=".VMDDIR);
	fprintf ($fq0, "%s\n", "export STRIDE_BIN=".STRIDEBIN."\n");
	fprintf ($fq0, "%s\n", VMDBIN." -dispdev text -size 300 300 -e $vmdScriptFile");
	fprintf ($fq0, "%s\n", "tgatoppm $ftmp | pnmcrop -white | pnmtopng -transparent white > $fout");
	fprintf ($fq0, "\n");
    fclose ($fq0);
    exec ("/bin/sh $noqueueScriptFile\n");

	unlink ($noqueueScriptFile);
	unlink ($vmdScriptFile);
	unlink ($ftmp);
}













function getAccessibilities($pdbFile) {
	$bn = basename ($pdbFile);
	$rsaFile = substr ($bn, 0, strpos ($bn, ".")).".rsa";
	exec(NACCESS." ".$pdbFile." &> naccess.error");

	$fin=sfopen($rsaFile, "r");
	while(!feof($fin)) {
		$line=fgets($fin);
		$header=substr($line, 0, 3);
		if ($header=="RES") {
			$residueName  =substr($line, 4, 4);
			$residueNumber=substr($line, 9, 4)+0;
			$absAcc=substr($line, 13, 9)+0.0;
			$relAcc=substr($line, 22, 6)+0.0;
			$accesibility[$residueNumber]["name"]  =$residueName;
			$accesibility[$residueNumber]["absAcc"]=$absAcc;
			$accesibility[$residueNumber]["relAcc"]=$relAcc;
		}
	}
	fclose($fin);

	return $accesibility;
}

function buildMask($accesibility, $maskType, $threshold) {
	foreach ($accesibility as $rn => $residue) {
		if ($residue["relAcc"]<=$threshold) {
			$buried[]=$rn;
		} else {
			$notburied[]=$rn;
		}
	}

	if ($maskType=="buried" && count($buried)>0) {
		$mask=":" . implode(",", $buried);
	} elseif ($maskType=="notburied" && count($notburied)>0) {
		$mask=":" . implode(",", $notburied);
	} else {
		$mask="";
	}

	return $mask;
}

function processHETATM($fpdb, $modResFile) {
	# Read HETATM translation table
	$flist = sfopen ($modResFile, "r");
	while ($line = fgets($flist)) {
		list ($old,$new,$label) = split (' ',$line);
		$modList[$old]=trim($new);
	}
	fclose ($flist);
	
	# Process the fils, storing ATOM lines and adapting HETATM lines
	$tmpfile = tempnam (".", "HETATMtoATOM");

	$fin = sfopen($fpdb, "r");
	$fout = sfopen($tmpfile, "w");
	$line = fgets($fin);
	while ($line != FALSE) {
		if (strncmp($line, "HETATM", 6) == 0) {
			$resName = trim(substr($line, 17, 4));
			if ($modList[$resName]) {
				$line = str_replace ($resName, $modList[$resName], $line);
				$line = str_replace ("HETATM", "ATOM  ", $line);
				#JL Pendent de comprovar eliminem els fosfats que hi pugui haver per evitar el filtre de DNA
				$line = str_replace (" P ", " X ", $line);
				fwrite($fout, $line);
			}
		}

		if (strncmp($line, "ATOM  ", 6) == 0) {
			fwrite($fout, $line);
		}
		$line = fgets($fin);
	}

	fclose($fout);
	fclose($fin);

	# Switch temoral and old files
	unlink ($fpdb);
	rename ($tmpfile, $fpdb);
}


function getCA($in, $out, $modResFile) {
	$flist = sfopen ($modResFile, "r");
	while ($line = fgets($flist)) {
		list ($old,$new,$label) = split (' ',$line);
		$modList[$old]=trim($new);
	}
	fclose ($flist);
	$idx = 1;
	$fin = sfopen($in, "r");
	$fout = sfopen($out, "w");
	$line = fgets($fin);
	while ($line != FALSE) {
		if (trim(substr($line,0,6)) == "HETATM") {
				$resName = trim(substr($line,17,4));
				if ($modList[$resName]) {
					$line = str_replace ($resName, $modList[$resName], $line);
					$line = str_replace ("HETATM","ATOM  ", $line);
					#JL Pendent de comprovar eliminem els fosfats que hi pugui haver per evitar el filtre de DNA
					$line = str_replace (" P ", " X ", $line);
				}
		}

		if (trim(substr($line, 0, 4)) == "ATOM") {
			if (trim(substr($line, 12, 4)) == "CA") {
				fwrite($fout, $line);
				$idx++;
			}
		}
		$line = fgets($fin);
	}

	fclose($fout);
	fclose($fin);
}

function computeSecStructure ($pdbFile, $outFile) {
	exec (STRIDEBIN." -f$outFile $pdbFile");
}

function buildMaskSecondary($ssFile, $type) {
	$mask = "";
	$fin = sfopen ($ssFile, "r") ;
	while (($line = fgets ($fin)) != FALSE) {
		if (strncmp(substr ($line, 0, 3), "ASG", 3) == 0) {
			$resNum = substr ($line, 10, 5) + 0;
			$typeId = $line[24];
			if ($typeId == 'H' || $typeId == 'G' || $typeId == 'I') {
				$helixList[] = $resNum;
			} elseif  ($typeId == 'E') {
				$strandList[] = $resNum;
			} elseif  ($typeId == 'C' || $typeId == 'T') {
				$coilList[] = $resNum;
			}
		}
	}
	fclose ($fin);

	if (strcasecmp ($type, "helix") == 0) {
		if (count ($helixList) > 0) {
			$mask = ":".implode (",", $helixList);
		}
	} elseif (strcasecmp ($type, "strand") == 0) {
		if (count ($strandList) > 0) {
			$mask = ":".implode (",", $strandList);
		}
	} elseif (strcasecmp ($type, "coil") == 0) {
		if (count ($coilList) > 0) {
			$mask = ":".implode (",", $coilList);
		}
	}

	return $mask;
}

function searchFirstLineContaining ($file, $string) {
	$lineas = file ($file, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
	foreach ($lineas as $linea_num => $linea) {
		$found = strstr ($linea, $string);
		if ($found != FALSE) {
			break;
		}
	}
	return $found;
}

function pczFileContainsMinMaxEvecs ($pczFile, $minVecs, $maxVecs) {
	$tmp = tempnam ("/tmp", "flexibl");
	exec (PCZDUMP." -i $pczFile --info -o $tmp");

	$linea = searchFirstLineContaining ($tmp, "Vectors");
	$cachos = explode (":", $linea);
	$nVecs = $cachos[1] + 0;
	if (($minVecs > 0 && $nVecs < $minVecs) || ($maxVecs > 0 && $nVecs > $maxVecs)) {
		$result = FALSE;
	} else {
		$result = TRUE;
	}

	unlink ($tmp);

	return $result;
}

function pczFileHasFrames ($pczFile) {
	$tmp = tempnam ("/tmp", "flexibl");
	exec (PCZDUMP." -i $pczFile --info -o $tmp");

	$linea = searchFirstLineContaining ($tmp, "Frames");
	$cachos = explode (":", $linea);
	$nFrames = $cachos[1] + 0;

	unlink ($tmp);

	return $nFrames;
}

function pczFileHasRMSdType ($pczFile, $desiredType) {
	$tmp = tempnam ("/tmp", "flexibl");
	exec (PCZDUMP." -i $pczFile --info -o $tmp");

	$linea = searchFirstLineContaining ($tmp, "RMSd type");
	$cachos = explode (":", $linea);
	$type = $cachos[1];
	switch ($desiredType) {
		case STDRMS:
			$result = (FALSE != strstr ($type, "Standard"));
			break;
		case GAUSSRMS:
			$result = (FALSE != strstr ($type, "Gaussian"));
			break;
	}

	unlink ($tmp);

	return $result;
}

function searchSimilarProtein2 ($pdbFile) {
	$sequence = getSequence ($pdbFile);
	$blastr = identifyProtein ($sequence);
	$pdbCode = $blastr[0][0];

	return $pdbCode;
}

function getSequence2 ($pdbFile) {
	include "dictionary.inc.php";

	$sequence = "";
	$residueList = getResidues ($pdbFile);
	$nResidues = count ($residueList);
	for ($residue = 0; $residue < $nResidues; $residue++) {
		$sequence = $sequence.$three2one[$residueList[$residue]];
	}

	return $sequence;
}

function getSequenceFromFASTA ($file) {
	include "dictionary.inc.php";

	$sequence = "";
	$content = file ($file);
	foreach ($content as $line) {
		if ($line[0]!=">") {
			$sequence .= $line;
		}
	}

	# Check that the sequence only contains letters, "-" and "*"
	$coincidencias = array();
	if (preg_match('/[^a-zA-Z\n\s]+/', $sequence)==1) {
		# Invalid data
		$sequence = NULL;
	}

	return $sequence;
}

function identifyProtein2 ($sequence) {
	if ($sequence == "") {
		return NULL;
	}

	$blastinput = tempnam("/tmp", "blastin");
	$blastoutput = tempnam("/tmp", "blastout");

	// Write the sequence to a file
	$bi = sfopen ($blastinput, "w");
	fwrite ($bi, $sequence);
	fclose ($bi);

	// Perform the blast search
	exec (BLAST." -p blastp -d ".BLASTDB." -e ".BLASTMAXEVALUE." -i $blastinput -o $blastoutput");
	$outText = file ($blastoutput);

	$pattern = "/^([01-9a-zA-Z_]+)\s+mol:protein\s+length:(\d+)\s+.*\s+(\d+)\s+(.+)/";
	$matches = preg_grep ($pattern, $outText);
	//$pdbLine = reset ($matches);
	$i = 0;
	foreach ($matches as $pdbLine) {
		preg_match ($pattern, $pdbLine, $lineMatches);
		$results[$i][0] = $lineMatches[1];
		$results[$i][1] = $lineMatches[2];
		$results[$i][2] = $lineMatches[3];
		$results[$i][3] = $lineMatches[4];
		$i++;
	}

	// Delete the temporary files
	unlink ($blastinput);
	unlink ($blastoutput);
	/*
	for ($i=0; $i<count($results); $i++) {
		$pdbCode = $results[$i][0];
		$length  = $results[$i][1];
		$score   = $results[$i][2];
		$evalue  = $results[$i][3];

		print "<pre>$pdbCode - $length - $score - $evalue</pre>";
	}
	*/

	return $results;
}

function pczFileContainsOnlyMask ($pczFile, $mask) {
	$tmp1 = tempnam (".", "sizing");
	system (PCZDUMP." --avg --pdb -i $pczFile -o $tmp1");
	$tmp2 = tempnam (".", "sizing");
	system (PCZDUMP." --avg --pdb -i $pczFile -o $tmp2 -M \"$mask\"");

	$sameAtomNumber = (countLines ($tmp1) == countLines ($tmp2));

	unlink ($tmp1);
	unlink ($tmp2);
	
	return $sameAtomNumber;
}

function prepareMapFiles ($pdbFile, $serializedData, $gnuplotReadyData) {
	global $_MYSESSION;

	$map = generateMap($pdbFile);
	$_MYSESSION["PDBmap"] = $map;
	savePDBMap ($serializedData ,$map); # For serialized data
	writeMap ($map, $gnuplotReadyData); # For GNUPlot
}

?>
