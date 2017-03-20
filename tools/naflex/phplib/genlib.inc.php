<?php
#
# genlib.inc
# Utilitats generals i access BD
# Copyright 1999 Josep Ll. Gelpi G3 COM, S.L.
# versio PHP
#
# rutines auxiliars
# Base de Dades
#

function getResidues($pdbFile) {
    
    $ftraj = fopen($pdbFile, 'r');
    $resold = "0"+0;
    while(!feof($ftraj)) {
        $line=fgets($ftraj);
        if((strlen($line) > 26) && ((substr($line,0,4)=="ATOM") || (substr($line,0,5) == " ATOM") || (substr($line,0,6)=="  ATOM"))) {
            $resNum=substr($line, 22, 4) + 0;
            if ($resNum != $resold) {
                $residueList[$resNum]=substr($line, 17, 3);
                $resold = $resNum;
            }
        } 
    }
    fclose($ftraj);
    return $residueList;
}


function getSequence ($pdbFile) {

# 3 2 1
$three2one["ALA"]='A';
$three2one["ARG"]='R';
$three2one["ASN"]='N';
$three2one["ASP"]='D';
$three2one["ASX"]='B';
$three2one["CYS"]='C';
$three2one["CYX"]='C';
$three2one["GLU"]='E';
$three2one["GLN"]='Q';
$three2one["GLX"]='Z';
$three2one["GLY"]='G';
$three2one["HIS"]='H';
$three2one["HID"]='H';
$three2one["HIE"]='H';
$three2one["HIP"]='H';
$three2one["ILE"]='I';
$three2one["LEU"]='L';
$three2one["LYS"]='K';
$three2one["MET"]='M';
$three2one["PHE"]='F';
$three2one["PRO"]='P';
$three2one["SEC"]='U';
$three2one["SER"]='S';
$three2one["THR"]='T';
$three2one["TRP"]='W';
$three2one["TYR"]='Y';
$three2one["VAL"]='V';
$three2one["Xaa"]='X';

    $sequence = "";
    $residueList = getResidues ($pdbFile);
    $nResidues = count ($residueList);
    for ($residue = 1; $residue <= $nResidues; $residue++) {
        $sequence = $sequence.$three2one[$residueList[$residue]];
    }
    return $sequence;
}

function searchSimilarProtein ($sequence='', $pdbFile='') {
	if (!$sequence) {
            $sequence = getSequence ($pdbFile);
        }
	$blastr = identifyProtein ($sequence);
	$pdbCode = $blastr[0][0];
	return $pdbCode;
}

function identifyProtein ($sequence) { 
    if ($sequence == "") {
        return NULL;
    }

    $blastinput = tempnam($_SESSION['projectData']['projDir'], "blastin");
    $blastoutput = tempnam($_SESSION['projectData']['projDir'], "blastout");

    // Write the sequence to a file
    $bi = @fopen ($blastinput, "w");
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
    return $results;
}


function prepSep ($tipind) {
	if ($tipind == "T") 
		return "'";
	else 
		return "";
};

/**
 *  Send me sanitized variables please 
 */
function getRecord ($taula, $indcmp, $indval, $tipind) {
	$sepch=prepSep($tipind); 
	$sql = "SELECT * FROM $taula WHERE $indcmp = $sepch$indval$sepch";
logger("SQL: $sql");
	$result=getRecordset($sql);
	if (mysql_num_rows ($result)) 
		return mysql_fetch_array($result);
	else
		return 0;
	mysql_free_result ($result);
};

/**
 *  Send me sanitized $sql please 
 */
function execSql ($sql) {
	(mysql_query($sql)) or die ("$sql<br>".mysql_error());
};

/**
 *  Send me sanitized $sql please 
 */
function getRecordSet ($sql) {
	($result=mysql_query ($sql)) or die ($sql."<br>".mysql_error());
	return $result;
};

//CF this function is unused?
function getDicc ($taula, $idcmp, $ordcmp) {
	$sql = "SELECT * FROM $taula ";
	if ($ordcmp) 
		$sql .= "ORDER BY $ordcmp"; 
	$result=getRecordSet($sql);
	while ($row=mysql_fetch_array($result)) 
		$dicc[$row[$idcmp]]=$row["valor"];
	mysql_free_result ($result);
	return $dicc;
};

//CF this function is unused?
function putDicc (&$dicc, $taula, $idcmp, $tipval) {
	$cm=prepSep($tipval);
	while (list($k,$v) = each ($dicc)) 
		execSql ("UPDATE $taula SET valor =$cm$v$cm WHERE $idcmp = '$k'");
};

