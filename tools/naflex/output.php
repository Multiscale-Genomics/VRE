<?php

require "../../phplib/genlibraries.php";
require "phplib/globalVars.inc.php";
require "NUCMD/phplib/pdb.inc.php";


redirectOutside();

//$dirName = $_REQUEST['project'];
//$dirName = basename(getAttr_fromGSFileId($_REQUEST['project'],'path'));

//$path = '/files/'.$_SESSION['User']['id'].'/'.$dirName;

?>

<?php require "../../htmlib/header.inc.php"; ?>

<body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white page-container-bg-solid page-sidebar-fixed">
  <div class="page-wrapper">

  <?php require "../../htmlib/top.inc.php"; ?>
  <?php require "../../htmlib/menu.inc.php"; ?>


<!--<script type="text/javascript" src="js/sequenceSelection.js"></script>
<script type="text/javascript" src="js/NA_Checks.js"></script>
<script type="text/javascript" src="js/sortable.js"></script>-->

<!-- BEGIN CONTENT -->
                <div class="page-content-wrapper">
                    <!-- BEGIN CONTENT BODY -->
                    <div class="page-content">
                        <!-- BEGIN PAGE HEADER-->
                        <!-- BEGIN PAGE BAR -->
                        <div class="page-bar">
                            <ul class="page-breadcrumb">
                              <li>
                                  <a href="home/">Home</a>
                                  <i class="fa fa-circle"></i>
                              </li>
                              <li>
                                  <a href="workspace/">User Workspace</a>
                                  <i class="fa fa-circle"></i>
                              </li>
                              <li>
                                  <span>Tools</span>
                                  <i class="fa fa-circle"></i>
                              </li>
                              <li>
                                  <span>NAFlex analyses</span>
																	<i class="fa fa-circle"></i>
                              </li>
															 <li>
                                  <a href="tools/naflex/output.php?execution=<?php echo $_REQUEST['execution']; ?>">Output <?php echo basename(getAttr_fromGSFileId($_REQUEST['execution'],'path')); ?></a>
                              </li>

                            </ul>
                        </div>
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLE-->
                        <h1 class="page-title"> Results
                            <small>Nucleic Acids Flexibility Analyses</small>
                        </h1>
                        <!-- END PAGE TITLE-->
                        <!-- END PAGE HEADER-->
												<div class="row">

<input type="hidden" id="base-url" value="<?php echo $GLOBALS['BASEURL']; ?>" />


<?php 

//$data = getNUCDBData($_REQUEST['proj'], True);


if ($_REQUEST['execution'])
    $proj = $_REQUEST['execution'];

if ($_REQUEST['type'])
	$analysisType = $_REQUEST['type'];
else
	$analysisType = "";

$execution = $_REQUEST['execution'];
//$op = $_REQUEST[proj];
$op = basename(getAttr_fromGSFileId($_REQUEST['execution'],'path'));
$nuc = $_REQUEST[nuc];
$analysis = $analysisType;

//$dir = $GLOBALS['webDir'].$GLOBALS['parmbsc1Dir'];
//$dir = '/orozco/services/parmbsc1_naflex_Data/NAFlex_parmBSC1/';
$dir = $GLOBALS['dataDir'].$_SESSION['User']['id']."/".$_SESSION['User']['activeProject']."/.tmp";
$webdir = "files/".$_SESSION['User']['id']."/".$_SESSION['User']['activeProject']."/.tmp/outputs_".$execution."/";
$downdir = "../../files/".$_SESSION['User']['id']."/".$_SESSION['User']['activeProject']."/.tmp/outputs_".$execution."/";


#$userPath = "userData/$proj/$analysis";

$path = "$dir/outputs_$proj/$analysis";

//$userPath = $GLOBALS['parmbsc1Dir']."/$proj/$analysis";
$userPath = $downdir.$analysis;

if($analysis == "STACKING_2"){
	$path = "$dir/outputs_$proj/STACKING";
	$userPath = $GLOBALS['parmbsc1Dir']."/$proj/STACKING";
}

if($analysis == "EXPvsMD"){
	$path = "$dir/outputs_$proj/CURVES";
	$userPathCurves = $GLOBALS['parmbsc1Dir']."/$proj/CURVES";
	$userPathCurvesEXP = $GLOBALS['parmbsc1Dir']."/${proj}_EXP/CURVES";
}

