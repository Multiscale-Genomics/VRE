<?php
require "phplib/global.inc.php";
#

$trajName = $_REQUEST['traj'];

$topName = reset(preg_split('/\./',$trajName)).".pdb";
$format = end(preg_split('/\./',$trajName));

#$topName = $trajName.".pdb";

$pdbGFS = getGSFile($GLOBALS['cassandra'], $topName);
$trajGFS = getGSFile($GLOBALS['cassandra'], $trajName);

$top = "/tmp/".basename($topName);
$traj = "/tmp/".basename($trajName);

$fout=fopen($top, "w");
fwrite($fout, $pdbGFS);
fclose($fout);

$fout=fopen($traj, "w");
fwrite($fout, $trajGFS);
fclose($fout);

#echo "TopName: $topName, TrajName: $trajName";

if ( $top == ">" or $traj == ">" ) {
	print "Uuups, we couldn't generate the trajectory... Please try it again later.";
	return;
}

if (!file_exists($top) || !file_exists($traj)) {
	if (0 == filesize( $top ) || 0 == filesize($traj) ) {
		print "Uuups, we couldn't generate the trajectory... Please try it again later.";
		return;
	}
}

$traj2 = str_replace(".$format",'',basename($trajName));
#$format = 'crd';
redirect("../NAFlex2/metatraj2NAFlex.php?id=$traj2&ext=$format&mask=SelectedFragment&frames=SelectedFrames");