//CF this function is unused?
function update (&$f, $proc, $txtInd) {
	global $Session;
	if ($txtInd) 
		$comet="'";
	else
		$comet="";
	$rs = getRecordSet ("SELECT * FROM gravacio WHERE form = '$proc' ORDER by taula");
	if (mysql_num_rows ($rs)) {
		$rsFields=mysql_fetch_array ($rs);
		$taula = $rsFields["taula"];
		$sql = "UPDATE $taula SET ";
		$pr=1;
		do {
			if ($rsFields["taula"] != $taula) {
				$sql .= " WHERE $idt= $comet$idtv$comet ";
				execSql($sql);
				$rsFields=mysql_fetch_array($rs);
				$taula = $rsFields["taula"];
				$sql = "UPDATE $taula SET ";
				$pr=1;
			};
			if (!$pr)
				$sql .= ", ";
			$pr=0;
			$sql .= $rsFields["camp"]. "=";
			switch ($rsFields["tipus"]) {
			case "T":
				$sql .= "'".protCom($f[$rsFields["fcamp"]]) ."' ";
				break;
			case "N":
				$sql .= protNum($f[$rsFields["fcamp"]])." ";
				break;
			case "B":
				if ($f[$rsFields["fcamp"]] == "on") 
					$sql .= "1 ";
				else
					$sql .= "0 ";
			};
			$idtv = $f[$rsFields["idTaula"]];
			$idt = $rsFields["idTaula"];
		} while ($rsFields=mysql_fetch_array($rs)); 
		$sql .= " WHERE $idt = $comet$idtv$comet";
		execSql($sql);
		mysql_free_result ($rs);
	};
};

//CF this function is unused?
function getNouIndex ($taula, $indcmp, $refcmp, $ref) {
	execSql ("INSERT  INTO $taula ($refcmp) VALUES ('$ref')");
	return mysql_insert_id ();
};

//CF this function is unused?
function insRecord (&$f, $proc) {
	global $Session;
	$rs = getRecordSet ("SELECT * FROM gravacio WHERE form = '$proc' ORDER by taula");
	if (mysql_num_rows($rs)) {
		$rsFields=mysql_fetch_array($rs);
		$taula = $rsFields["taula"];
		$camps = "INSERT INTO $taula (";
		$valors = "";
		$pr=1;
		do {		
			if ($rsFields["taula"] != $taula) {
				$sql = "$camps) VALUES ($valors)";
				execSql($sql);
				$taula = $rsFields[$taula];
				$camps = "INSERT INTO $taula (";
				$valors = "";
				$pr=0;
			}
			else {	
				if (!$pr) { 
					$camps .= ", ";
					$valors .= ", ";
				};
				$pr=0;
				$camps .= $rsFields["camp"]; 
				switch ($rsFields["tipus"]) {
				case "T":
					$valors .= "'". protCom($f[$rsFields["fcamp"]])."' ";
					break;
				case "N":
					$valors .= protNum($f[$rsFields["fcamp"]])." ";
					break;
				case "B":
					if ($f[$rsFields["fcamp"]] == "on") 
						$valors .= "1 ";
					else
						$valors .= "0 ";
				};
			}
		} while ($rsFields=mysql_fetch_array($rs));
		$sql .= "$camps) VALUES ($valors)";
		execSql($sql);
		mysql_free_result($rs);
 	};
};

//CF this function is unused?
function delRecord ($taula, $indcmp, $indval, $txtInd) {
	if ($txtInd) 
		$comet="'";
	else
		$comet="";
	execSql ("DELETE FROM $taula WHERE $indcmp = $comet$indval$comet");
};
#Utilitats
function pad ($t,$n) {
	return str_pad ($t,$n,"0",STR_PAD_LEFT);
};
function elimEspais($t) {
	return (trim($t));
};
function elimCRLF ($t) {
	return str_replace ("\r\n"," ",$t);
};
function translStr ($txt,$a,$b) {
	return str_replace ($a,$b,$txt);
};
function elimStr ($txt,$a) {
	return str_replace ($a,"",$txt);
};
function endsWith($FullStr, $EndStr) {
    $StrLen = strlen($EndStr);
    $FullStrEnd = substr($FullStr, strlen($FullStr) - $StrLen);
    return $FullStrEnd == $EndStr;
}

function beginsWith($FullStr, $EndStr) {
    $StrLen = strlen($EndStr);
    $FullStrEnd = substr($FullStr, $StrLen);
    return $FullStrEnd == $EndStr;
}

