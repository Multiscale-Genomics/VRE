<?php

require_once "phplib/constants.inc.php";

function debugHost() {
	$hostList = array ("bscwb14", "orozco11", "prttl-pau.mmb.pcb.ub.es");
	$listedHost = false;
	$hn = php_uname("n");
	foreach ($hostList as $host) {
		$listedHost = $listedHost || (strcasecmp ($hn, $host) == 0);
	}

	return $listedHost;
}

function returnError ($idErr, $extraParams="") {
	global $_MYSESSION;

	$sessId = $_MYSESSION["sessId"];
	$errStr = "Location: error.php?sessId=$sessId&idErr=$idErr";
	if ($extraParams!="") {
		$errStr .= "&$extraParams";
	}
	header('Status: 302 Found');
	header($errStr);
	
	exit();
}

function getLindemann() {
	global $_MYSESSION;
	
	$lindemann["Std"]  ["All"]      =readValue($_MYSESSION["lindemannAllFileStd"]);
	$lindemann["Std"]  ["Buried"]   =readValue($_MYSESSION["lindemannBuriedFileStd"]);
	$lindemann["Std"]  ["NotBuried"]=readValue($_MYSESSION["lindemannNotBuriedFileStd"]);
	$lindemann["Std"]  ["Helix"]    =readValue($_MYSESSION["lindemannHelixFileStd"]);
	$lindemann["Std"]  ["Strand"]   =readValue($_MYSESSION["lindemannStrandFileStd"]);
	$lindemann["Std"]  ["Coil"]     =readValue($_MYSESSION["lindemannCoilFileStd"]);
	$lindemann["Gauss"]["All"]      =readValue($_MYSESSION["lindemannAllFileGauss"]);
	$lindemann["Gauss"]["Buried"]   =readValue($_MYSESSION["lindemannBuriedFileGauss"]);
	$lindemann["Gauss"]["NotBuried"]=readValue($_MYSESSION["lindemannNotBuriedFileGauss"]);
	$lindemann["Gauss"]["Helix"]    =readValue($_MYSESSION["lindemannHelixFileGauss"]);
	$lindemann["Gauss"]["Strand"]   =readValue($_MYSESSION["lindemannStrandFileGauss"]);
	$lindemann["Gauss"]["Coil"]     =readValue($_MYSESSION["lindemannCoilFileGauss"]);

	return $lindemann;
}

function checkLindemann() {
	$lindemann=getLindemann();
	$max=max(max($lindemann["Std"]),max($lindemann["Gauss"]));
	$min=min(min($lindemann["Std"]),min($lindemann["Gauss"]));
	$ok=($max<10.0)&&($min>=0.0);

	return $ok;
}

function getCollectivity($rms, $mode) {
	global $_MYSESSION;
	
	$collectivity=readVector($_MYSESSION["collectivityFile$rms"]);

	return $collectivity[$mode-1];
}

function getEigenvalue($rms, $mode) {
	global $_MYSESSION;
	
	$eigenvalues=readVector($_MYSESSION["evalsFile$rms"]);

	return $eigenvalues[$mode-1];
}

function readValue($fName) {
	if (is_readable ($fName) && filesize($fName)!=0) {
		$fin=sfopen($fName, "r");
		fscanf($fin, "%f", $value);
		fclose($fin);
	} else {
		$value = 0.0;
	}

	return $value;
}

function readVector($fName) {
	$i=0;
    $fbfactor=sfopen($fName, "r");
    while (fscanf($fbfactor, "%f", $value)) {
        $array[$i]=$value;
        $i++;
    }
    fclose($fbfactor);
	return $array;
}

