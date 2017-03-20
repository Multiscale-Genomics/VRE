<?php
# MDWeb
# nucleicAcidAnalysis.php
#
require "phplib/globals.inc.php";

# Printing Headers
//print headerNA("NAFlex. Nucleic Acids Flexibility",0);

include 'header.php';

?>

<script type="text/javascript" src="js/sequenceSelection.js"></script>
<script type="text/javascript" src="js/NA_Checks.js"></script>
<script type="text/javascript" src="js/sortable.js"></script>

	<!-- JQuery -->
        <!--<script type="text/javascript" src="NUCMD/js/jquery-1.11.0.min.js"></script>-->

	<!-- JSMol -->
	<!--<script type="text/javascript" src="NUCMD/jsmol/JSmol.min.nojq.js"></script>

	<!-- JSMol auxiliar Scripts --
	<script type="text/javascript" src="NUCMD/js/jmolScripts.js"></script>

	<!-- Image Preview --
	<script type="text/javascript" src="NUCMD/js/imagePreview.js"></script>

	<!-- Image/Video visualization with jQuery --
	<script type="text/javascript" src="NUCMD/js/jqueryImages/jqueryImages.js"></script>

	<script type="text/javascript">

		$(document).ready( function() {

			imagePreview();
		});
	</script>-->
<?php 

if ($_REQUEST['proj'])
    $proj = $_REQUEST['proj'];

if ($_REQUEST['type'])
	$analysisType = $_REQUEST['type'];
else
	$analysisType = "CURVES";

$project = $_REQUEST[proj];
$op = $_REQUEST[proj];
$nuc = $_REQUEST[nuc];
$analysis = $analysisType;

//$dir = $GLOBALS['webDir'].$GLOBALS['parmbsc1Dir'];
$dir = '/orozco/services/parmbsc1_naflex_Data/NAFlex_parmBSC1/';

#$userPath = "userData/$proj/$analysis";

$path = "$dir/$proj/$analysis";

$userPath = $GLOBALS['parmbsc1Dir']."/$proj/$analysis";

if($analysis == "STACKING_2"){
	$path = "$dir/$proj/STACKING";
	$userPath = $GLOBALS['parmbsc1Dir']."/$proj/STACKING";
}

if($analysis == "EXPvsMD"){
	$path = "$dir/$proj/CURVES";
	$userPathCurves = $GLOBALS['parmbsc1Dir']."/$proj/CURVES";
	$userPathCurvesEXP = $GLOBALS['parmbsc1Dir']."/${proj}_EXP/CURVES";
}

if($analysis == "PDB"){
	$path = "$dir/$proj/PDB/CURVES";
	$userPath = $GLOBALS['parmbsc1Dir']."/$proj/PDB/CURVES";
}

# Checking Parameters
if (!$project ){
	print "<i><b>Oooops, operation not found! Please, check input user, project and operation...</b></i><br/><br/>";
	print "<b>Project</b>: $project<br/>";
	print footerNA();
	exit;
}

#$curvesFile = "$path/${op}_curvesOut.lis";
$curvesFile = "$path/seq.info";

$canalFile = "$path/${op}_canalOut.lis";
$shiftxFile = "$path/shift_avg.out";

logger("Path: $path, Analysis Type: $analysisType, Proj: $proj");

if(!is_dir($path)){
	print "<i><b>Oooops, path $path not found! Please, check input user, project and operation...</b></i><br/><br/>";
	print footerNA();
	exit;
}

chdir($path);

