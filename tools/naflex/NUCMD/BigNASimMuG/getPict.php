<?php

require "../NAFlex2/phplib/globalVars.inc.php";

$mimeType="image/png";

$proj = $_REQUEST['idSim'];

$dir = $GLOBALS['webDir'].$GLOBALS['parmbsc1Dir']."/".$proj;

$fileAux = "htmlib/imageNotAvailable.png";
$file = "$dir/INFO/structure.png";

if (file_exists($file) && is_readable($file)) {
	header("Content-Type: $mimeType");
	header("Content-Length: " . filesize($file));
	readfile($file);
}
else {
	header("Content-Type: $mimeType");
	header("Content-Length: " . filesize($fileAux));
	readfile($fileAux);
}
?>