function protCom ($t) {	
#protegeix cometes simples a &#146;
	return str_replace ("'","&#146;",$t);
};
function protNum($t) {
	return str_replace (",",".",$t);
};
function elimNoChar ($t) {
# elimina dels extrems 
	$a="������������������������<>()";
	while (strpos($a,substr($t,0,1)) !== false)
		$t=delStr($t,0,1);
	while (strpos($a,substr($t,strlen($t)-1,1)) !== false)
		$t=delStr($t,strlen($t)-1,1);
	return $t;
};
function noAccents ($t) {
	return translStr ($t,"������������������������","aaeeiioouucnAAEEIIOOUUCN");
};
function delStr ($a,$p1,$l) {
	return substr_replace ($a,"",$p1,$l);
};
# Dates
function avui () {
	return date("Ymd");
};
function ara () {
	return date ("His");
};
function moment () {
	return date("YmdHis");
};

function timestamp() {
    return date("d/m/y : H:i:s", time());
};

function dif_days ($date1, $date2) {
    if ($date1 < $date2) {
        $bigger = $date2;
        $smaller = $date1;
    } else if ($date1 >= $date2) {
        $bigger = $date1;
        $smaller = $date2;
    }
    $y2=substr($bigger, 0, 4);
    $y1=substr($smaller, 0, 4);
    $m2=substr($bigger, 4, 2);
    $m1=substr($smaller, 4, 2);
    $d2=substr($bigger, 6, 2);
    $d1=substr($smaller, 6, 2);
    $result = ($y2-$y1)*365 + ($m2-$m1)*30.5 + ($d2-$d1);
    return ($result);
}
function getTimestamp ($dat) {
	if (strlen($dat) == 8) 
		return mktime(0,0,0,substr($dat,4,2),substr($dat,6,2),substr($dat,0,4));
	else
		return mktime(substr($dat,8,2),substr($dat,10,2),substr($dat,12,2),substr($dat,4,2),substr($dat,6,2),substr($dat,0,4));
};
function prdata ($idi,$dat) {
	if (strlen($dat) == 8)
		return date ("d/m/Y",getTimestamp($dat));
	else
		return date("d/m/Y H:i",getTimestamp($dat));
};
function plantilla (&$f,$txt) {
	reset ($f);
	foreach ($f as $k => $v) { 
		$txt=str_replace("%$k%",$v,$txt);
	};
	return $txt;
};
function redirect ($url) {
	header ("Location:$url");
	exit;
};

function getWSIdFromName ($ws) {
    $line = getRecord("MobyLiteDB.Service","name", $ws,"T");
	return $line['idService'];
}

function getObjectIdFromName ($obj) {
    $line = getRecord("MobyLiteDB.Ontology","objectName", $obj,"T");
	return $line['objectId'];
}

function getObjectNameFromId ($id) {
    $line = getRecord("MobyLiteDB.Ontology","objectId", $id,"T");
	return $line['objectName'];
}

function getObjectParent ($obj) {
    $id = getObjectIdFromName ($obj);
    $rs = getRecordSet("SELECT * FROM MobyLiteDB.Relationship WHERE type = 'isa' and Child = '$id'");
    $isa = mysql_fetch_array($rs);
    $parent = getObjectNameFromId ($isa['Parent']);
	return $parent;
}

function getISAList ($obj) {
	$isaList = Array ($obj);
	$obj1=$obj;
    while ($pare = getObjectParent($obj1)) {
		$isaList[]=$pare;
		$obj1=$pare;
	}
	return $isaList;
}

function isa ($obj1, $obj2) {
    $isaList = getISAList($obj1);
    if (in_array($obj2, $isaList))
        return true;
    else
        return false;
}

function getObjectType ($fname) {
    $type = execScript("getObjectType.pl", $fname);
    return $type[0];
}


function getParameterDefaultValue ($WS, $param) {
    $idservice = getWSIdFromName($WS);
    $rs = getRecordSet ("SELECT * FROM MobyLiteDB.Parameter WHERE idService = '".$idservice."' AND articleName = '".$param."' AND type = 'secondary'");
    $line = mysql_fetch_array($rs);
    $value = $line['DefValue'];
    return $value;
}

function getParameterMinimumValue ($WS, $param) {
    $idservice = getWSIdFromName($WS);
    $rs = getRecordSet ("SELECT * FROM MobyLiteDB.Parameter WHERE idService = '".$idservice."' AND articleName = '".$param."' AND type = 'secondary'");
    $line = mysql_fetch_array($rs);
    $value = $line['MinValue'];
    return $value;
}

function getParameterMaximumValue ($WS, $param) {
    $idservice = getWSIdFromName($WS);
    //CF fixed SQL injection
    $idservice = mysql_real_escape_string($idservice);
    $param = mysql_real_escape_string($param);
    
    $rs = getRecordSet ("SELECT * FROM MobyLiteDB.Parameter WHERE idService = '".$idservice."' AND articleName = '".$param."' AND type = 'secondary'");
    $line = mysql_fetch_array($rs);
    $value = $line['MaxValue'];
    return $value;
}

?>
