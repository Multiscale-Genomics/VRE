<?php

ini_set('memory_limit', '2048M');

function isPDB ($file) {
	$str = @file_get_contents ($file);
	if ($str == false) {
		if (debugHost()) {
			echo "<p>Cannot open file: $file for reading</p>";
			exit();
		} else {
			header('Status: 302 Found');
			header("Location: error.php?idErr=22&sessId=$sessId");
			exit();
		}
	}
	return !(strpos ($str, "ATOM  ") === FALSE);
}

function hasDNA ($pdbFile) {
	$has = false;
	
	$ftraj=sfopen($pdbFile, "r");

	while(!feof($ftraj)) {
		$line=fgets($ftraj);
		if(strncmp($line, "ATOM  ", 6)==0) {
			$atomName=trim(substr($line, 12, 4));
			if($atomName=="P") {
				$has = true;
			}
		}
	}

	fclose($ftraj);

	return $has;
}

function getResidues2($pdbFile) {
	$ftraj=sfopen($pdbFile, "r");

	while(!feof($ftraj)) {
		$line=fgets($ftraj);
		if(strncmp($line, "ATOM", 4)==0) {
			$atomName=trim(substr($line, 12, 4));
			if($atomName=="CA") {
				$resNum=substr($line, 22, 4) + 0;
				$residueList[$resNum]=substr($line, 17, 3);
			}
		} else if(strncmp($line, "TER", 3)==0) {
			break;
		}

	}

	fclose($ftraj);

	return $residueList;
}

function getResiduesMap ($map, $chain=0) {
	foreach (array_values ($map["res"]) as $res) {
		if (!$chain or ($chain == $res["chain"]))
			$list[]=trim($res["resName"]);
	}
	return $list;
}

function getFirstResidueNumber ($pdbFile) {
	$resNum=-1;
	
	$ftraj=sfopen($pdbFile, "r");

	while(!feof($ftraj)) {
		$line=fgets($ftraj);
		if(strncmp($line, "ATOM", 4)==0) {
			$atomName=trim(substr($line, 12, 4));
			if($atomName=="CA") {
				$resNum=substr($line, 22, 4) + 0;
				break;
			}
		} else if(strncmp($line, "TER", 3)==0) {
			break;
		}
	}

	fclose($ftraj);

	return $resNum;
}

function getLastResidueNumber ($pdbFile) {
	$resNum=-1;
	
	$ftraj=sfopen($pdbFile, "r");

	while(!feof($ftraj)) {
		$line=fgets($ftraj);
		if(strncmp($line, "ATOM", 4)==0) {
			$atomName=trim(substr($line, 12, 4));
			if($atomName=="CA") {
				$resNum=substr($line, 22, 4) + 0;
			}
		} else if(strncmp($line, "TER", 3)==0) {
			break;
		}
	}

	fclose($ftraj);

	return $resNum;
}

function countNFrames($filename) {
	$nFrames=0;
	$atomReaded=false;
	$ftraj=sfopen($filename, "r");
	
	while(!feof($ftraj)) {
		$line=fgets($ftraj);
		if(strncmp($line, "ATOM", 4)==0) {
			if ($atomReaded==false) {
				$atomReaded=true;
				$nFrames++;
			}
		} else if(strncmp($line, "TER", 3)==0) {
			$atomReaded=false;
		}

	}
	fclose($ftraj);

	return $nFrames;
}

function writeFrame($filename, $filenameOut, $nFrame, $chain="") {
	$nFrames=countNFrames($filename);
	if ($nFrame>$nFrames) {
		$nFrame=$nFrames;
	}
	$nFrames=0;
	$atomsWritten=0;
	$atomReaded=false;
	$ftraj=sfopen($filename, "r");
	$fout=sfopen($filenameOut, "w");
    #JL Ajustem el format de $chain per detectar combinacions de cadenes amb expressió regular _A,C-E -> [AC-E]
    if ($chain and ($chain != "First"))
           $chain="/[".str_replace(',','',$chain)."]/i";
	while(!feof($ftraj) && $nFrames<=$nFrame) {
		$line=fgets($ftraj);
		if(strncmp($line, "ATOM", 4)==0 or strncmp($line, "HETA", 4) == 0) {
			if ($atomReaded==false) {
				$atomReaded=true;
				$nFrames++;
			}
			if ($line[21] == " ") {
				$line[21] = "_";
			}
			$actChain = $line[21];
    # JL Mantenim el filtre per detectar la primera cadena si $chain == FIRST
    # Afegim el filtre per evitar que la primera cadena sigui DNA
			if (($chain == "First") and (trim(substr($line, 12, 4)) == "CA"))  
				$chain = "/$actChain/i";

            if (($nFrames==$nFrame) && $atomReaded && ($chain != "First") && (!$chain || preg_match ($chain,$actChain))) {
				fwrite($fout, $line);
				$atomsWritten++;
			}
		} else if(strncmp($line, "ENDMDL", 6)==0) {
			$atomReaded=false;
		}
	}
	
	fclose($fout);
	fclose($ftraj);

	return $atomsWritten;
}

function extractAtomsFromPDB ($atom, $pdbin, $pdbout, $substituteBlankChain = "") {
	$atomsWritten=0;
	
	$fin=sfopen($pdbin, "r");
	$fout=sfopen($pdbout, "w");
	
	while (!feof ($fin)) {
		$line = fgets($fin);

		# Name chains without previous names
		if ($substituteBlankChain != "") {
			if ($line[21] == " ") {
				$line[21] = "_";
			}
		}

		# Select lines to print
		if (strncmp ($line, "ATOM  ", 6) == 0 or strncmp ($line, "HETATM", 6) == 0) {
			if (strcasecmp ($atom, "all") == 0 || $atom == "" || trim (substr ($line, 12, 4)) == $atom) {
				fwrite ($fout, $line);
				$atomsWritten++;
			}
		}
	}
	
	fclose($fout);
	fclose($fin);

	return $atomsWritten;
}

function trimPDB ($filename, $beginSelection, $endSelection) {
	if ($endSelection<=0) {
		$endSelection = 9999;
	}
	$filenameOut=tempnam(dirname($filename), "trimpdb");

	$ftraj=sfopen($filename, "r");

	$fout=sfopen($filenameOut, "w");

	while(!feof($ftraj)) {
		$line=fgets($ftraj);
		if(strncmp($line, "ATOM", 4)==0) {
			$resNumber = substr($line, 22, 4) + 0;
			if ($resNumber >= $beginSelection && $resNumber <= $endSelection) {
				fwrite($fout, $line);
			}
		}
	}
	
	fclose($fout);
	fclose($ftraj);

	unlink($filename);
	rename($filenameOut, $filename);
}

function extractCoordinates($pdbFile, $coordFile) {
	$ftraj=sfopen($pdbFile, "r");
	$fout=sfopen($coordFile, "w");
	fwrite($fout, "Coordinates extracted from PDB file\n");

	$nextColumn=1;
	while(!feof($ftraj)) {
		$line=fgets($ftraj);
		if(strncmp($line, "ATOM", 4)==0) {
			$x=substr($line, 30, 8);
			$y=substr($line, 38, 8);
			$z=substr($line, 46, 8);
			if ($nextColumn>10) {
				fwrite($fout, "\n");
				$nextColumn=1;
			}
			fwrite($fout, $x, 8);
			$nextColumn++;
			if ($nextColumn>10) {
				fwrite($fout, "\n");
				$nextColumn=1;
			}
			fwrite($fout, $y, 8);
			$nextColumn++;
			if ($nextColumn>10) {
				fwrite($fout, "\n");
				$nextColumn=1;
			}
			fwrite($fout, $z, 8);
			$nextColumn++;
			if ($atomReaded==false) {
				$atomReaded=true;
			}
		} else if(strncmp($line, "TER", 3)==0) {
			fwrite($fout, "\n");
			$nextColumn=1;
		}

	}
	fclose($fout);
	fclose($ftraj);
}

