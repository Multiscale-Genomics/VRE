<?php

require "phplib/session.inc.php";

$f_location = $_REQUEST["fileloc"];

$mimeType="image/png";

if  ($_REQUEST["type"]=="curves") {

	$l1 = preg_split ("/\//",$f_location);
	$name = $l1[sizeof($l1)-1];
	if ($name == '')
		$name = "rawdata.dat";

	$mimeType="text/plain";
	header("Content-Disposition: attachment; filename=$name");
	header('Expires: 0');
	header('Pragma: no-cache');
}
else if  ($_REQUEST["type"]=="animation") {
	$mimeType="chemical/x-pdb";
}
else if  ($_REQUEST["type"]=="tgz") {
	$mimeType="application/x-compressed";
	header("Content-Disposition: attachment; filename=$name");
}

if (file_exists($f_location) && is_readable($f_location)) {
	header("Content-Type: $mimeType");
	header("Content-Length: " . filesize($f_location));
	readfile($f_location);
}

?>
