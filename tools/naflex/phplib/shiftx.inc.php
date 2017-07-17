<!--
# MDWeb for Nucleic Acids
# Chemical Shifts analysis with Shiftx.
-->

<?php
	# Results Averaged by Time/Atom section:
	$cmd = "ls *.avg.shiftX.dat.png";
	$out = exec($cmd,$files);

        $length = count($files);

	$cont = 0;
	$html = '';
	print "<h3>Chemical Shifts per Atom (averaged by time)</h3><br/>\n";
	print "<table align='center' cellpadding='5'><tr>";
        for($i=0;$i<$length;$i++){
		$parts = preg_split('/\./',$files[$i]);
		$atom = $parts[0];
		//print "<td><b><i><u><a href=\"javascript:unhide('avg$atom');\">$atom</a></u></i></b></td>\n";
		print "<td><b><i><u><a href=\"javascript:window.open('$GLOBALS[homeURL]/$userPath/$atom.avg.shiftX.dat.png','Chemical Shift','resize=1,scrollbars=1');\">$atom</a></u></i></b></td>\n";
		$html .= "<div align='center' style='background-color:#E6E6FA;' id='avg$atom' class='hidden'><br/>\n";
		$file = "$path/$atom.avg.shiftX.dat.png";
		if(file_exists($file) and filesize($file) != 0){
			$html .= "<p align='center'> <b><i><u>Atom $atom </u></i></b></p><br/>\n";
			//$html .= "<a align='center' href='$GLOBALS[homeURL]/$userPath/$atom.avg.shiftX.dat.png' target='_blank'>";
			$html .= "<img border='0' src='$userPath/$atom.avg.shiftX.dat.png'></a>\n";
		}
		$html .= "<br/><br/></div>\n";
		$cont++;
		if($cont % 15 == 0)
			print "</tr><tr>\n";
	}
	print "</tr></table>\n";

	print "$html\n";

	# Results by Time/Residue section:

	print "<h3>Chemical Shifts per Residue (by time)</h3><br/>\n";
	$done = array();
	$html = '';
	$cont = 0;
	print "<table align='center' cellpadding='5'><tr>";
	$ftraj=fopen("structure.atoms", "r");
	while(!feof($ftraj)) {
		$line=fgets($ftraj);
		$line = rtrim($line);
		
		$arr = preg_split("/\s+/",$line);
		$residue = $arr[0];
		$atom = $arr[1];
	
		$file = "$path/$residue-$atom.shiftX.dat.png";

		if(!$done["$residue"]){
			$html .= "<br/><br/></div>\n";
			print "<td><b><i><u><a href=\"javascript:unhide('$residue');\">$residue</a></u></i></b></td>\n";
			$cont ++;
			if($cont % 15 == 0)
				print "</tr><tr>\n";
			$html .= "<div align='center' style='background-color:#E6E6FA;' id='$residue' class='hidden'><br/>";
			$done["$residue"] = 1;
		}

		if(file_exists($file) and filesize($file) != 0){
			$html .= "<p align='center'> <b><i><u>Atom $atom </u></i></b></p><br/>\n";

			$html .= "<a align='center' href='$GLOBALS[homeURL]/$userPath/$residue-$atom.shiftX.dat.png' target='_blank'>";

			$html .= "<img border='0' src='$userPath/$residue-$atom.shiftX.dat.png'></a>\n";
		}
	}
	fclose($ftraj);
	print "</tr></table>\n";

	print "<div>\n";
	print $html;
	print "</div><br/>\n";

        //$out = exec("cat $shiftxFile", $txtArray);
        //$txt = join ("<br/>",$txtArray);
	
	//print "<pre>$txt</pre>";
?>