# Read a PDB file and store the atom coordinates in an array
function readCoordinates ($pdbFile) {
	$ftraj=sfopen($pdbFile, "r");
	$i = 0;
	
	while(!feof($ftraj)) {
		$line=fgets($ftraj);
		if(strncmp($line, "ATOM  ", 6)==0) {
			$positions[$i]["x"]=substr($line, 30, 8) + 0.0;
			$positions[$i]["y"]=substr($line, 38, 8) + 0.0;
			$positions[$i]["z"]=substr($line, 46, 8) + 0.0;
			$i++;
		}

	}
	fclose($ftraj);

	return $positions;
}

# Check that a PDB file contains atoms with readable coordinates
function isValidPDB ($pdbFile) {
	$ftraj=sfopen($pdbFile, "r");

	$valid = true;
	while(!feof($ftraj)) {
		$line=fgets($ftraj);
		if(strncmp($line, "ATOM  ", 6)==0) {
			$valid = $valid &&
				is_numeric (substr($line, 30, 8)) &&
				is_numeric (substr($line, 38, 8)) &&
				is_numeric (substr($line, 46, 8));
		}

	}
	fclose($ftraj);

	return $valid;
}

function readPDBCoordinates ($pdb) {
	$nElems = 0;
	$fpdb = sfopen($pdb, "r");

	$line = fgets($fpdb);
	while ($line != FALSE) {
		if (strncmp ("ATOM  ", $line, 6) == 0) {

			// Reading only the first MODEL (in case of models).
			if (strncmp ("ENDMDL", $line, 6) == 0) {
				break;
			}
			$atomName = trim(substr($line, 12, 4));
			if($atomName == "CA") {
				$xyz[$nElems]["x"] = substr ($line, 30, 8) + 0.0;
				$xyz[$nElems]["y"] = substr ($line, 38, 8) + 0.0;
				$xyz[$nElems]["z"] = substr ($line, 46, 8) + 0.0;
				$nElems++;
			}
		}
		$line = fgets($fpdb);
	}

	fclose($fpdb);

	return $xyz;
}

function readPDBCoordinatesNA ($pdb) {
	$nElems = 0;
	$fpdb = sfopen($pdb, "r");

	$line = fgets($fpdb);
	while ($line != FALSE) {
		if (strncmp ("ATOM  ", $line, 6) == 0) {

			// Reading only the first MODEL (in case of models).
			if (strncmp ("ENDMDL", $line, 6) == 0) {
				break;
			}
			$atomName = trim(substr($line, 12, 4));
			if(preg_match("/^C1[*']/",$atomName)) {
				$xyz[$nElems]["x"] = substr ($line, 30, 8) + 0.0;
				$xyz[$nElems]["y"] = substr ($line, 38, 8) + 0.0;
				$xyz[$nElems]["z"] = substr ($line, 46, 8) + 0.0;
				$nElems++;
			}
		}
		$line = fgets($fpdb);
	}

	fclose($fpdb);

	return $xyz;
}

function computeAllToAllDistances ($xyz) {
	$nElems = count ($xyz);

	// Filling the diagonal
	for ($i = 0; $i < $nElems; $i++) {
		$distances[$i][$i] = 0.0;
	}

	// Computing relevant distances
	for ($i = 0; $i < $nElems; $i++) {
		for ($j = $i + 1; $j < $nElems; $j++) {
			$xi = $xyz[$i]["x"];
			$yi = $xyz[$i]["y"];
			$zi = $xyz[$i]["z"];
			$xj = $xyz[$j]["x"];
			$yj = $xyz[$j]["y"];
			$zj = $xyz[$j]["z"];
			$dx = abs($xi - $xj);
			$dy = abs($yi - $yj);
			$dz = abs($zi - $zj);
			$distances[$i][$j] = $dx*$dx + $dy*$dy + $dz*$dz;
//			$distances[$j][$i] = $distances[$i][$j];
		}
	}

	return $distances;
}

function checkCADistances($in) {
	$xyz = readPDBCoordinates ($in);
	$distances = computeAllToAllDistances ($xyz);
	$nElems = count ($xyz);
	
	$distancesOK = true;
	$margin = 2.2;
	
	// Check for steric clashes
	for ($i = 0; $i < $nElems && $distancesOK; $i++) {
		for ($j = $i + 1; $j < $nElems && $distancesOK; $j++) {
			$distancesOK = $distancesOK && ($distances[$i][$j] > 1.0);
		}
	}

	// Check for proper distance between consecutive CA atoms
	for ($i = 0; $i < $nElems-1 && $distancesOK; $i++) {
		$j = $i+1;
		$distancesOK = $distancesOK && ($distances[$i][$j] > pow((3.8 - $margin),2)) && ($distances[$i][$j] < pow((3.8 + $margin),2));
	}

	/*
	$fdebug=@fopen("/tmp/dist.ca", "w");
	fprintf ($fdebug, "Distances for file: %s\nNumber of elements: %d\n", $in, $nElems);
	for ($i = 0; $i < $nElems-1; $i++) {
		$j = $i+1;
		fprintf($fdebug, "%d-%d %f\n", $i, $j, $distances[$i][$j]);
	}
	fwrite($fdebug, print_r($distances, true));
	fclose($fdebug);
	*/

	return $distancesOK;
}

function checkAllResiduesPresent($in) {
	$fin = sfopen($in, "r");

	$lastNum = -1;
	$numerationOK = true;
	$line = fgets($fin);
	while ($line != FALSE && $numerationOK == true) {
		#if (strncmp ("ATOM  ", $line, 6) == 0) {
			$atomName = trim(substr($line, 12, 4));
			if($atomName == "CA") {
				$resNum = substr ($line, 22, 4) + 0;
				if ($resNum == $lastNum || $resNum == $lastNum + 1 || $lastNum == -1) {
					$lastNum = $resNum;
				} else {
					$numerationOK = false;
				}
			}
		#}
		$line = fgets($fin);
	}

	fclose ($fin);

	return $numerationOK;
}

function generateMap ($pdbFile) {
	$fin = sfopen($pdbFile, "r");
	
	$map = Array();
	$i = 0;
	while (($line = fgets($fin)) != FALSE) {
		if (strncmp ("ATOM  ", $line, 6) == 0) {
			$atomName = trim(substr($line, 12, 4));
			if($atomName == "CA") {
				$chain = substr ($line, 21, 1);
				if (!$chain)
                    $chain="_";
				$resNum = trim(substr ($line, 22, 5));
				$atNum = substr ($line, 6, 5) + 0;
				$resName = trim(substr ($line, 17,4));
				$map["res"][$i]["chain"] = $chain;
				$map["res"][$i]["resNum"] = $resNum;
				$map["res"][$i]["atNum"] = $atNum;
				$map["res"][$i]["atName"] = $atomName;
                $map["res"][$i]["resName"] = $resName;
				$map["res"][$i]["resShortId"] = $chain.$resNum;
				$map["res"][$i]["resId"] = $resName." ".$chain.$resNum;
				if (!isset($map["chains"][$chain]))
					$map["chains"][$chain]= Array("minRes"=>$i, "maxRes" => $i, "numResidus" => 1);
				else {
					$map["chains"][$chain]["maxRes"] = $i;
					$map["chains"][$chain]["numResidus"]++;
				}
				$i++;
			}
		}
	}

	fclose ($fin);

	return $map;
}

function generateMapNA ($pdbFile) {
	$fin = sfopen($pdbFile, "r");
	
	$map = Array();
	$i = 0;
	while (($line = fgets($fin)) != FALSE) {
		if (strncmp ("ATOM  ", $line, 6) == 0) {
			$atomName = trim(substr($line, 12, 4));
			if(preg_match("/^C1[*']/",$atomName)) {
				$chain = substr ($line, 21, 1);
				if (!$chain)
                    $chain="_";
				$resNum = trim(substr ($line, 22, 5));
				$atNum = substr ($line, 6, 5) + 0;
				$resName = trim(substr ($line, 17,4));
				$map["res"][$i]["chain"] = $chain;
				$map["res"][$i]["resNum"] = $resNum;
				$map["res"][$i]["atNum"] = $atNum;
				$map["res"][$i]["atName"] = $atomName;
                $map["res"][$i]["resName"] = $resName;
				$map["res"][$i]["resShortId"] = $chain.$resNum;
				$map["res"][$i]["resId"] = $resName." ".$chain.$resNum;
				if (!isset($map["chains"][$chain]))
					$map["chains"][$chain]= Array("minRes"=>$i, "maxRes" => $i, "numResidus" => 1);
				else {
					$map["chains"][$chain]["maxRes"] = $i;
					$map["chains"][$chain]["numResidus"]++;
				}
				$i++;
			}
		}
	}

	fclose ($fin);

	return $map;
}