if($analysis == "PDB"){
	$path = "$dir/outputs_$proj/CURVES";
	$userPath = $downdir."CURVES";
	#$userPath = $GLOBALS['parmbsc1Dir']."/$proj/CURVES";
}

# Checking Parameters
if (!$execution ){
	print "<i><b>Oooops, operation not found! Please, check input user, execution and operation...</b></i><br/><br/>";
	print "<b>Execution</b>: $execution<br/>";
	print footerNA();
	exit;
}

#$curvesFile = "$path/${op}_curvesOut.lis";
$curvesFile = "$path/seq.info";

$canalFile = "$path/${op}_canalOut.lis";
$shiftxFile = "$path/shift_avg.out";

logger("Path: $path, Analysis Type: $analysisType, Proj: $proj");

if(!is_dir($path)){
	print "<i><b>Oooops, path $path not found! Please, check input user, execution and operation...</b></i><br/><br/>";
	print footerNA();
	exit;
}

$oldpath = getcwd();

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

		$proj_pcazip = basename(getAttr_fromGSFileId($_REQUEST['execution'],'path'));

		$plot = "$userPath/${proj_pcazip}_pcazipOut.proj$evec.plot.dat.png";
		$dat = "$userPath/${proj_pcazip}_pcazipOut.proj$evec.plot.dat";
		$anim = "$userPath/${proj_pcazip}_pcazipOut.anim$evec.pdb";

?>
	<h2 align="center"> Nucleic Acid Analysis - Principal Component Analysis <br>
 <i>(Using Pcazip program)</i></h2>

<?php
	include "phplib/pcazip.inc2.php"; 
	#include "phplib/pcazip.incJMol.php"; 

	break;

	case 'STIFFNESS':
		logger("NucleicAcidAnalysis, case Stiffness.");
?>
	<h2 align="center"> Nucleic Acid Analysis - Stiffness Analysis <br>
 <i>(Using Curves+ program)</i></h2>

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
	<h2 align="center"> Nucleic Acid Analysis - NMR Analysis <br>
 <i>J-Couplings</i></h2>

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
	<h2 align="center"> Nucleic Acid Analysis - NMR Analysis <br> 
	<i>NOEs</i></h2>

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
	<h2 align="center"> Nucleic Acid Analysis - Hydrogen Bond Analysis <br>
	 <i>Canonical Hydrogen Bond Distances</i></h2>

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
	<h2 align="center"> Nucleic Acid Analysis - HB/Stacking Analysis <br>
	<i>HB/Stacking Energies</i></h2>

<?php
	include "phplib/Stacking.inc.php"; 

	break;

	case 'STACKING_2':	# Non-duplex structures
		logger("NucleicAcidAnalysis, case Stacking for unusual structures.");

?>
	<h2 align="center"> Nucleic Acid Analysis - HB/Stacking Analysis <br>
 <i>HB/Stacking Energies</i></h2>

<?php
	include "phplib/StackingNoDuplex.inc.php"; 

	break;

	case 'CONTACTS':
		logger("NucleicAcidAnalysis, case DistanceContactMaps.");

?>
	<h2 align="center"> Nucleic Acid Analysis - Distance Analysis <br>
 <i>Distance Contact Maps</i></h2>

<?php
	include "phplib/distContactMaps.inc.php";

	break;

	case 'PDB':
		logger("NucleicAcidAnalysis, case PDB.");

?>
	<h2 align="center"> Nucleic Acid Analysis - Experimental PDB Analysis <br>
<i>Experimental PDB Curves Analysis</i></h2>

<?php
	include "phplib/curves.inc.php"; 

	break;

	case 'EXPvsMD':
		logger("NucleicAcidAnalysis, case Exp vs MD.");

?>
	<h2 align="center"> Nucleic Acid Analysis - Experimental vs MD Analysis<br>
 <i>Helical Base Pair Steps</i></h2>

<?php
	include "phplib/expVsMD.inc.php"; 

	break;

	default: 

// init default content

?>

<?php
$checkDuplex = 0;
if (file_exists("$path/CURVES/seq.info")){
	$checkDuplex = checkDuplex("$path/CURVES/seq.info");
}

$fromPDB = false;

if (file_exists("$path/CURVES/curvespdb.mug")){
	$fromPDB = true;
}


