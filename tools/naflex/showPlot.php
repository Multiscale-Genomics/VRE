<?php
# MDWeb
# nucleicAcidAnalysis.php
#
require "phplib/globals.inc.php";

//if (!$_SESSION['userData']) redirect("index.php");

# Printing Headers
print headerNA("NAFlex. Nucleic Acids Flexibility",0);

print projectHeader($_SESSION['projectData']);

$k = count($_POST);

if($k <= 1){
	?>
		<script type="text/javascript">
			alert("At least one observable should be selected!");
			self.close();
		</script>
	<?php
}

$path = $_POST['path'];

?>
	<script type="text/javascript" src="js/sortable.js"></script>

        <h3 align="center"> Selected Observables - <i>Plot</i> </h3>
<?php

#echo "AVG: <br/>";
$cont = 0;
foreach (array_keys($_POST['avg']) as $k){

	$file = "$path/$k.avg.dat";
	#echo "$k ($file) <br/>";

	if(file_exists($file)){
		#echo "File $k exists...<br/>";

		$legends .= "$k ";
		$cont ++;

		$fdat=fopen($file, "r");

		while(!feof($fdat)) {
			$line=fgets($fdat);
			list ($code,$mean,$stdev) = preg_split('/\s+/',$line);

			if($code){
				$dat[$code][$k]['mean'] = $mean;
				$dat[$code][$k]['stdev'] = $stdev;
				$index[$code] = 1;
				$observables[$k] = 1;
				#echo "$k $code mean $mean stdev $stdev <br/>";
			}
		}
		fclose($fdat);
	}
}

$numBases = count($dat);
$splitBases = intval ($numBases / 2);
foreach ($index as $code => $v) {
	foreach ($observables as $obs => $v2){
		$mean = $dat[$code][$obs]['mean'];
		$stdev = $dat[$code][$obs]['stdev'];
		if($mean){
			$final[$code] = $final[$code]." $mean $stdev";
		}
		else{
			$final[$code] = $final[$code]. " ? ?";
		}
	}
}

chdir($path);

$filenameOut = "pasted.avg.dat";
$fout=fopen($filenameOut, "w");

fwrite($fout, "# Nucleotide - Observable (mean, stdev)\n");
fwrite($fout, "# ".$legends."\n");

$contWrite = 0;
foreach ($final as $code => $line){
	#echo "$code $line <br/>";
	$towrite = "$code $line\n";
	fwrite($fout, $towrite);
	$contWrite++;
	#echo "WRITE: $contWrite , $splitBases";
	if($contWrite == $splitBases){
		$towrite = "\n";
		fwrite($fout, $towrite);
	}
}
fclose($fout);

$arr = preg_split('/\//',$path);
array_shift($arr);
array_shift($arr);
array_shift($arr);
array_shift($arr);
$newPath = implode("/",$arr);

$cmd = "perl /var/www/MDWeb/scripts/plotXYWithSeqgnuplot_mult.pl $filenameOut $cont \"Nucleotide\"  \"NOE (Angstroms)\" \"Selected Observables\" $legends";
$p = exec($cmd);

?>

<div id="J1p2p-DNAAvgSelPlot" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;">
<table border="0"><tr><td>
<img border="1" src=' <?php echo $GLOBALS['homeURL'].'/'.$newPath.'/'.$filenameOut ?>.png' align='center'>
</td><td>
<p align="right" class="curvesDatText" onClick="javascript:window.open('<?php echo $GLOBALS['homeURL'].'/'.$newPath ?>/pasted.avg.dat.png','SelObservables','_blank,resize=1,width=800,height=600');">Open in New Window</p><br/>
<a href="getFile.php?fileloc=<?php echo $newPath ?>/pasted.avg.dat&type=curves"> <p align="right" class="curvesDatText">Download Raw Data</p></a>
</td></tr></table>
</div>

        <h3 align="center"> Selected Observables - <i>Raw Data</i> </h3>

                <!-- Plot for ALL J-couplings -->
                <div id="nmrJ.ALL">
<?php
                        $cmd = "cat NOE.stats";
                        $out = exec($cmd,$lines);
                        $length = count($lines);

                        #print "<table cellpadding='15' align='center' border='0' class='tableNMR'>\n";
                        print "<table cellpadding='15' align='center' border='0' id='sortableTable' class='sortable tableNMR'>\n";
                        print "<tr><td>Nucleotide Number</td><td>Nucleotide Code</td><td>NOE</td><td>Mean</td><td>Stdev</td></tr>\n";

			foreach ($index as $code => $v) {
				if(preg_match('/_/',$code)){
					list($code2,$trash) = preg_split('/_/',$code);
					list($nuc,$num) = preg_split('/-/',$code2);
				}
				else{
					list($nuc,$num) = preg_split('/-/',$code);
				}
				foreach ($observables as $obs => $v2){
					$mean = $dat[$code][$obs]['mean'];
					$stdev = $dat[$code][$obs]['stdev'];
					if($mean){
						$mean = sprintf("%5.2f",$dat[$code][$obs]['mean']);
						$stdev = sprintf("%5.2f",$dat[$code][$obs]['stdev']);

		                                # 18 J1p2pp-DNA    5.320    1.504
						#$num = floatval($num);
                                		print "<tr><td>$num</td><td>$nuc</td><td>$obs</td><td>$mean</td><td>$stdev</td></tr>\n";
					}
				}
                        }
                        print "</table>\n";
        ?>

                <table align="center" border="0"><tr><td>
		<a href="getFile.php?fileloc=<?php echo $newPath ?>/pasted.avg.dat&type=curves"> <p align="right" class="curvesDatText">Download Raw Data</p></a>
                </td></tr></table>
        </div>

<?php

print footerNA();

?>