function writeMap ($map, $foutName) {
	$fout = sfopen ($foutName, "w");

	for ($i = 0; $i < count ($map["res"]); $i++) {
		fprintf ($fout, "%s %s\n", $map["res"][$i]["chain"], $map["res"][$i]["resNum"]);
	}
	
	fclose ($fout);
}

function savePDBMap ($mapFile, $map) {
	$fout = sfopen ($mapFile, "w");
 	fwrite ($fout, serialize($map));
	fclose ($fout);
}

function loadPDBMap ($mapFile) {
    $mapStr = @file_get_contents($mapFile);
	if ($mapStr == false) {
		if (debugHost()) {
			echo "<p>Cannot open file: $mapFile for reading</p>";
			exit();
		} else {
			header('Status: 302 Found');
			header("Location: error.php?idErr=22&sessId=$sessId");
			exit();
		}
	}
	
    return unserialize ($mapStr);
}

// MDWeb PDB CHECK Functions, August 2010.

function getModels($pdbFile) {
	$ftraj=sfopen($pdbFile, "r");

	while(!feof($ftraj)) {
		$line=fgets($ftraj);
		if(strncmp($line, "MODEL", 5)==0) {
			$modelNum=trim(substr($line, 12, 4));
			$modelList[$modelNum]= 1;
		}
	}

	fclose($ftraj);

	return $modelList;
}

function getChains($pdbFile) {
	//echo "PDB: ".$pdbFile;
	$ftraj=sfopen($pdbFile, "r");

	while(!feof($ftraj)) {
		$line=fgets($ftraj);
		if( (strncmp($line, "ATOM ", 5)==0) or (strncmp($line, "HETATM", 6)==0)) {
			$chain=trim(substr($line, 21, 1));
			if(!$chain) {
				$chain = "@";
			}
			$chainList["$chain"]= 1;
		}
	}

	fclose($ftraj);

	return $chainList;
}

function getAlternateLocation($pdbFile) {
	$ftraj=sfopen($pdbFile, "r");

	while(!feof($ftraj)) {
		$line=fgets($ftraj);
		if( (strncmp($line, "ATOM ", 5)==0) or (strncmp($line, "HETATM", 6)==0)) {
			$chain=trim(substr($line, 21, 1));
			if(!$chain) {
				$chain = "@";
			}
			$resName = trim(substr ($line, 17,4));
			$nres=trim(substr($line, 22, 4));
			$atom=trim(substr($line, 6, 5));
			$altLoc=trim(substr($line, 16, 1));
		
			if($altLoc){
				$altLocList["$nres$chain"]["$altLoc"] = $resName;
			}
		}
	}

	fclose($ftraj);

	return $altLocList;
}

function getDNAChains($pdbFile) {
	$ftraj=sfopen($pdbFile, "r");
	$DNAChainList="";
	while(!feof($ftraj)) {
		$line=fgets($ftraj);
		if( (strncmp($line, "ATOM ", 5)==0) or (strncmp($line, "HETATM", 6)==0)) {
			$chain=trim(substr($line, 21, 1));
			if(!$chain) {
				$chain = "@";
			}
			$resName = trim(substr ($line, 17,4));
			if($resName == "DA" or $resName == "DT" or $resName == "DC" or $resName == "DG" or $resName == "RA" or $resName == "RU" or $resName == "RC" or $resName == "RG" or $resName == "A" or $resName == "T" or $resName == "C" or $resName == "G"){
				$DNAChainList[$chain] = 1;
			}
		}
	}

	fclose($ftraj);

	return $DNAChainList;
}

function getInsertionCode($pdbFile) {
	$ftraj=sfopen($pdbFile, "r");

	while(!feof($ftraj)) {
		$line=fgets($ftraj);
		if( (strncmp($line, "ATOM ", 5)==0) or (strncmp($line, "HETATM", 6)==0)) {
			$chain=trim(substr($line, 21, 1));
			if(!$chain) {
				$chain = "@";
			}
			$resName = trim(substr ($line, 17,4));
			$nres=trim(substr($line, 22, 4));
			$insCode=trim(substr($line, 26, 1));
		
			if($insCode){
				$insCodeList["$nres$chain"]["$insCode"] = $resName;
			}
		}
	}

	fclose($ftraj);

	return $insCodeList;
}

function getLigands($pdbFile) {
    $ftraj=sfopen($pdbFile, "r");

    $libdir = LIBLIG;
    $lib = opendir($libdir);
    while($filename = readdir($lib)) { 
        if(strncmp(substr($filename,-4),".lib",4) == 0){
            $lig = substr($filename,0,-4);
            $libDirList["$lig"] = 1;
        }
    }
    // Ions
    $libDirList["Cl-"] = 1;

    // Water Codes
    $wat["HOH"] = 1;
    $wat["WAT"] = 1;

    // Heavy Metals
    $hm["MG"] = 1;	$hm["MN"] = 1;	$hm["MO"] = 1;
    $hm["Mg2"]= 1;	$hm["Mn2"]= 1;  $hm["Ca2"] = 1;
    $hm["ZN"] = 1;	$hm["NI"] = 1;	$hm["FE"] = 1;
    $hm["Zn2"]= 1;	$hm["Ni2"]= 1;	$hm["Fe2"] = 1;
    $hm["CO"] = 1;	$hm["CU"] = 1;	$hm["HG"] = 1;
    $hm["Co2"]= 1;	$hm["Cu2"]= 1;
    $hm["CD"] = 1;	$hm["AG"] = 1;	$hm["AU"] = 1;
    $hm["Cd2"]= 1;

    while(!feof($ftraj)) {
        $line=fgets($ftraj);
        if ( strncmp($line, "HETATM", 6)==0 ) {
            $res=trim(substr($line, 17, 3));
            if($wat["$res"]) continue;
            if($libDirList["$res"]){
                $ligandList["$res"] = 1;
            }
            else{
                $ligandList["$res"] = 2;
            }
        }
    }

    fclose($ftraj);
    return $ligandList;
}

function replaceCharacters($pdbFile) {
    $fin=sfopen($pdbFile, "r");
    $fout=sfopen($pdbFile."replacing", "w");
    while(!feof($fin)) {
        $line=fgets($fin);
        $newLine= str_replace("'","*",$line);
/*	
	if(preg_match("/'/",$line)){
		echo "line: ".$line;
		echo "<br />";
		echo "newLine: ".$newLine;
		echo "<br />";
	}
*/
        fwrite($fout, $newLine);
    }
    fclose($fin);
    fclose($fout);
    rename($pdbFile."replacing", $pdbFile);
}

function changeLigandNames($pdbFile) {
    // Water Codes
    $wat["HOH"] = 1;
    $wat["WAT"] = 1;
    
    // MonoAtomic Ligands
    $ma["Ca2"] = 1;     $ma["Cd2"] = 1;
    $ma["Mg2"]= 1;  	$ma["Mn2"]= 1;
    $ma["Zn2"]= 1;      $ma["Ni2"]= 1;
    $ma["Fe2"] = 1;
    $ma["Co2"]= 1;      $ma["Cu2"]= 1;
    $ma["Cs+"] = 1;     $ma["K+"] = 1;  
    $ma["Li+"] = 1;     $ma["Na+"]= 1;
    $ma["Br-"] = 1;		$ma["Cl-"] = 1;

    $ftraj=sfopen($pdbFile, "r+");
    while(!feof($ftraj)) {
        $line=fgets($ftraj);
        //Replacing quotes ' for asterisks *
        $line= str_replace("'","*",$line);
        //$line= str_replace("-","_",$line);
        if ( strncmp($line, "HETATM", 6)==0 ) {
            $res=trim(substr($line, 17, 3));
            if($wat["$res"]){ 
            	continue;
            }
            //if($hm["$res"]) continue;
            if(getLigandName($res) != null){
            	$ligandName=getLigandName($res);
				$monoat = $ma[$ligandName];
                $ligandList[$ligandName] = 1;
                for ($i = strlen($ligandName); $i < 3; $i++) {
                	$ligandName=$ligandName." ";
                }
                fseek($ftraj, -strlen($line), SEEK_CUR);
		if($monoat){
	                $line= substr($line, 0, 12)." ".$ligandName." ".$ligandName.substr($line, 20, strlen($line)-1);
		}
		else{
	                $line= substr($line, 0, 17).$ligandName.substr($line, 20, strlen($line)-1);
		}
                fwrite($ftraj,$line);
            }
        }
        if ( strncmp($line, "ATOM  ", 6)==0 ) {
        	fseek($ftraj, -strlen($line), SEEK_CUR);
        	//logger("linia: ".$line);
        	fwrite($ftraj,$line);
        }
    }
    fclose($ftraj);
}