switch($analysisType){

	case 'CURVES':
		logger("NucleicAcidAnalysis, case Curves.");
	#print "Into Path: $path<br/><br/>";
?>
	<h2 align="center"> Nucleic Acid Analysis - Helical Parameters <br/>
	<i>(Using Curves+ and Canal programs)</i></h2>

<?php

	include "phplib/curves.inc.php";

	break;

	case 'PCAZIP':
		logger("NucleicAcidAnalysis, case Pcazip.");

		if($_REQUEST['evec'])
			$evec = $_REQUEST['evec'];
		else
			$evec = 1;
		$plot = "$userPath/${proj}_pcazipOut.proj$evec.plot.dat.png";
		$dat = "$userPath/${proj}_pcazipOut.proj$evec.plot.dat";
		$anim = "$userPath/${proj}_pcazipOut.anim$evec.pdb";

?>
	<h2 align="center"> Nucleic Acid Analysis - Principal Component Analysis </h2>
	<h2 align="center"> <i>(Using Pcazip program)</i></h2>

<?php
	include "phplib/pcazip.inc2.php"; 
	#include "phplib/pcazip.incJMol.php"; 

	break;

	case 'STIFFNESS':
		logger("NucleicAcidAnalysis, case Stiffness.");
?>
	<h2 align="center"> Nucleic Acid Analysis - Stiffness Analysis </h2>
	<h2 align="center"> <i>(Using Curves+ program)</i></h2>

<?php
	include "phplib/stiffness2.inc.php"; 

	break;

	case 'NMR_JC':
		logger("NucleicAcidAnalysis, case NMR_Jcouplings.");

		# If NMR analysis, we don't have Curves output, so we generate a new file with similar info called seqs.info.
		#$curvesFile = "$path/seq.info";
		$curvesFile = "seq.info";

		$cmd = "cwd";
		$out = exec($cmd);
		logger("CWD: $out");

		$cmd = "grep 'NucType:' $curvesFile";
		$out = exec($cmd,$nucleicType);

		$p = preg_split ("/:/",$nucleicType[0]);
		$naType = $p[sizeof($p)-1];
		$naType = preg_replace('/ /','',$naType);
		logger("Nucleic Type: -$naType-");

?>
	<h2 align="center"> Nucleic Acid Analysis - NMR Analysis </h2>
	<h2 align="center"> <i>J-Couplings</i></h2>

<?php

	if ($naType == 'DNA')
		include "phplib/nmr_J_DNA.inc2.php"; 
	if ($naType == 'RNA')
		include "phplib/nmr_J_RNA.inc2.php"; 

	break;

	case 'NMR_NOE':
		logger("NucleicAcidAnalysis, case NMR_NOEs.");

		# If NMR analysis, we don't have Curves output, so we generate a new file with similar info called seqs.info.
		$curvesFile = "$path/seq.info";

		$cmd = "grep 'NucType:' $curvesFile";
		$out = exec($cmd,$nucleicType);

		$p = preg_split ("/:/",$nucleicType[0]);
		$naType = $p[sizeof($p)-1];
		$naType = preg_replace('/ /','',$naType);
		logger("Nucleic Type: -$naType-");

?>
	<h2 align="center"> Nucleic Acid Analysis - NMR Analysis </h2>
	<h2 align="center"> <i>NOEs</i></h2>

<?php

	if ($naType == 'DNA')
		include "phplib/nmr_NOEs_DNA.inc.php"; 
	if ($naType == 'RNA')
		include "phplib/nmr_NOEs_RNA.inc.php"; 

	break;

	case 'HBs':
		logger("NucleicAcidAnalysis, case HBs.");

		# If NMR analysis, we don't have Curves output, so we generate a new file with similar info called seqs.info.
		$curvesFile = "$path/seq.info";

		$cmd = "grep 'NucType:' $curvesFile";
		$out = exec($cmd,$nucleicType);

		$p = preg_split ("/:/",$nucleicType[0]);
		$naType = $p[sizeof($p)-1];
		$naType = preg_replace('/ /','',$naType);
		logger("Nucleic Type: -$naType-");

?>
	<h2 align="center"> Nucleic Acid Analysis - Hydrogen Bond Analysis </h2>
	<h2 align="center"> <i>Canonical Hydrogen Bond Distances</i></h2>

<?php

	include "phplib/HBs.inc.php"; 

	#if ($naType == 'DNA')
	#	include "phplib/HBs_DNA.inc.php"; 
	#if ($naType == 'RNA')
	#	include "phplib/HBs_RNA.inc.php"; 

	break;

	case 'STACKING':
		logger("NucleicAcidAnalysis, case Stacking.");

?>
	<h2 align="center"> Nucleic Acid Analysis - HB/Stacking Analysis</h2>
	<h2 align="center"> <i>HB/Stacking Energies</i></h2>

<?php
	include "phplib/Stacking.inc.php"; 

	break;

	case 'STACKING_2':	# Non-duplex structures
		logger("NucleicAcidAnalysis, case Stacking for unusual structures.");

?>
	<h2 align="center"> Nucleic Acid Analysis - HB/Stacking Analysis</h2>
	<h2 align="center"> <i>HB/Stacking Energies</i></h2>

<?php
	include "phplib/StackingNoDuplex.inc.php"; 

	break;

	case 'CONTACTS':
		logger("NucleicAcidAnalysis, case DistanceContactMaps.");

?>
	<h2 align="center"> Nucleic Acid Analysis - Distance Analysis</h2>
	<h2 align="center"> <i>Distance Contact Maps</i></h2>

<?php
	include "phplib/distContactMaps.inc.php";

	break;

	case 'PDB':
		logger("NucleicAcidAnalysis, case PDB.");

?>
	<h2 align="center"> Nucleic Acid Analysis - Experimental PDB Analysis</h2>
	<h2 align="center"> <i>Experimental PDB Curves Analysis</i></h2>

<?php
	include "phplib/curves.inc.php"; 

	break;

	case 'EXPvsMD':
		logger("NucleicAcidAnalysis, case Exp vs MD.");

?>
	<h2 align="center"> Nucleic Acid Analysis - Experimental vs MD Analysis</h2>
	<h2 align="center"> <i>Helical Base Pair Steps</i></h2>

<?php
	include "phplib/expVsMD.inc.php"; 

	break;
}

