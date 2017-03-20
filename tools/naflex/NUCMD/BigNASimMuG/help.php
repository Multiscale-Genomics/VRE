<?php
require "phplib/global.inc.php";
print headerMMB("BIGNASim database structure and analysis portal for nucleic acids simulation data");
?>
<script>
$(document).ready( function() {

        menuTabs("Help");

});
</script>

<h3>BIGNASim database structure and analysis portal for nucleic acids simulation data</h3>

<div class="metaImageSection">
    <div class="metaImage" style="flex-wrap: nowrap;">

<?php

if (!$_REQUEST['id'])
	$_REQUEST['id']="base";

$menu = Array (
	'base' => 'General Information',
	'BNSdownload' => 'Download',
	'browsing' => 'Browsing',
	'searching' => 'Searching',
	'simulation' => 'Simulation',
	'analysis' =>'Global Analyses',
	'metatraj' => 'Meta-Trajectory',
	'datamanager' => 'Data Manager',
	'stat' => 'Statistics',
	'submission' => 'Traj. Submission',
	'onto' => 'NA Ontology',
	'expMD' => 'Experimental Averages',
	'expTraj' => 'Experimental Trajectories',
	'NAFlex' => 'DNA & RNA Structure and Helical Parameters Analyses (NAFlex)',
	'tut1' => 'Tutorial 1 -- Search',
	'tut2' => 'Tutorial 2 -- Global analysis (XCGY)',
	'tut3' => 'Tutorial 3 -- Meta-trajectory (XCGY)',
	'tut4' => 'Tutorial 4 -- Experimental vs MD analysis',
	'faq' => 'F.A.Q.',
	'software' => 'Software',
	#'links' => 'Related Links',
	'references' => 'References',
);
$sep = Array(
	'tut1' => 1,
	'faq' => 1,
	'onto' => 1,
	'browsing' => 1,
);
?>

<div id="MenuHelp">
  <p><a class="itemMenu" href="index.php">Home</a></p>
  <?php foreach (array_keys($menu) as $id) {

	if($sep[$id]){
		echo "----<br/>";
	}
?>
 <?php if($id != "NAFlex") { ?>
  <p><a class="itemMenu" href="help.php?id=<?php print $id?>">
 <?php } else { ?>
  <p><a class="itemMenu" href="http://mmb.irbbarcelona.org/NAFlex/help.php?id=naFlex">
 <?php 
	}

	if($_REQUEST['id']== $id) 
		echo "<strong>".$menu[$id]."</strong>";
	else
		echo $menu[$id];
	print "</a></p>";
}
?>
</div>
<div id="ContentHelp">
  <?php 

	if(file_exists("htmlib/help/$_REQUEST[id].inc.htm"))
		include "htmlib/help/$_REQUEST[id].inc.htm";

?>
	</div>
   </div>
</div>

<?php
print footerMMB();
?>