if($fromPDB) { 

?>
	<div class="col-md-3 col-sm-4">
		<div class="thumbnail">
				<a href="tools/naflex/output.php?execution=<?php echo $execution; ?>&type=PDB">
					<div style="width: 100%; height: 200px; background:#fff url('tools/naflex/images/output/curves.png') no-repeat center center;background-size:contain; border:1px solid #e8e8e8;"></div>
				</a>
				<div class="caption">
						<h3><a href="tools/naflex/output.php?execution=<?php echo $execution; ?>&type=PDB">PDB Analysis</a></h3>
						<p>
								<a href="tools/naflex/output.php?execution=<?php echo $execution; ?>&type=PDB" class="btn green"> View Analysis </a>
						</p>
				</div>
		</div>
	</div>

<?php

}else{

	if (file_exists("$path/CURVES/helical_bp") && is_dir("$path/CURVES/helical_bp") && $checkDuplex) {

	?>
	<div class="col-md-3 col-sm-4">
		<div class="thumbnail">
				<a href="tools/naflex/output.php?execution=<?php echo $execution; ?>&type=CURVES">
					<div style="width: 100%; height: 200px; background:#fff url('tools/naflex/images/output/curves.png') no-repeat center center;background-size:contain; border:1px solid #e8e8e8;"></div>
				</a>
				<div class="caption">
						<h3><a href="tools/naflex/output.php?execution=<?php echo $execution; ?>&type=CURVES">Curves Analysis</a></h3>
						<p>
								<a href="tools/naflex/output.php?execution=<?php echo $execution; ?>&type=CURVES" class="btn green"> View Analysis </a>
						</p>
				</div>
		</div>
	</div>
	<?php 

	}

}

if (file_exists("$path/STIFFNESS/FORCE_CTES/rise_avg.dat.gnuplot")  && $checkDuplex) {

?>
<div class="col-md-3 col-sm-4">
		<div class="thumbnail">
				<a href="tools/naflex/output.php?execution=<?php echo $execution; ?>&type=STIFFNESS">
					<div style="width: 100%; height: 200px; background:#fff url('tools/naflex/images/output/stiffness.png') no-repeat center center;background-size:contain; border:1px solid #e8e8e8;"></div>
				</a>
				<div class="caption">
						<h3><a href="tools/naflex/output.php?execution=<?php echo $execution; ?>&type=STIFFNESS">Stiffness Analysis</a></h3>
						<p>
								<a href="tools/naflex/output.php?execution=<?php echo $execution; ?>&type=STIFFNESS" class="btn green"> View Analysis </a>
						</p>
				</div>
		</div>
</div>
<?php 

}

if ( is_dir("$path/PCAZIP") && file_exists("$path/PCAZIP/pcazdump.info.log") && file_exists("$path/PCAZIP/".basename(getAttr_fromGSFileId($_REQUEST['execution'],'path'))."_pcazipOut.anim1.pdb")) {

?>
<div class="col-md-3 col-sm-4">
		<div class="thumbnail">
				<a href="tools/naflex/output.php?execution=<?php echo $execution; ?>&type=PCAZIP">
					<div style="width: 100%; height: 200px; background:#fff url('tools/naflex/images/output/pcazip.gif') no-repeat center center;background-size:contain; border:1px solid #e8e8e8;"></div>
				</a>
				<div class="caption">
						<h3><a href="tools/naflex/output.php?execution=<?php echo $execution; ?>&type=PCAZIP">PCAzip Analysis</a></h3>
						<p>
								<a href="tools/naflex/output.php?execution=<?php echo $execution; ?>&type=PCAZIP" class="btn green"> View Analysis </a>
						</p>
				</div>
		</div>
</div>
<?php 

}

if (file_exists("$path/NMR_JC") && is_dir("$path/NMR_JC")) {

?>
<div class="col-md-3 col-sm-4">
		<div class="thumbnail">
				<a href="tools/naflex/output.php?execution=<?php echo $execution; ?>&type=NMR_JC">
					<div style="width: 100%; height: 200px; background:#fff url('tools/naflex/images/output/nmr_jc.png') no-repeat center center;background-size:contain; border:1px solid #e8e8e8;"></div>
				</a>
				<div class="caption">
						<h3><a href="tools/naflex/output.php?execution=<?php echo $execution; ?>&type=NMR_JC">NMR_JC Analysis</a></h3>
						<p>
								<a href="tools/naflex/output.php?execution=<?php echo $execution; ?>&type=NMR_JC" class="btn green"> View Analysis </a>
						</p>
				</div>
		</div>
</div>
<?php 

}

