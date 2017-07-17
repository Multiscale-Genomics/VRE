<?php

#plantilles
function gettemplate ($fn) {
#print "htmlib/$fn";
	return file_get_contents("htmlib/$fn", FILE_TEXT);
}

function parseTemplate ($f, $txt, $indirFields='', $dateFields='') {
	if ($indirFields) {
		foreach (array_keys($indirFields) as $k) {
			$txt = str_replace ("##$k##",$GLOBALS[$indirFields[$k]][$f[$k]],$txt);
		}
	}
	if ($dateFields) {
		foreach (array_values($dateFields) as $k) {
			$txt = str_replace ("##$k##",prdata($_SESSION[idioma],$f[$k]),$txt);
		}
	}
	foreach (array_keys($f) as $k) 
		$txt = str_replace ("##$k##",$f[$k],$txt);
	$txt = preg_replace ("/##[^#]*##/","-",$txt);
	return $txt;
}


?>
