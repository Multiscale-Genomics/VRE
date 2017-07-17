<?php

	require "phplib/global.inc.php";
	require "phplib/aux.inc.php";
	#require "PDBProcess.inc.php";

	session_start();
	
        #if ( $_SESSION['User'] ){
        #	$idSession = $_SESSION['User']['id'];
	#}
	#else
	#	$idSession = $_REQUEST["idSession"];


	$external_ap = "bignasim";
	require "../master/header.php";

	$oldWorkDir = getcwd();

	$idSession = $_REQUEST["idSession"];
	$workDir = $GLOBALS['tmpDir']."/".$idSession;
	if (! file_exists("$workDir"))
		mkdir("$workDir");

	chdir($workDir);

	print headerMMB("ParmBSC1 Nucleic Acids Simulations Database");
	
	// Loading Session Vars
	#$_SESSION = loadSession($idSession);

?>

<div id="pagewrap_vre">
          <div class="head_wait container">

	<h3>BIGNASim database structure and analysis portal for nucleic acids simulation data</h3>

<?php
	#foreach ($_SESSION[trajs] as $k => $v){
	#	echo "$k => $v<br/>";
	#}

	#foreach ($_SESSION["SGE"] as $sgepid => $v){
	#	$a = $_SESSION[SGE][$sgepid][name];
	#	$b = $_SESSION[SGE][$sgepid][desc];
	#	echo "PID: $sgepid, NAME: $a, DESC: $b <br/>";
	#}

?>
           <h2><em>BIGNASim trajectory generator</em></h2>
                <p style="font-size: 1.2em;  white-space: pre-wrap;">
	<strong>Metatrajectory</strong> is being generated. This process can take several minutes.

        You will be <strong>automatically redirected</strong> to a <strong>workspace</strong> once the process has finished. In this <strong>workspace</strong>, all trajectories generated by <strong>BIGNASim session</strong> will be available to <strong>download</strong> and/or to further analyse them with <strong>NAFlex server</strong>.
		
	The <strong>workspace</strong> area can be accessed at any time here: <a href="BNSdatamanager/workspace.php?BNSId=<?php echo $idSession ?>">Session Workspace</a>

	Generated trajectories will be stored in the <strong>workspace</strong> for a maximum of <strong>30 days</strong>.

	<strong>Workspace</strong> can be accessed anytime using this link:

	<?php echo $GLOBALS['homeNUCMD_FULL'] ?>/BNSdatamanager/workspace.php?BNSId=<?php echo $idSession ?>

	</p>

	<img src="images/progressBarTractor.gif" alt="Ploughing..." >
          </div>
</div>
<!--
	<table align="center" cellpadding="10"><tr>
	<td>

        <table cellpadding="10" border="0" align="center">
                <tr>
                        <td colspan="3" class="titol">Meta-trajectory generator</b></i></td>
                </tr>
                <tr>
                <td align="center">
                        <img src="images/pcazip_nuc.gif" alt="[OrigStructure]" title="[OrigStructure]" style="max-width:200px;" border="0" />
                </td>
                <td align="center">
                        <img src="images/animated_arrow_right2.gif" width="60%" border="0" />
                </td>
                <td align="center">
                        <img src="images/pcazip_nuc.gif" alt="[TargetStructure]" title="[TargetStructure]" style="max-width:200px;" border="0" />
                </td>
                </tr>
        </table>

	</td>
	</tr>
	</table>
-->
<?php

	$pid = $_SESSION['pid'];

	$pend = stillRunning($essId,$pid);

	if ($pend) {
?>

	<script>
	    timer = setTimeout("location.href = 'waitResults.php?idSession=<?php echo $idSession ?>'", 60000);
	</script>

<?php 
	}
	else {
?>
	<script>
		location.href = 'BNSdatamanager/workspace.php?BNSId=<?php echo $idSession ?>'; 
	</script>



<?php 
	}

	
	chdir($oldWorkDir);


	require "../master/footer.php";

	//print footerMMB();

?>
