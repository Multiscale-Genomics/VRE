<?php

require "phplib/session.inc.php";

$name = $_REQUEST["code"];
$num = $_REQUEST["bps"];

if (!$name or !$num){
	echo "BIGNASim code and Base Pair Step input parameters are mandatory.\n";
	echo "Example: http://mmb.irbbarcelona.org/BigNASim/getStiffnessMatrix.php?code=NAFlex_DDD_bsc1&bps=2\n";
	exit;
}

$ls = exec("ls ../NAFlex2/NAFlex-Data/NAFlex_parmBSC1/$name/STIFFNESS/FORCE_CTES/*.$num.cte");

if ($ls){
	$f_location = $ls;

	$aname = preg_split("/\//",$ls);
	$aname2 = preg_split("/\./",$aname[7]);
	$napos = strtoupper($aname2[0])."-$aname2[1]";

	if (file_exists($f_location) && is_readable($f_location)) {
	
		$st = file_get_contents($f_location);

		$b = explode ('\n',$st);
		$c = implode ('',$b);
		$d = trim($c);
		$array = preg_split("/\s+/",$d);
		$a = array();
		$a["Stiffness for $napos"] = $array;

		echo json_encode($a);
		echo "\n";
	}
	else{
		echo "Sorry, stiffness matrix for BIGNASim code $name and Base Pair Step $num not found...\n";
	}
}
else{
	echo "Sorry, stiffness matrix for BIGNASim code $name and Base Pair Step $num not found...\n";
}

?>
