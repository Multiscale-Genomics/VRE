<?
# check.inc.php
# Controls de format
#
# Copyright 1999 Josep Ll. Gelpi G3 COM, S.L.
#
function checkOblig (&$falta, &$f, $oblig) {
# versio obligatoris en formulari
	foreach (split("#",$oblig) as $cm) {	
		if (!$f[$cm]) 
			$falta[$cm] = 1;
	}
	return count($falta);
}

?>