function getMonoAtomics($pdbFile) {
    $ftraj=sfopen($pdbFile, "r");

    // Water Codes
    $wat["HOH"] = 1;
    $wat["WAT"] = 1;

    // MonoAtomic Ligands
    $ma["Ca2"] = 1;     $ma["Cd2"] = 1;
    $ma["Mg2"]= 1;      $ma["Mn2"]= 1;
    $ma["Zn2"]= 1;      $ma["Ni2"]= 1;
    $ma["Fe2"] = 1;
    $ma["Co2"]= 1;      $ma["Cu2"]= 1;
    $ma["Cs+"] = 1;     $ma["K+"] = 1;
    $ma["Li+"] = 1;     $ma["Na+"]= 1;
    $ma["Br-"] = 1;     $ma["Cl-"] = 1;

    while(!feof($ftraj)) {
        $line=fgets($ftraj);
        if ( strncmp($line, "HETATM", 6)==0 ) {
            $res=trim(substr($line, 17, 3));
            if($wat["$res"]) continue;
            if($ma["$res"]){
                $list["$res"] = 1;
            }
        }
    }

    fclose($ftraj);

    return $list;
}

function getHeavyMetals($pdbFile) {
    $ftraj=sfopen($pdbFile, "r");

    // Water Codes
    $wat["HOH"] = 1;
    $wat["WAT"] = 1;

    // Heavy Metals
    $hm["MG"] = 1;	$hm["MN"] = 1;	$hm["MO"] = 1;
    $hm["Mg2"]= 1;	$hm["Mn2"]= 1;  $hm["Ca2"] = 1;
    $hm["ZN"] = 1;	$hm["NI"] = 1;	$hm["FE"] = 1;
    $hm["Zn2"]= 1;	$hm["Ni2"]= 1;	$hm["Fe2"] = 1;
    $hm["CO"] = 1;	$hm["CU"] = 1;	$hm["HG"] = 1;
    $hm["Co2"]= 1;	$hm["Cu2"]= 1;
    $hm["CD"] = 1;	$hm["AG"] = 1;	$hm["AU"] = 1;
    $hm["Cd2"]= 1;

    while(!feof($ftraj)) {
        $line=fgets($ftraj);
        if ( strncmp($line, "HETATM", 6)==0 ) {
        	$atom=trim(substr($line, 12, 3));
            $res=trim(substr($line, 17, 3));
            if($wat["$res"]) continue;
            if($hm["$res"]){
                $heavyMetalList["$res"] = $atom;           
            }
        }
    }

    fclose($ftraj);

    return $heavyMetalList;
}

function checkCADistances2($in) {
	$xyz = readPDBCoordinates ($in);
	$distances = computeAllToAllDistances ($xyz);
	$nElems = count ($xyz);
	$map = generateMap($in);

//	$margin = 2.2;
	$margin = 1.0;
	$caDist = 3.8;
	$stericClashDist = 1.0;
	
	// Check for steric clashes
	for ($i = 0; $i < $nElems; $i++) {
		for ($j = $i + 1; $j < $nElems; $j++) {

			$d = $distances[$i][$j];
			
			if($d < $stericClashDist){  // Steric Clash Dist ^ 2 = Steric Clash Dist = 1

				$resNum1 	= $map["res"][$i]["resNum"];
				$resName1 	= $map["res"][$i]["resName"];
				$atNum1 	= $map["res"][$i]["atNum"];
				$chain1 	= $map["res"][$i]["chain"];
				$resNum2 	= $map["res"][$j]["resNum"];
				$resName2 	= $map["res"][$j]["resName"];
				$atNum2 	= $map["res"][$j]["atNum"];
				$chain2 	= $map["res"][$j]["chain"];
				if ($resNum1 != $resNum2){
					$d2 = sqrt($d);
					$distList["CA-$atNum1-$resName1$resNum1-$chain1 : CA-$atNum2-$resName2$resNum2-$chain2, Dist: $d2"] = 1;
				}
			}
		}
	}

	// Check for proper distance between consecutive CA atoms
	for ($i = 0; $i < $nElems-1; $i++) {
		$j = $i+1;

		$d = $distances[$i][$j];

		if ( ($d < pow(($caDist - $margin),2)) || ($d > pow(($caDist + $margin),2)) ){
			$resNum1 = $map["res"][$i]["resNum"];
			$resName1 = $map["res"][$i]["resName"];
			$atNum1 = $map["res"][$i]["atNum"];
			$chain1 = $map["res"][$i]["chain"];
			$resNum2 = $map["res"][$j]["resNum"];
			$resName2 = $map["res"][$j]["resName"];
			$atNum2 = $map["res"][$j]["atNum"];
			$chain2 = $map["res"][$j]["chain"];

			if($chain1 == $chain2 && $resNum1 != $resNum2){
				$d2 = sqrt($d);
				$distList["CA-$atNum1-$resName1$resNum1-$chain1 : CA-$atNum2-$resName2$resNum2-$chain2, Dist: $d2"] = 2;	
			}
		}
	}
	
	return $distList;
}

function checkC1Distances($in) {
	$xyz = readPDBCoordinatesNA ($in);
	$distances = computeAllToAllDistances ($xyz);
	$nElems = count ($xyz);
	$map = generateMapNA($in);

	$margin = 1.0;
	$c1Dist = 5.5;
	
	// Check for proper distance between consecutive C1 atoms (Nucleic Acids Bases)
	for ($i = 0; $i < $nElems-1; $i++) {
		$j = $i+1;

		$d = $distances[$i][$j];
		$patata = sqrt($d);
		$r1 =  pow(($c1Dist - $margin),2);
		$r2 =  pow(($c1Dist + $margin),2);
		if ( ($d < $r1) || ($d > $r2) ){
logger("Checking: if ( ($d < $r1) || ($d > $r2) )");
			$resNum1 = $map["res"][$i]["resNum"];
			$resName1 = $map["res"][$i]["resName"];
			$atNum1 = $map["res"][$i]["atNum"];
			$chain1 = $map["res"][$i]["chain"];
			$resNum2 = $map["res"][$j]["resNum"];
			$resName2 = $map["res"][$j]["resName"];
			$atNum2 = $map["res"][$j]["atNum"];
			$chain2 = $map["res"][$j]["chain"];

			if($chain1 == $chain2 && $resNum1 != $resNum2){
				$d2 = sqrt($d);
				$distList["C1-$atNum1-$resName1-$resNum1-$chain1 : C1-$atNum2-$resName2-$resNum2-$chain2, Dist: $d2"] = 2;	
			}
		}
	}
	
	return $distList;
}

function checkAllResiduesPresent2($in) {
	$fin = sfopen($in, "r");

	$map = generateMap($in);

	$lastNum = -1;
	$i = 0;
	$line = fgets($fin);
	while ($line != FALSE) {
		// Reading only the first MODEL (in case of models).
		if (strncmp ("ENDMDL", $line, 6) == 0) {
			break;
		}
		if (strncmp ("ATOM  ", $line, 6) == 0) {
			$atomName = trim(substr($line, 12, 4));
			if($atomName == "CA") {
				$resNum = substr ($line, 22, 4) + 0;
				if (!($resNum == $lastNum || $resNum == $lastNum + 1 || $lastNum == -1)){
					$resNum1 = $map["res"][$i-1]["resNum"];
					$resName1 = $map["res"][$i-1]["resName"];
					$atNum1 = $map["res"][$i-1]["atNum"];
					$chain1 = $map["res"][$i-1]["chain"];
					$resNum2 = $map["res"][$i]["resNum"];
					$resName2 = $map["res"][$i]["resName"];
					$atNum2 = $map["res"][$i]["atNum"];
					$chain2 = $map["res"][$i]["chain"];

					if($chain1 == $chain2){
						$consResList["CA-$atNum1-$resName1$resNum1-$chain1 : CA-$atNum2-$resName2$resNum2-$chain2"] = 1;
					}
				}
				$lastNum = $resNum;
				$i++;
			}
		}
		$line = fgets($fin);
	}

	fclose ($fin);

	return $consResList;
}