if (file_exists("$path/NMR_NOE") && is_dir("$path/NMR_NOE")) {

?>
<div class="col-md-3 col-sm-4">
		<div class="thumbnail">
				<a href="tools/naflex/output.php?execution=<?php echo $execution; ?>&type=NMR_NOE">
					<div style="width: 100%; height: 200px; background:#fff url('tools/naflex/images/output/nmr_noe.png') no-repeat center center;background-size:contain; border:1px solid #e8e8e8;"></div>
				</a>
				<div class="caption">
						<h3><a href="tools/naflex/output.php?execution=<?php echo $execution; ?>&type=NMR_NOE">NMR_NOEs Analysis</a></h3>
						<p>
								<a href="tools/naflex/output.php?execution=<?php echo $execution; ?>&type=NMR_NOE" class="btn green"> View Analysis </a>
						</p>
				</div>
		</div>
</div>
<?php 

}

if (file_exists("$path/HBs") && is_dir("$path/HBs") /*&& $data['rev_sequence'] != '-'*/ && (file_exists("$path/HBs/N1-H3.avg.dat.png") or file_exists("$path/HBs/O6-H41.avg.dat.png")) && $checkDuplex) {

?>
<div class="col-md-3 col-sm-4">
		<div class="thumbnail">
				<a href="tools/naflex/output.php?execution=<?php echo $execution; ?>&type=HBs">
					<div style="width: 100%; height: 200px; background:#fff url('tools/naflex/images/output/hbs.png') no-repeat center center;background-size:contain; border:1px solid #e8e8e8;"></div>
				</a>
				<div class="caption">
						<h3><a href="tools/naflex/output.php?execution=<?php echo $execution; ?>&type=HBs">HBs Analysis</a></h3>
						<p>
								<a href="tools/naflex/output.php?execution=<?php echo $execution; ?>&type=HBs" class="btn green"> View Analysis </a>
						</p>
				</div>
		</div>
</div>
<?php 

}

if (file_exists("$path/STACKING") && is_dir("$path/STACKING")/* && $data['rev_sequence'] != '-'*/ && file_exists("$path/STACKING/energies/stackingContactMapsMIN.dat.png")) {

if($checkDuplex){

?>
<div class="col-md-3 col-sm-4">
		<div class="thumbnail">
				<a href="tools/naflex/output.php?execution=<?php echo $execution; ?>&type=STACKING">
					<div style="width: 100%; height: 200px; background:#fff url('tools/naflex/images/output/stacking.png') no-repeat center center;background-size:contain; border:1px solid #e8e8e8;"></div>
				</a>
				<div class="caption">
						<h3><a href="tools/naflex/output.php?execution=<?php echo $execution; ?>&type=STACKING">Stacking Analysis</a></h3>
						<p>
								<a href="tools/naflex/output.php?execution=<?php echo $execution; ?>&type=STACKING" class="btn green"> View Analysis </a>
						</p>
				</div>
		</div>
</div>
<?php

}else{

?>
<div class="col-md-3 col-sm-4">
		<div class="thumbnail">
				<a href="tools/naflex/output.php?execution=<?php echo $execution; ?>&type=STACKING_2">
					<div style="width: 100%; height: 200px; background:#fff url('tools/naflex/images/output/stacking.png') no-repeat center center;background-size:contain; border:1px solid #e8e8e8;"></div>
				</a>
				<div class="caption">
						<h3><a href="tools/naflex/output.php?execution=<?php echo $execution; ?>&type=STACKING_2">Stacking Analysis</a></h3>
						<p>
								<a href="tools/naflex/output.php?execution=<?php echo $execution; ?>&type=STACKING_2" class="btn green"> View Analysis </a>
						</p>
				</div>
		</div>
</div>

<?php

}

}

if (file_exists("$path/CONTACTS") && is_dir("$path/CONTACTS") /*&& $data['rev_sequence'] != '-'*/) {

?>
<div class="col-md-3 col-sm-4">
		<div class="thumbnail">
				<a href="tools/naflex/output.php?execution=<?php echo $execution; ?>&type=CONTACTS">
					<div style="width: 100%; height: 200px; background:#fff url('tools/naflex/images/output/contacts.png') no-repeat center center;background-size:contain; border:1px solid #e8e8e8;"></div>
				</a>
				<div class="caption">
						<h3><a href="tools/naflex/output.php?execution=<?php echo $execution; ?>&type=CONTACTS">Contacts Analysis</a></h3>
						<p>
								<a href="tools/naflex/output.php?execution=<?php echo $execution; ?>&type=CONTACTS" class="btn green"> View Analysis </a>
						</p>
				</div>
		</div>
</div>
<?php 

}

