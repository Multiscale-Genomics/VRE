<?php

require "../phplib/genlibraries.php";
redirectOutside();
$tls = getTools_ListComplete(1);
$vslzrs = getVisualizers_ListComplete(1);

$toolList = array_merge($tls, $vslzrs);

foreach($toolList as $tool) {

	if($_REQUEST["tool"] == $tool["_id"]) {
		$comb = getInputFilesCombinations($tool);
		break;
	}

}

echo json_encode(explode("~", $comb));