function checkSSbonds($in) {
	$fin = sfopen($in, "r");

	$ss_cutoff = 2.5;
	$i = 0;
	$line = fgets($fin);
	while ($line != FALSE) {

		// Reading only the first MODEL (in case of models).
		if (strncmp ("ENDMDL", $line, 6) == 0) {
			break;
		}

		if(preg_match('/ATOM +\d+  SG  CYS/',$line)){
			#ATOM   1752  SG  CYS A 231      10.734  57.929  57.743  1.00 14.59           S 
			$i++;
			$sg[$i]{chain} = trim(substr($line, 21, 1));
			if(!$sg[$i]{chain}){
				$sg[$i]{chain} = "@";
			}
			$sg[$i]{resnum} = trim(substr($line, 22, 5));
			$sg[$i]{xcoor} =  substr($line, 31, 8);
			$sg[$i]{ycoor} =  substr($line, 39, 8);
			$sg[$i]{zcoor} =  substr($line, 47, 8);
			$sg[$i]{resname} = trim(substr ($line, 17,4));
		}

		$line = fgets($fin);
	}

	fclose ($fin);

	for ($i=1; $i<=count($sg); $i++){
		for ($j=$i+1; $j<=count($sg); $j++){

			$a = $sg[$i]{xcoor} - $sg[$j]{xcoor};
			$b = $sg[$i]{ycoor} - $sg[$j]{ycoor};
			$c = $sg[$i]{zcoor} - $sg[$j]{zcoor};

			$n = ($a*$a + $b*$b + $c*$c);

			$betr = sqrt($n);
			$a = $sg[$i]{chain};
			$b = $sg[$i]{resnum};
			$c = $sg[$j]{chain};
			$d = $sg[$j]{resnum};
			if ($betr < $ss_cutoff){
				$ssBonds["$a:$b:$c:$d"] = $sg[$i]{resname}.":".$sg[$j]{resname}.":".$betr;
			}
		}
	}

	return $ssBonds;
}

function checkDistances($in) {
	$xyz = readCoordinates ($in);
	$distances = computeAllToAllDistances ($xyz);
	$nElems = count ($xyz);
	$map = generateMap($in);

	$stericClashDist = 1.0;

	// Check for steric clashes
	for ($i = 0; $i < $nElems; $i++) {
		for ($j = $i + 1; $j < $nElems; $j++) {

			$d = $distances[$i][$j];
			
			if($d < $stericClashDist){ // Steric Clash Dist ^ 2 = Steric Clash Dist = 1

				$resNum1 = $map["res"][$i]["resNum"];
				$resName1 = $map["res"][$i]["resName"];
				$atNum1 = $map["res"][$i]["atNum"];
				$atName1 = $map["res"][$i]["atName"];
				$chain1 = $map["res"][$i]["chain"];
				$resNum2 = $map["res"][$j]["resNum"];
				$resName2 = $map["res"][$j]["resName"];
				$atNum2 = $map["res"][$j]["atNum"];
				$atName1 = $map["res"][$j]["atName"];
				$chain2 = $map["res"][$j]["chain"];

				$d2 = sqrt($d);
				$distList["$atName1-$atNum1-$resName1$resNum1-$chain1 : $atName2-$atNum2-$resName2$resNum2-$chain2, Dist: $d2"] = 1;
			}
		}
	}

	return $distList;
}

function fixPDB($in,$out,$model,$chains,$altLocs,$insCodes,$ligands) {

	$fin = sfopen($in, "r");
	$fout = sfopen($out, "w");

	$m = 1;

	$line = fgets($fin);
	while ($line != FALSE) {

		// Reading only the MODEL $model (in case of models).
		if (strncmp ("MODEL ", $line, 6) == 0) {
			$m = trim(substr($line,6,8));
		}
		// Next if not Correct Model.
		if ($model != $m){
			$line = fgets($fin);
			continue;
		}

		// Next if Ligand not Found in MoDEL Library.
		if (strncmp ("HETATM", $line, 6) == 0) {

			// Getting Residue and Atom info.
			$res=trim(substr($line, 17, 3));

			if (! $ligands[$res] ){
				$line = fgets($fin);
				continue;
			}
		}
		if (strncmp ("ATOM  ", $line, 6) == 0) {

			// Getting Chain Id.
			$chain=trim(substr($line, 21, 1));
			if(!$chain) {
				$chain = "@";
			}

			// Getting Residue and Atom info.
			$res=trim(substr($line, 17, 3));
			$nres=trim(substr($line, 22, 4));
			$atom=trim(substr($line, 6, 5));
			$altLoc=trim(substr($line, 16, 1));
			$insCode=trim(substr($line, 26, 1));

			$altLocCode = "$nres$chain-$altLoc";
			$insCodeCode = "$nres$chain-$insCode";
		
			// Next if not Correct Chain(s)
			if(!$chains[$chain]){
				$line = fgets($fin);
				continue;
			}
			// Next if not Correct Insertion Code(s)
			if( $insCode and !$insCodes[$insCodeCode] ) {
				$line = fgets($fin);
				continue;
			}
			// Next if not Correct Alternate Location Code(s)
			if( $altCode and !$altLocs[$altLocCode] ) {
				$line = fgets($fin);
				continue;
			}
		}
		// print out line
		$ok = fwrite($fout,$line);

		$line = fgets($fin);

	}

	fclose ($fin);
	fclose ($fout);

}

function removeHydrogens($in,$out){
	$fin = sfopen($in, "r");
	$fout = sfopen($out, "w");

	$line = fgets($fin);
	while ($line != FALSE) {

		if(preg_match('/ATOM|HETATM/',$line)){

			$at = trim(substr($line,12,4));
			if( !preg_match('/^H/',$at) and !preg_match('/^\dH/',$at)){
				// print out line
				$ok = fwrite($fout,$line);				
			}
		}
		else{
			// print out line
			$ok = fwrite($fout,$line);
		}

		$line = fgets($fin);
	}

	fclose ($fin);
	fclose ($fout);

	return;
}

function getAngles($pdbFile){
	//echo "<br>".$pdbFile."<br>";
	$angles= array();
	$anglesTHR= array();
	$anglesCYS= array();
	$dir = $GLOBALS['scriptDir'];
    $outFile = "$pdbFile.angles";
    $cmd = "perl $dir/torsionAngles.pl $pdbFile > $outFile";
    $cat = exec($cmd);
    
    $fin = sfopen($outFile, "r");
    $line = fgets($fin);
    $cont=0;
    while ($line != FALSE) {
    	$line = trim($line);
    	$gap= split("[ ]+", $line);
        $anglesMap[$cont]['residueNumber']= $gap[0]; 
        $anglesMap[$cont]['residueName']= $gap[1]; 
        $anglesMap[$cont]['residueChain']= $gap[2];
        $anglesMap[$cont]['chirality']= trim($gap[sizeof($gap)-1]);
        $anglesMap[$cont]['omega']= $gap[5];
        $line=fgets($fin);
        $cont++;
    }
    foreach($anglesMap as $resNres => $arRes){
    	if ((strcasecmp($arRes['residueName'], "THR") == 0) and (strcasecmp($arRes['chirality'], "CHIRALITY.") == 0)){
        	$nres1=$nres1.$chain;
			$anglesTHR[]=	"Improper Chirality: ".$arRes['residueName']."-".$arRes['residueNumber'].$arRes['residueChain'].":".$arRes['residueName']."-".$arRes['residueNumber'].$arRes['residueChain']." :<br>";
        	
        }
     	if ((strcasecmp($arRes['residueName'], "GLY") != 0) and (strcasecmp($arRes['residueName'], "PRO") != 0) and ($arRes['omega'] > -45) and ($arRes['omega'] < 45)){
     		$nextResNum= $anglesMap[$resNres+1]['residueNumber'];
     		$nextResName= $anglesMap[$resNres+1]['residueName'];
     		$nextResChain= $anglesMap[$resNres+1]['residueChain'];
        	if((strcmp(trim($nextResChain), trim($arRes['residueChain']))==0) || ($nextResNum == ($arRes['residueNumber']+1))  ){
				$anglesCYS[]=	"Improper Chirality: ".$arRes['residueName']."-".$arRes['residueNumber'].$arRes['residueChain'].":".$nextResName."-".$nextResNum.$nextResChain." :<br>";
        	}
        }
    	
    } 

    $angles["THR"]=$anglesTHR;
    $angles["CYS"]=$anglesCYS;
    return $angles;
}