if (file_exists("$path/EXPvsMD") && is_dir("$path/EXPvsMD") and ( ! preg_match('/DDD_bsc1/',$execution) )) {

?>
<div class="col-md-3 col-sm-4">
		<div class="thumbnail">
				<a href="tools/naflex/output.php?execution=<?php echo $execution; ?>&type=EXPvsMD">
					<div style="width: 100%; height: 200px; background:#fff url('tools/naflex/images/output/expvsmd.png') no-repeat center center;background-size:contain; border:1px solid #e8e8e8;"></div>
				</a>
				<div class="caption">
						<h3><a href="tools/naflex/output.php?execution=<?php echo $execution; ?>&type=EXPvsMD">Experimental vs MD Analysis</a></h3>
						<p>
								<a href="tools/naflex/output.php?execution=<?php echo $execution; ?>&type=EXPvsMD" class="btn green"> View Analysis </a>
						</p>
				</div>
		</div>
</div>
<?php 

}

?>

<?php }

// end default content

chdir($oldpath);

?>

</div>
                    </div>
                    <!-- END CONTENT BODY -->
                </div>
                <!-- END CONTENT -->

<div class="modal fade bs-modal" id="modalImages" tabindex="-1" role="basic" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                            </div>
                            <div class="modal-body">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
				</div>


<?php 

include "../../htmlib/footer.inc.php"; 
include "../../htmlib/js.inc.php";

?>

<?php

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
        $datfile = "$GLOBALS[webDir]/$dat";
	$count = substr_count(file_get_contents($datfile), "\n");
        $file = "$GLOBALS[webDir]/$plot";
	logger("PLOT: $file");
        if( file_exists($file) && filesize($file) != 0){

		# Long sequences (as chromatin in MuG)
		if ($count > 25) {

	                ?>
        	        <table border="0"><tr><td>
                	<img border="1" style="width: 1000px; height: 500px;" src="<?php echo $GLOBALS['BASEURL']; ?>tools/naflex/<?php echo $plot ?>">
	                </td><td>
        	        <p align="right" class="btn blue" style="margin:0;" onClick='window.open("<?php echo $GLOBALS['BASEURL']; ?>tools/naflex/<?php echo $plot ?>","<?php echo $plotname ?>","_blank,resize=1,width=1200,height=800");'>Open in New Window</p><br/>
                	<a href="<?php echo $GLOBALS['BASEURL']; ?>tools/naflex/getFile.php?fileloc=<?php echo $dat ?>&type=curves"> <p align="right" class="btn blue" style="margin:0;">Download Raw Data</p></a>
	                </td></tr></table>
                	<?php

		}
		else {
	                ?>
        	        <table border="0"><tr><td>
                	<img border="1" src="<?php echo $GLOBALS['BASEURL']; ?>tools/naflex/<?php echo $plot ?>">
	                </td><td>
        	        <!--<p align="right" class="curvesDatText" onClick='window.open("<?php echo $GLOBALS['homeURL'].'/'.$plot ?>","<?php echo $plotname ?>","_blank,resize=1,width=800,height=600");'>Open in New Window</p><br/>-->
                	<a href="<?php echo $GLOBALS['BASEURL']; ?>tools/naflex/getFile.php?fileloc=<?php echo $dat ?>&type=curves"> <p align="right" class="btn blue" style="margin:0;">Download Raw Data</p></a>
	                </td></tr></table>
                	<?php
		}

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
			<a href="<?php echo $GLOBALS['BASEURL']; ?>tools/naflex/<?php echo $plot ?>" class="preview jqueryImages" title>
			<img border="1" width="190px" src="<?php echo $GLOBALS['BASEURL']; ?>tools/naflex/<?php echo $plot ?>"></a>
	                <!--<img border="1" width="190px" src="<?php echo $plot ?>">-->
			<br/>
	                <!--<p class="curvesDatText" onClick='window.open("<?php echo $GLOBALS['homeURL'].'/'.$plot ?>","_blank","resize=1,width=800,height=600");'>Open in New Window</p><br/>-->
        	        <a href="<?php echo $GLOBALS['BASEURL']; ?>tools/naflex/getFile.php?fileloc=<?php echo $dat ?>&type=curves"> <p class="btn blue" style="margin:0;">Download Raw Data</p></a>
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