//print footerNA();
include "footer.php";


function getCollectivity($op,$mode) {

$file = $op."_pcazipOut.collectivity";
$collectivity=readVector($file);

return $collectivity[$mode-1];
}

function getEigenvalue($op,$mode) {

$file = $op."_pcazipOut.evals";
$eigenvalues=readVector($file);

return $eigenvalues[$mode-1];
}

function readVector($fName) {
$i=0;
$fbfactor=fopen($fName, "r");
while (fscanf($fbfactor, "%f", $value)) {
$array[$i]=$value;
$i++;
}
fclose($fbfactor);
return $array;
}

function plotAVG ($userPath,$plotname){

	# Added line for NUCMD development, we changed .avg to _avg to maintain 
	# coherence with other analysis stored in our Mongo DB. 
	if (preg_match('/NOE/',$userPath)){
		$plotname = preg_replace ('/(.*)\.avg/','${1}_avg',$plotname);
	}

        $plot = "$userPath/$plotname.dat.png";
        $dat = "$userPath/$plotname.dat";
        $file = "$GLOBALS[webDir]/$plot";
	logger("PLOT: $file");
        if( file_exists($file) && filesize($file) != 0){

                ?>
                <table border="0"><tr><td>
                <img border="1" src="<?php echo $plot ?>">
                </td><td>
                <p align="right" class="curvesDatText" onClick='window.open("<?php echo $GLOBALS['homeURL'].'/'.$plot ?>","<?php echo $plotname ?>","_blank,resize=1,width=800,height=600");'>Open in New Window</p><br/>
                <a href="getFile.php?fileloc=<?php echo $dat ?>&type=curves"> <p align="right" class="curvesDatText">Download Raw Data</p></a>
                </td></tr></table>

                <?php
        } else {
                echo "<p><b><i>Sorry, information not available...</i></b></p>";
        }
}

function plotExpVsMD ($userPath,$plotname){

	$pairs = array("AA","AC","AG","AT","CC","CA","CG","CT","GG","GA","GC","GT","TT","TA","TC","TG");

	?>
      	<table border="0"><tr>
	<?php

	$count = 0;
	foreach ($pairs as $pair) {

	        $plot = "$userPath/$pair.$plotname.ExpVsMD.png";
		$plotnameLC = strtolower($plotname);
        	$dat = "$userPath/$pair.$plotnameLC.tgz";
	        $file = "$GLOBALS[webDir]/$plot";
		logger("PLOT: $file");
	        if( file_exists($file) && filesize($file) != 0){
			if($count % 5 == 0) {
				echo "</tr><tr>";
			}
        	        ?>
			<td>
			<p><strong><?php echo $pair ?></strong></p></br>
			<a href="<?php echo $plot ?>" class="preview jqueryImages" title><img border="1" width="190px" src="<?php echo $plot ?>"></a>
	                <!--<img border="1" width="190px" src="<?php echo $plot ?>">-->
			<br/>
	                <p class="curvesDatText" onClick='window.open("<?php echo $GLOBALS['homeURL'].'/'.$plot ?>","_blank","resize=1,width=800,height=600");'>Open in New Window</p><br/>
        	        <a href="getFile.php?fileloc=<?php echo $dat ?>&type=curves"> <p class="curvesDatText">Download Raw Data</p></a>
			</td>
	                <?php
			$count++;
        	} else {
	                #echo "<p><b><i>Sorry, information not available...</i></b></p>";
        	}
	}
	?>
	</tr></table>
	<?php
}

?>