function getClashes($pdbFile) {
    
    $arrayClashes=            array();
    $arraySteric=             array();
    $arrayApolar=             array();
    $arrayPolarDonor=         array();
    $arrayPolarAcceptor=     array();
    $arrayIonicPositive=     array();
    $arrayIonicNegative=     array();
    $chiralityProblem=         array();
    $arrayChiralityProblem=    array();
    

    # Atom Types DAT.
    #
    # Categories:
    # 1 = Apolar
    # 2 = Polar Donor
    # 3 = Polar Donor, Ionic Positive
    # 4 = Polar Acceptor
    # 5 = Polar Acceptor, Ionic Negative
    # 6 = Polar Mixt
    
    $apolar["1"] = 1;
    $donor["2"] = 1;
    $donor["3"] = 1;
    $ionicP["3"] = 1;
    $ionicN["5"] = 1;
    $acceptor["4"] = 1;
    $acceptor["5"] = 1;
    $mixt["6"] = 1;

    // Reading Atom Types: atomTypes.dat
    $fin = sfopen("phplib/atomTypes.dat", "r");
    $line = fgets($fin);
    while ($line != FALSE) {
        # GLU-2HB
        if(preg_match('[^#]',$line)){
            $line = fgets($fin);
            continue;
        }
        list ($code,$category) = preg_split('[:]',$line);
        $attypes[$code] = $category;
        $line = fgets($fin);
    }
    fclose ($fin);

    foreach ($attypes as $k => $v) {
#        echo "Atom: $k, Category: $v <br>";
    }

#     "Looking for AllAtoms to AllAtoms distances... <br>";
    $maxDist = 4.5;
    $stericClashDist = 1.0;
#    $stericApolarClashDist = 4.5;
#    $stericPolarClashDist = 3.5;
#    $ionicClashDist = 3.5;
    $stericApolarClashDist = 2.9;
    $stericPolarClashDist = 3.1;
    $ionicClashDist = 3.5;
    
    $dir = "fortran";
    $outFile = "$pdbFile.juntesMax";
    $cmd = "$dir/juntes2 $pdbFile $maxDist >& $outFile";
    $cat = exec($cmd);
   
  # Arrays $done remove multiple clashes for a given residue pair. Warning: they don't keep the atom clash with minimum distance, just the first one!!
    $doneSteric = array();
    $doneApolar = array();
    $donePolarD = array();
    $donePolarA = array();
    $doneIonicP = array();
    $doneIonicN = array();

    $fin = sfopen($outFile, "r");
    $line = fgets($fin);
    while ($line != FALSE) {
        if (strncmp ("Dist:", $line, 5) == 0) {
            // OLD: //Dist:    2.413  Atoms:  706 CA  GLY   85A -   709 N  LEU   86A

	    // NEW: //Dist:    3.794  Atoms:    4 O   GLY    1D L -    25 OD1 ASP    1A L
	    // NEW: //Dist:    4.562  Atoms:    3 C   GLY    1D L -    27 N   CYS    1  L
            $array = preg_split('[\s+]',$line);
            $dist = $array[1];

            $nat1 = $array[3];
            $at1 = $array[4];
            $res1 = $array[5];
            $oldnres1 = $array[6];
	    $chain1 = $array[7];
	    $numRes1 = $oldnres1;
	    // Insertion Code?
	    $resInsCode1 = "";
	    if(preg_match("/[A-Za-z]$/",$oldnres1)){
		$numRes1 = substr($oldnres1, 0, -1);
		$resInsCode1 = substr($oldnres1,-1);
	    }

            $elem1 = substr($at1, 0, 1);
	    $nres1 = "$oldnres1$chain1";

            $nat2 = $array[9];
            $at2 = $array[10];
            $res2 = $array[11];
            $oldnres2 = $array[12];
	    $chain2 = $array[13];
            $numRes2 = $oldnres2;
            // Insertion Code?
	    $resInsCode2 = "";
            if(preg_match("/[A-Za-z]$/",$oldnres2)){
                $numRes2 = substr($oldnres2, 0, -1);
	    	$resInsCode2 = substr($oldnres2,-1);
	    }

            $elem2 = substr($at2, 0, 1);
	    $nres2 = "$oldnres2$chain2";

            # GLU-2HB
            $code1 = "$res1-$at1";
            $code2 = "$res2-$at2";

            $cat1 = $attypes[$code1] + 0;
            $cat2 = $attypes[$code2] + 0;
            if ( (($numRes2 == $numRes1+1) or ($numRes1 == $numRes2+1)) and ($chain1 == $chain2) ){
            # Contiguous Residues of chain $chain1: $numRes1, $numRes2. Continuing...<br>";
                $line = fgets($fin);
                continue;
            }

            if ( ($numRes2 == $numRes1) and ($chain1 == $chain2) ){
		if ( ($resInsCode1 == chr(ord($resInsCode2) + 1) or $resInsCode2 == chr(ord($resInsCode1) + 1) ) or ($resInsCode1 == "" and $resInsCode2 == "A") or ($resInsCode2 == "" and $resInsCode1 == "A") ){
	            	# Contiguous Residues of chain $chain1 (Residue Insertion Code CASE!): $numRes1, $resInsCode1 - $resInsCode2. Continuing...<br>";
        	        $line = fgets($fin);
                	continue;
		}
            }


            if( $dist < $stericClashDist){
                // Steric Clash: ALL vs ALL
                if (!$doneSteric["$res1-$nres1:$res2-$nres2"]){
                    $arraySteric[]= "Steric Clash: $at1-$nat1-$res1-$nres1 : $at2-$nat2-$res2-$nres2 : $dist <br>";
		$doneSteric["$res1-$nres1:$res2-$nres2"] = 1;
                }
            }

            if( ($apolar[$cat1] or $apolar[$cat2]) and $dist < $stericApolarClashDist){
                // Apolar Steric Clash: Apolar Atom vs ALL
                if (!$doneApolar["$res1-$nres1:$res2-$nres2"]){
                    $arrayApolar[]= "Steric Clash (Apolar-*): $at1-$nat1-$res1-$nres1 : $at2-$nat2-$res2-$nres2 : $dist <br>";
		$doneApolar["$res1-$nres1:$res2-$nres2"] = 1;
                }
            }
            else if( ($donor[$cat1] and $donor[$cat2]) and $dist < $stericPolarClashDist){
                // Polar Donor Steric Clash: Polar Donor Atom vs Polar Donor Atom
                if (!$donePolarD["$res1-$nres1:$res2-$nres2"]){
                    	$arrayPolarDonor[]= "Steric Clash(PolarDonor-PolarDonor): $at1-$nat1-$res1-$nres1 : $at2-$nat2-$res2-$nres2 : $dist <br>";
			$donePolarD["$res1-$nres1:$res2-$nres2"] = 1;
                
	                 // ASN/GLN improper amide assignment: Amide nitrogen with polar donor steric clash.
	                 // Chirality array has ASN/GLN residue with improper amide assignment at first position ALWAYS.
	                 if ( ($res1 == 'GLN' and $at1 == 'NE2') or ($res1 == 'ASN' and $at1 == 'ND2') ) { 
	                    $chiralityProblem["$res1-$nres1:$res2-$nres2"] = 1;
	                 }
			 else if ( ($res2 == 'GLN' and $at2 == 'NE2') or ($res2 == 'ASN' and $at2 == 'ND2') ){
	                    $chiralityProblem["$res2-$nres2:$res1-$nres1"] = 1;
	 		 }
		}
	    }
            else if( ($acceptor[$cat1] and $acceptor[$cat2]) and $dist < $stericPolarClashDist){
                	// Polar Acceptor Steric Clash: Polar Acceptor Atom vs Polar Acceptor Atom
                	if (!$donePolarA["$res1-$nres1:$res2-$nres2"]){
                   		$arrayPolarAcceptor[]= "Steric Clash(PolarAcceptor-PolarAcceptor): $at1-$nat1-$res1-$nres1 : $at2-$nat2-$res2-$nres2 : $dist <br>";
				$donePolarA["$res1-$nres1:$res2-$nres2"] = 1;
                
				 // ASN/GLN improper amide assignment: Amide oxigen with polar acceptor steric clash.
				 // Chirality array has ASN/GLN residue with improper amide assignment at first position ALWAYS.
		                 if ( ($res1 == 'GLN' and $at1 == 'OE1') or ($res1 == 'ASN' and $at1 == 'OD1') ){
		                    $chiralityProblem["$res1-$nres1:$res2-$nres2"] = 1;
		                 }
				 else if ( ($res2 == 'GLN' and $at2 == 'OE1') or ($res2 == 'ASN' and $at2 == 'OD1') ){
		                    $chiralityProblem["$res2-$nres2:$res1-$nres1"] = 1;
				 }
			}
            }
            else if( ($ionicP[$cat1] and $ionicP[$cat2]) and $dist < $ionicClashDist){
                // Ionic Steric Clash: Ionic Positive Atom vs Ionic Positive Atom
                if (!$doneIonicP["$res1-$nres1:$res2-$nres2"]){
                    $arrayIonicPositive[]= "Steric Clash(IonicPositive-IonicPositive): $at1-$nat1-$res1-$nres1 : $at2-$nat2-$res2-$nres2 : $dist <br>";
		$doneIonicP["$res1-$nres1:$res2-$nres2"] = 1;
                }
            }
            else if( ($ionicN[$cat1] and $ionicN[$cat2]) and $dist < $ionicClashDist){
                // Ionic Steric Clash: Ionic Negative Atom vs Ionic Negative Atom
                if (!$doneIonicN["$res1-$nres1:$res2-$nres2"]){
                    $arrayIonicNegative[]= "Steric Clash(IonicNegative-IonicNegative): $at1-$nat1-$res1-$nres1 : $at2-$nat2-$res2-$nres2 : $dist <br>";
		$doneIonicN["$res1-$nres1:$res2-$nres2"] = 1;
                }
            }
            else{
#                "NO STERIC CLASH!! $cat1 - $cat2donor[$cat1]=$donor[$cat1], donor[$cat2]=$donor[$cat2]||$at1-$nat1-$res1-$nres1 : $at2-$nat2-$res2-$nres2, Dist: $dist <br>";
            }
        }
        $line = fgets($fin);
    }
    fclose ($fin);
    $arrayClashes["steric"]=$arraySteric;
    $arrayClashes["apolar"]=$arrayApolar;
    $arrayClashes["polarDonor"]=$arrayPolarDonor;
    $arrayClashes["polarAcceptor"]=$arrayPolarAcceptor;
    $arrayClashes["ionicPositive"]=$arrayIonicPositive;
    $arrayClashes["ionicNegative"]=$arrayIonicNegative;

    
    $arrayClashes["chirality"]=$chiralityProblem;

    return $arrayClashes;
}