function generateImage($pdbFile, $pngOutput) {
	$fpdb=$pdbFile;
	$fout=tempnam(".", "tga");
	
	$vmdScript="mol load pdb $fpdb
display projection orthographic
color Display Background white
axes location off
display nearclip set 0
display height 4
lappend auto_path soft/vmd/la1.0
lappend auto_path soft/vmd/orient
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
mol representation newCartoon 0.2 20 5
mol addrep top
mol color colorid 4
mol material BrushedMetal
mol selection (within 5 of resname \\\"CY\\.\\\") and (resname \\\"CY\\.\\\")  and mass > 2
mol representation Licorice 0.3 20 20
mol addrep top
render TachyonInternal $fout
quit";

	$vmdScriptFile=tempnam(".", "vmdScript");
	$fvmd=sfopen($vmdScriptFile, "w");
	fwrite($fvmd, $vmdScript);
	fclose($fvmd);

	exec(VMDBIN." -dispdev text -size 300 300 -e $vmdScriptFile");
	exec("tgatoppm $fout | pnmcrop -white | pnmtopng -transparent white > $pngOutput");

	unlink($vmdScriptFile);
	unlink($fout);
}

function randomString($randStringLength) {
	$timestring = microtime();
	$secondsSinceEpoch=(integer) substr($timestring, strrpos($timestring, " "), 100);
	$microseconds=(double) $timestring;
	$seed = mt_rand(0,1000000000) + 10000000 * $microseconds + $secondsSinceEpoch;
	mt_srand($seed);
	$randstring = "";
	for($i=0; $i < $randStringLength; $i++) {
		$randstring .= mt_rand(0, 9);
		$randstring .= chr(ord('A') + mt_rand(0, 5));
	}
	return($randstring);
}

function capcalera($title) {
	return capcaleraEstils ($title, array(), 0);
};

function capcaleraEstils($title, $estils = array(), $refresh = 0, $base = "", $mathml = false, $margins = true) {
	if (dirname ($_SERVER[PHP_SELF]) != APPLOCATION) {
		$parents = substr_count (substr (dirname($_SERVER[PHP_SELF]), strlen(APPLOCATION)), "/");
		$bt = str_repeat ("../", $parents);
	}
	
	$header =  "<?xml version=\"1.0\" ?>\n";
	if ($mathml) {
		header("Vary: Accept");
		header("Content-Type: application/xhtml+xml");
		$header =  $header."<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1 plus MathML 2.0//EN\" \"http://www.w3.org/TR/MathML2/dtd/xhtml-math11-f.dtd\" [
	<!ENTITY mathml \"http://www.w3.org/1998/Math/MathML\">
]>\n";
	} else {
		$header =  $header."<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
	}
	$header =  $header."<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\">
<head>
	<title>$title</title>\n";

	if ($base != "") {
		$header = $header."	<base href=\"$base\" />\n";
	}

	$header = $header."	<meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\" />\n";

	if ($refresh > 0) {
		$header = $header . "<meta http-equiv=\"Refresh\" content=\"$refresh\" />";
	}

	foreach ($estils as $estil) {
		$header = $header."	<link rel=\"stylesheet\" type=\"text/css\" href=\"$estil\" />\n";
	}

	if ($margins == false) {
		$backgroundimg = "nobackgroundimg ";
	} else {
		$backgroundimg = "";
	}
	$header = $header."
	<script type=\"text/javascript\" src=\"${bt}js/input.js\"></script>
	<script type=\"text/javascript\" src=\"${bt}js/sprintf.js\"></script>
	<script type=\"text/javascript\" src=\"${bt}js/utility.js\"></script>
	<script type=\"text/javascript\" src=\"${bt}js/popupdiv.js\"></script>
	<script type=\"text/javascript\" src=\"${bt}js/popup.js\"></script>
	<script type=\"text/javascript\" src=\"${bt}js/help.js\"></script>
	<script type=\"text/javascript\" src=\"${bt}js/tableAlternator.js\"></script>
	<script type=\"text/javascript\" src=\"${bt}js/externalLinks.js\"></script>
</head>
<body>
	<div class='${backgroundimg}divbase'>
	<p class='novertivalmargins' style='text-align:center;'><img alt=\"Cap&ccedil;alera\" src=\"".APPLOCATION."/img/bannerweb.jpg\" width=\"658\" height=\"114\" /></p>
";

	return $header;

};

function capcaleraRefresh($title, $refresh) {
	return capcaleraEstils ($title, array(), $refresh);
};