function getLigandName($ligand) {
	$libraryLigands = array();

	$libraryLigands[strtoupper("1DA")]="1DA";
	$libraryLigands[strtoupper("2PH")]="2PH";
	$libraryLigands[strtoupper("4HA")]="4HA";
	$libraryLigands[strtoupper("998")]="998";
	$libraryLigands[strtoupper("A80")]="A80";
	$libraryLigands[strtoupper("ABC")]="ABC";
	$libraryLigands[strtoupper("ACM")]="ACM";
	$libraryLigands[strtoupper("ACN")]="ACN";
	$libraryLigands[strtoupper("ACT")]="ACT";
	$libraryLigands[strtoupper("ACY")]="ACY";
	$libraryLigands[strtoupper("ADE")]="ADE";
	$libraryLigands[strtoupper("ADN")]="ADN";
	$libraryLigands[strtoupper("ADP")]="ADP";
	$libraryLigands[strtoupper("AHA")]="AHA";
	$libraryLigands[strtoupper("arg")]="arg";
	$libraryLigands[strtoupper("ARN")]="ARN";
	$libraryLigands[strtoupper("ATP")]="ATP";
	$libraryLigands[strtoupper("AZI")]="AZI";
	$libraryLigands[strtoupper("B18")]="B18";
	$libraryLigands[strtoupper("BDN")]="BDN";
	$libraryLigands[strtoupper("BGL")]="BGL";
	$libraryLigands[strtoupper("BHP")]="BHP";
	$libraryLigands[strtoupper("BME")]="BME";
	$libraryLigands[strtoupper("BML")]="BML";
	$libraryLigands[strtoupper("BNZ")]="BNZ";
	$libraryLigands[strtoupper("BOG")]="BOG";
	$libraryLigands[strtoupper("Br-")]="Br-";
	$libraryLigands[strtoupper("BR")]="Br-";
	$libraryLigands[strtoupper("BU1")]="BU1";
	$libraryLigands[strtoupper("CA")]="Ca2";
	$libraryLigands[strtoupper("Ca2")]="Ca2";
	$libraryLigands[strtoupper("CBI")]="CBI";
	$libraryLigands[strtoupper("Cd2")]="Cd2";
	$libraryLigands[strtoupper("Cd")]="Cd2";
	$libraryLigands[strtoupper("CHX")]="CHX";
	$libraryLigands[strtoupper("CIT")]="CIT";
	$libraryLigands[strtoupper("Cl")]="Cl-";
	$libraryLigands[strtoupper("CMO")]="CMO";
	$libraryLigands[strtoupper("Co2")]="Co2";
	$libraryLigands[strtoupper("Co")]="Co2";
	$libraryLigands[strtoupper("CO3")]="CO3";
	$libraryLigands[strtoupper("COT")]="COT";
	$libraryLigands[strtoupper("Cu2")]="Cu2";
	$libraryLigands[strtoupper("Cu")]="Cu2";
	$libraryLigands[strtoupper("CYH")]="CYH";
	$libraryLigands[strtoupper("CYN")]="CYN";
	$libraryLigands[strtoupper("Cs")]="Cs+";
	$libraryLigands[strtoupper("DDQ")]="DDQ";
	$libraryLigands[strtoupper("DGG")]="DGG";
	$libraryLigands[strtoupper("DIO")]="DIO";
	$libraryLigands[strtoupper("DMF")]="DMF";
	$libraryLigands[strtoupper("DMS")]="DMS";
	$libraryLigands[strtoupper("DNC")]="DNC";
	$libraryLigands[strtoupper("DOX")]="DOX";
	$libraryLigands[strtoupper("DTT")]="DTT";
	$libraryLigands[strtoupper("EDO")]="EDO";
	$libraryLigands[strtoupper("EHN")]="EHN";
	$libraryLigands[strtoupper("EOH")]="EOH";
	$libraryLigands[strtoupper("EPE")]="EPE";
	$libraryLigands[strtoupper("F10")]="F10";
	$libraryLigands[strtoupper("Fe2")]="Fe2";
	$libraryLigands[strtoupper("Fe")]="Fe2";
	$libraryLigands[strtoupper("FMN")]="FMN";
	$libraryLigands[strtoupper("GDP")]="GDP";
	$libraryLigands[strtoupper("GLA")]="GLA";
	$libraryLigands[strtoupper("GLB")]="GLB";
	$libraryLigands[strtoupper("gly")]="gly";
	$libraryLigands[strtoupper("GOL")]="GOL";
	$libraryLigands[strtoupper("HEM")]="HEM";
	$libraryLigands[strtoupper("IDA")]="IDA";
	$libraryLigands[strtoupper("IMD")]="IMD";
	$libraryLigands[strtoupper("IND")]="IND";
	$libraryLigands[strtoupper("INT")]="INT";
	$libraryLigands[strtoupper("IOL")]="IOL";
	$libraryLigands[strtoupper("IPA")]="IPA";
	$libraryLigands[strtoupper("IPH")]="IPH";
	$libraryLigands[strtoupper("KDG")]="KDG";
	$libraryLigands[strtoupper("K+")]="K+";
	$libraryLigands[strtoupper("K")]="K+";
	$libraryLigands[strtoupper("LDA")]="LDA";
	$libraryLigands[strtoupper("Li+")]="Li+";
	$libraryLigands[strtoupper("LYN")]="LYN";
	$libraryLigands[strtoupper("lys")]="lys";
	$libraryLigands[strtoupper("MAN")]="MAN";
	$libraryLigands[strtoupper("MEE")]="MEE";
	$libraryLigands[strtoupper("MES")]="MES";
	$libraryLigands[strtoupper("Mg2")]="Mg2";
	$libraryLigands[strtoupper("Mg")]="Mg2";
	$libraryLigands[strtoupper("Mn2")]="Mn2";
	$libraryLigands[strtoupper("Mn")]="Mn2";
	$libraryLigands[strtoupper("MOH")]="MOH";
	$libraryLigands[strtoupper("MPD")]="MPD";
	$libraryLigands[strtoupper("MR9")]="MR9";
	$libraryLigands[strtoupper("MSM")]="MSM";
	$libraryLigands[strtoupper("MTX")]="MTX";
	$libraryLigands[strtoupper("NAD")]="NAD";
	$libraryLigands[strtoupper("Na")]="Na+";
	$libraryLigands[strtoupper("Na+")]="Na+";
	$libraryLigands[strtoupper("NEO")]="NEO";
	$libraryLigands[strtoupper("NH3")]="NH3";
	$libraryLigands[strtoupper("NH4")]="NH4";
	$libraryLigands[strtoupper("Ni2")]="Ni2";
	$libraryLigands[strtoupper("Ni")]="Ni2";
	$libraryLigands[strtoupper("NO3")]="NO3";
	$libraryLigands[strtoupper("OAA")]="OAA";
	$libraryLigands[strtoupper("OXL")]="OXL";
	$libraryLigands[strtoupper("OXY")]="OXY";
	$libraryLigands[strtoupper("PBP")]="PBP";
	$libraryLigands[strtoupper("PEG")]="PEG";
	$libraryLigands[strtoupper("PGA")]="PGA";
	$libraryLigands[strtoupper("PLM")]="PLM";
	$libraryLigands[strtoupper("PO2")]="PO2";
	$libraryLigands[strtoupper("POP")]="POP";
	$libraryLigands[strtoupper("PP7")]="PP7";
	$libraryLigands[strtoupper("PPI")]="PPI";
	$libraryLigands[strtoupper("PYD")]="PYD";
	$libraryLigands[strtoupper("PYR")]="PYR";
	$libraryLigands[strtoupper("RIP")]="RIP";
	$libraryLigands[strtoupper("RRT")]="RRT";
	$libraryLigands[strtoupper("RTL")]="RTL";
	$libraryLigands[strtoupper("SAM")]="SAM";
	$libraryLigands[strtoupper("SB2")]="SB2";
	$libraryLigands[strtoupper("SBI")]="SBI";
	$libraryLigands[strtoupper("SIN")]="SIN";
	$libraryLigands[strtoupper("SKD")]="SKD";
	$libraryLigands[strtoupper("SO4")]="SO4";
	$libraryLigands[strtoupper("SOA")]="SOA";
	$libraryLigands[strtoupper("SUL")]="SUL";
	$libraryLigands[strtoupper("TAR")]="TAR";
	$libraryLigands[strtoupper("TCD")]="TCD";
	$libraryLigands[strtoupper("THM")]="THM";
	$libraryLigands[strtoupper("TNL")]="TNL";
	$libraryLigands[strtoupper("TRS")]="TRS";
	$libraryLigands[strtoupper("TYM")]="TYM";
	$libraryLigands[strtoupper("URA")]="URA";
	$libraryLigands[strtoupper("URE")]="URE";
	$libraryLigands[strtoupper("V25")]="V25";
	$libraryLigands[strtoupper("WAT")]="WAT";
	$libraryLigands[strtoupper("Zn2")]="Zn2";
	$libraryLigands[strtoupper("Zn")]="Zn2";


//	$libraryLigands[strtoupper("2HA")]="2HA";
//	$libraryLigands[strtoupper("2MD")]="2MD";
//	$libraryLigands[strtoupper("3PG")]="3PG";
//	$libraryLigands[strtoupper("4IP")]="4IP";
//	$libraryLigands[strtoupper("ACH")]="ACH";
//	$libraryLigands[strtoupper("ACP")]="ACP";
//	$libraryLigands[strtoupper("AMP")]="AMP";
//	$libraryLigands[strtoupper("ANP")]="ANP";
//	$libraryLigands[strtoupper("AP5")]="AP5";
//	$libraryLigands[strtoupper("BIS")]="BIS";
//	$libraryLigands[strtoupper("CAG")]="CAG";
//	$libraryLigands[strtoupper("CAM")]="CAM";
//	$libraryLigands[strtoupper("COA")]="COA";
//	$libraryLigands[strtoupper("DCF")]="DCF";
//	$libraryLigands[strtoupper("DPM")]="DPM";
//	$libraryLigands[strtoupper("E64")]="E64";
//	$libraryLigands[strtoupper("EPU")]="EPU";
//	$libraryLigands[strtoupper("EST")]="EST";
//	$libraryLigands[strtoupper("FAD")]="FAD";
//	$libraryLigands[strtoupper("FAR")]="FAR";
//	$libraryLigands[strtoupper("G16")]="G16";
//	$libraryLigands[strtoupper("GCP")]="GCP";
//	$libraryLigands[strtoupper("GMY")]="GMY";
//	$libraryLigands[strtoupper("GP3")]="GP3";
//	$libraryLigands[strtoupper("GSH")]="GSH";
//	$libraryLigands[strtoupper("GSP")]="GSP";
//	$libraryLigands[strtoupper("GTP")]="GTP";
//	$libraryLigands[strtoupper("HAP")]="HAP";
//	$libraryLigands[strtoupper("HAX")]="HAX";
//	$libraryLigands[strtoupper("IGP")]="IGP";
//	$libraryLigands[strtoupper("ISP")]="ISP";
//	$libraryLigands[strtoupper("KTP")]="KTP";
//	$libraryLigands[strtoupper("M1A")]="M1A";
//	$libraryLigands[strtoupper("NAP")]="NAP";
//	$libraryLigands[strtoupper("NDC")]="NDC";
//	$libraryLigands[strtoupper("NDP")]="NDP";
//	$libraryLigands[strtoupper("NOV")]="NOV";
//	$libraryLigands[strtoupper("P6G")]="P6G";
//	$libraryLigands[strtoupper("PA5")]="PA5";
//	$libraryLigands[strtoupper("PLP")]="PLP";
//	$libraryLigands[strtoupper("PO4")]="PO4";
//	$libraryLigands[strtoupper("PPD")]="PPD";
//	$libraryLigands[strtoupper("PTX")]="PTX";
//	$libraryLigands[strtoupper("RAP")]="RAP";
//	$libraryLigands[strtoupper("REA")]="REA";
//	$libraryLigands[strtoupper("RIF")]="RIF";
//	$libraryLigands[strtoupper("SAH")]="SAH";
//	$libraryLigands[strtoupper("SEO")]="SEO";
//	$libraryLigands[strtoupper("STU")]="STU";
//	$libraryLigands[strtoupper("TET")]="TET";
//	$libraryLigands[strtoupper("THA")]="THA";
//	$libraryLigands[strtoupper("TMP")]="TMP";
//	$libraryLigands[strtoupper("U5P")]="U5P";
//	$libraryLigands[strtoupper("UPG")]="UPG";
//	$libraryLigands[strtoupper("Y3")]="Y3";

	return $libraryLigands[strtoupper($ligand)];
}


?>