function peu() {
return "
	<table id='Tabla_01' width='535' border='0' cellpadding='0' cellspacing='0'>
		<tr>
			<td ><a href='http://www.irbbarcelona.org'><img src='".APPLOCATION."/img/peuMMB_01.gif' width='89' height='97' alt='IRB Barcelona' /></a></td>
			<td ><a href='http://www.ub.edu'><img src='".APPLOCATION."/img/peuMMB_02.gif' width='156' height='97' alt='Universitat de Barcelona' /></a></td>
			<td ><a href='http://www.bsc.es/'><img src='".APPLOCATION."/img/peuMMB_03.gif' width='174' height='97' alt='Barcelona Supercomputing Center' /></a></td>
			<td ><a href='http://www.inab.org'><img src='".APPLOCATION."/img/peuMMB_04.gif' width='116' height='97' alt='Instituto Nacional de Bioinform&aacute;tica' /></a></td>
		</tr>
	</table>
	</div>

	<!-- Google Analytics code -->
	<script type='text/javascript'>
		var gaJsHost = (('https:' == document.location.protocol) ? 'https://ssl.' : 'http://www.');
		document.write(unescape(\"%3Cscript src='\" + gaJsHost + \"google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E\"));
	</script>
	<script type='text/javascript'>
		try {
			var pageTracker = _gat._getTracker('UA-10893080-1');
			pageTracker._trackPageview();
		} catch(err) {}</script>
	<!-- ##################### -->

</body>
</html>";
}

function capcaleraAjuda($title, $estils = array(), $refresh = 0, $base = "", $mathml = false) {
	$header =  "<?xml version=\"1.0\" ?>\n";
	if ($mathml) {
		header("Vary: Accept");
		header("Content-Type: application/xhtml+xml");
		$header =  $header."<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1 plus MathML 2.0//EN\" \"http://www.w3.org/TR/MathML2/dtd/xhtml-math11-f.dtd\" [
	<!ENTITY mathml \"http://www.w3.org/1998/Math/MathML\">
]>\n";
	} else {
		$header =  $header."<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
	}
	$header =  $header."<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\">
<head>
	<title>$title</title>\n";

	if ($base != "") {
		$header = $header."	<base href=\"$base\" />\n";
	}

	$header = $header."	<meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\" />\n";

	if ($refresh > 0) {
		$header = $header . "<meta http-equiv=\"Refresh\" content=\"$refresh\" />";
	}

	foreach ($estils as $estil) {
		$header = $header."	<link rel=\"stylesheet\" type=\"text/css\" href=\"$estil\" />\n";
	}

	$header = $header."	<link rel=\"stylesheet\" type=\"text/css\" href=\"css/estil.css\" />
	<script type=\"text/javascript\" src=\"js/externalLinks.js\"></script>
	<script type=\"text/javascript\" src=\"js/popup.js\"></script>
</head>
<body>
	<div id=\"divbaseAjuda\">
";

	return $header;

};

function peuAjuda() {
return '
	</div>
</body>
</html>';
}

function printParametersTable() {
	global $_MYSESSION;
	
	echo "<table class='estandard centrat'>\n<thead><tr><th>Parameter</th><th>Value</th></tr></thead>\n";
		
	if (isset ($_MYSESSION["mostSimilarProtein"])) {
		echo "<tr><th><a href='help/book.php?page=blast.php' rel='external'>Most similar protein</a></th><td><a href='http://mmb.pcb.ub.es/pdb/getStruc.php?idCode=".$_MYSESSION["mostSimilarProtein"]."' rel='external'>".$_MYSESSION["mostSimilarProtein"]."</a></td></tr>\n";
	} else {
		$pdbchain = explode ("_", $_MYSESSION["pdbCode"]);
		$pdbCode = strtoupper ($pdbchain[0]);
		echo "<tr><th><a href='help/book.php?page=glossary.php?term=PDB' rel='external'>PDB code</a></th><td><a href='http://mmb.pcb.ub.es/pdb/getStruc.php?idCode=$pdbCode' rel='external'>$pdbCode</a></td></tr>\n";
	}

	if ($_MYSESSION["isSequence"]) {
		echo "<tr><th>BLAST score</th><td>".$_MYSESSION["blastscore"]."</td></tr>\n";
		echo "<tr><th>BLAST E-value</th><td>".$_MYSESSION["blastevalue"]."</td></tr>\n";
	}
	
	if (isset($_MYSESSION["subtitle"])) {
		echo "<tr><th>Protein description</th><td>".$_MYSESSION["subtitle"]."</td></tr>\n";
	}
    	$nResidues=$_MYSESSION["lastResidueNumber"] - $_MYSESSION["firstResidueNumber"] + 1;
    	$nChains = count ($_MYSESSION["PDBmap"]["chains"]);
	echo "<tr><th>Number of chains</th><td>".$nChains." (".@join (",", @array_keys($_MYSESSION["PDBmap"]["chains"])).")</td></tr>\n";
	echo "<tr><th>Number of residues</th><td>".$nResidues." </td></tr>\n";
	echo "<tr><th>First residue</th><td>".$_MYSESSION["PDBmap"]["res"][$_MYSESSION["firstResidueNumber"]-1]["resId"]."</td></tr>\n";
	echo "<tr><th>Last residue</th><td>".$_MYSESSION["PDBmap"]["res"][$_MYSESSION["lastResidueNumber"]-1]["resId"]."</td></tr>\n";

		
	if ($_MYSESSION["analysisType"]=="NMA") {
		echo "<tr><th>Analysis type</th><td><a href=\"help/book.php?page=glossary%3Fterm%3DNMA\" rel='external' title=\"Normal Mode Analysis\">".$_MYSESSION['analysisType']."</a></td></tr>\n";
		if ($_MYSESSION["NMAAlgorithm"]=="Linear") {
			echo "<tr><th>Algorithm</th><td><a href=\"help/book.php?page=glossary%3Fterm%3DLinear\" rel='external' title=\"Standard algorithm\">".$_MYSESSION["NMAAlgorithm"]."</a></td></tr>\n";
			echo "<tr><th><a href=\"help/book.php?page=glossary%3Fterm%3DCutoff%20radius\" rel='external' title=\"Maximum distance of interacting atoms\">Cutoff radius</a></th><td>".$_MYSESSION["NMARCut"]."</td></tr>\n";
		} else {
			echo "<tr><th>Algorithm</th><td><a href=\"help/book.php?page=glossary%3Fterm%3DKovacs\" rel='external' title=\"Kovacs algorithm to avoid using a constant force for all springs\">".$_MYSESSION["NMAAlgorithm"]."</a></td></tr>\n";
		}
		echo "<tr><th><a href=\"help/book.php?page=glossary%3Fterm%3DForce%20constant\" rel='external' title=\"Force constant used\">Force constant (kcal/mol*&Aring;&sup2;)</a></th><td>".$_MYSESSION["NMAFCte"]."</td></tr>\n";
		echo "<tr><th><a href=\"help/book.php?page=glossary%3Fterm%3DRequested%20accuracy\" rel='external' title=\"Percentage of variance to conserve when calculating eigenvectors\">Requested accuracy (%)</a></th><td>".$_MYSESSION["accuracy"]."</td></tr>\n";
	}
	if ($_MYSESSION["analysisType"]=="DMD") {
		echo "<tr><th>Analysis type</th><td><a href=\"help/book.php?page=glossary%3Fterm%3DDMD\" rel='external' title=\"Discrete Molecular Dynamics\">".$_MYSESSION['analysisType']."</a></td></tr>\n";
		echo "<tr><th><a href=\"help/book.php?page=glossary%3Fterm%3DSigma\" rel='external' title=\"Well width for consecutive C&alpha;\">Sigma (&Aring;)</a></th><td>".$_MYSESSION["DMDSigma"]."</td></tr>\n";
		echo "<tr><th><a href=\"help/book.php?page=glossary%3Fterm%3DSigma%20Go\" rel='external' title=\"Well width for non-consecutive C&alpha;\">Sigma Go (&Aring;)</a></th><td>".$_MYSESSION["DMDSigmaGO"]."</td></tr>\n";
		echo "<tr><th><a href=\"help/book.php?page=glossary%3Fterm%3DCutoff%20radius\" rel='external' title=\"Maximum distance of interacting atoms\">Cutoff radius (kcal/mol*&Aring;&sup2;)</a></th><td>".$_MYSESSION["DMDRCutGO"]."</td></tr>\n";
		echo "<tr><th><a href=\"help/book.php?page=glossary%3Fterm%3DTemperature\" rel='external' title=\"Simulation temperature\">Temperature (K)</a></th><td>".$_MYSESSION["DMDTemperature"]."</td></tr>\n";
		echo "<tr><th><a href=\"help/book.php?page=glossary%3Fterm%3DRequested%20accuracy\" rel='external' title=\"Percentage of variance to conserve when calculating eigenvectors\">Requested accuracy (%)</a></th><td>".$_MYSESSION["accuracy"]."</td></tr>\n";
	}
	if ($_MYSESSION["analysisType"]=="BD") {
		echo "<tr><th>Analysis type</th><td><a href=\"help/book.php?page=glossary%3Fterm%3DBD\" rel='external' title=\"Brownian Dynamics\">".$_MYSESSION['analysisType']."</a></td></tr>\n";
		echo "<tr><th><a href=\"help/book.php?page=glossary%3Fterm%3DSteps\" rel='external' title=\"Number of simulation steps\">Steps</a></th><td>".$_MYSESSION["BDITempsMax"]."</td></tr>\n";
		echo "<tr><th><a href=\"help/book.php?page=glossary%3Fterm%3Ddt\" rel='external' title=\"Length of each time step\">&Delta;t (s)</a></th><td>".$_MYSESSION["BDDT"]."</td></tr>\n";
		echo "<tr><th><a href=\"help/book.php?page=glossary%3Fterm%3DOutput%20frequency\" rel='external' title=\"Number of steps after which a frame gets written\">Output frequency (steps)</a></th><td>".$_MYSESSION["BDITSnap"]."</td></tr>\n";
		echo "<tr><th><a href=\"help/book.php?page=glossary%3Fterm%3DForce%20constant\" rel='external' title=\"Force constant\">Force constant (kcal/mol*&Aring;&sup2;)</a></th><td>".$_MYSESSION["BDConst"]."</td></tr>\n";
		echo "<tr><th><a href=\"help/book.php?page=glossary%3Fterm%3Dr0\" rel='external' title=\"Mean distance between Ca\">Mean C-&alpha; distance (&Aring;)</a></th><td>".$_MYSESSION["BDR0"]."</td></tr>\n";
		echo "<tr><th><a href=\"help/book.php?page=glossary%3Fterm%3DRequested%20accuracy\" rel='external' title=\"Percentage of variance to conserve when calculating eigenvectors\">Requested accuracy (%)</a></th><td>".$_MYSESSION["accuracy"]."</td></tr>\n";
	}
	if ($_MYSESSION["analysisType"]=="MODEL") {
		echo "<tr><th>Analysis type</th><td><a href=\"help/book.php?page=glossary%3Fterm%3DMoDEL\" rel='external' title=\"Molecular Dynamics Extended Library\">".$_MYSESSION['analysisType']."</a></td></tr>\n";
		echo "<tr><th><a href=\"help/book.php?page=glossary%3Fterm%3DRequested%20accuracy\" rel='external' title=\"Percentage of variance to conserve when calculating eigenvectors\">Requested accuracy (%)</a></th><td>".$_MYSESSION["accuracy"]."</td></tr>\n";
	}
	if ($_MYSESSION["analysisType"]=="User") {
		echo "<tr><th>Analysis type</th><td>".$_MYSESSION['analysisType']."</td></tr>\n";
	}

	echo "</table>\n";
}

function printLinksList ($pdbc) {
	$pdbchain = explode ("_", $pdbc);
	$pdbCode = strtoupper ($pdbchain[0]);
	$pdbCodeLC = strtolower ($pdbchain[0]);
	
	echo "<p>Links to $pdbCode</p>\n";
	echo "<p>\n";
	echo "<a href='http://www.rcsb.org/pdb/explore/explore.do?structureId=$pdbCode' rel='external'>RCSB</a><br />\n";
	echo "<a href='http://www.ebi.ac.uk/pdbsum/$pdbCode' rel='external'>PDBSum</a><br />\n";
	echo "<a href='http://www.ebi.ac.uk/msd-srv/msdlite/atlas/summary/$pdbCodeLC.html' rel='external'>MSD</a><br />\n";
	echo "<a href='http://srs.ebi.ac.uk/srsbin/cgi-bin/wgetz?-newId+(([PDB:$pdbCode]))+-view+EBIMSDAstexView' rel='external'>SRS</a><br />\n";
	echo "<a href='http://www.ncbi.nlm.nih.gov/Structure/mmdb/mmdbsrv.cgi?uid=$pdbCode' rel='external'>MMDB</a><br />\n";
	echo "<a href='http://www.fli-leibniz.de/cgi-bin/ImgLib.pl?CODE=$pdbCode' rel='external'>JenaLib</a><br />\n";
	echo "<a href='http://bip.weizmann.ac.il/oca-bin/ocashort?id=$pdbCode' rel='external'>OCA</a><br />\n";
	echo "<a href='http://www.proteopedia.org/wiki/index.php/$pdbCodeLC' rel='external'>Proteopedia</a><br />\n";
	echo "<a href='http://www.cathdb.info/pdb/$pdbCodeLC' rel='external'>CATH</a><br />\n";
	echo "<a href='http://scop.mrc-lmb.cam.ac.uk/scop/pdb.cgi?disp=scop&amp;id=$pdbCode' rel='external'>SCOP</a><br />\n";
	echo "<a href='http://ekhidna.biocenter.helsinki.fi/dali/daliquery?find=$pdbCode' rel='external'>FSSP</a><br />\n";
	echo "<a href=\"http://srs.embl-heidelberg.de:8000/srs5bin/cgi-bin/wgetz?-e+[HSSP-ID:'$pdbCode']\" rel='external'>HSSP</a><br />\n";
	echo "<a href='http://pqs.ebi.ac.uk/pqs-bin/macmol.pl?filename=$pdbCode' rel='external'>PQS</a><br />\n";
	echo "<a href='http://www.ebi.ac.uk/thornton-srv/databases/cgi-bin/CSA/CSA_Site_Wrapper.pl?pdb=$pdbCode' rel='external'>CSA</a><br />\n";
	echo "<a href='http://projects.villa-bosch.de/cgi-bin/mcm/pdba.pl?id=$pdbCodeLC' rel='external'>ProSAT</a><br />\n";
	echo "<a href='http://www.cmbi.ru.nl/pdbreport/cgi-bin/nonotes?$pdbCode' rel='external'>Whatcheck</a><br />\n";
	echo "</p>\n";
}

#
#JL
#
function gapsWarning () {
	global $_MYSESSION;
	
    if (file_exists ($_MYSESSION[workDir]."/gapsWarning"))
        return "<p><span style='color:red'>Warning:</span> The simulated structure contains more than one chain or fragment, results shown may not be accurate.</p>";
    else
        return "";
}

function echoTiming ($fq, $label) {
    #JL Acumulo els valors a un arxiu per eventual optimització
    fprintf ($fq, "%s\n", "date +%s > $label");
    fprintf ($fq, "%s\n", "echo -n '$label ' >> timings");
    fprintf ($fq, "%s\n", "cat $label >> timings");
}

// Quick implementation to count the number of lines of a file
function countLines ($fin) {
	if (($content = file ($fin)) == FALSE) {
		$nLines = 0;
	} else {
		$nLines = count ($content);
	}

	return $nLines;
}

function createGNUPlotImageFile ($m) {
	$size = count ($m);
	$nombre_temp = tempnam(".", "gnuplotImage");
	$gestor = sfopen($nombre_temp, "w");
	for ($row = 0; $row < $size; $row++) {
		for ($column = 0; $column < $size; $column++) {
			if (!is_nan($m[$row][$column])) {
				fprintf ($gestor, "%d %d %f\n", $row+1, $column+1, $m[$row][$column]);
			} else {
				fprintf ($gestor, "%d %d ?\n", $row+1, $column+1);
			}
		}
	}
	fclose($gestor);

	return $nombre_temp;
}

function sfopen ($fname, $mode) {
	$gestor = @fopen($fname, $mode);
	if ($gestor == false) {
		if (debugHost()) {
			echo "<p>Cannot open file: $fname with mode $mode</p>";
			exit();
		} else {
			//header('Status: 302 Found');
			//header("Location: error.php?idErr=22&sessId=$sessId");
			exit();
		}
	}
	return $gestor;
}

?>
