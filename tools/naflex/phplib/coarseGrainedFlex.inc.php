<?php
	$cmd = "pwd";
	$pwd = exec($cmd);
	$dirs = preg_split('/\//',$pwd);
	array_shift($dirs);
	array_shift($dirs);
	array_shift($dirs);
	array_shift($dirs);
	$pwd = implode($dirs,'/');
logger("PWD: $pwd");
?>

<div id="AtomPairs">
        <?php

                $cmd = "ls *.png";
                $out = exec($cmd,$files);
                $length = count($files);

		$pairCont = 0;
		print "<table align='center' cellpadding='10'><tr>\n";
	        for($cont=0;$cont<$length;$cont++){
			# distanceMean.contactMapSTDEV.png
                        $file = $files[$cont];
                        $parts = preg_split('/\./',$file);
                        $num = $parts[1];
?>
			<td><div>
			<p id='dist<?php echo $num ?>' class='curvesText'><a id='dist<?php echo $num ?>Sel' class='unselected' href="javascript:selectDist('<?php echo $num ?>');" title='Dist: <?php echo $num ?>'><?php echo $num ?></a></p>
                        </div></td>
<?php
			$pairCont++;
			if($pairCont>=4){
				print "</tr><tr>\n";
			}
                }
		print "</tr></table>\n";
?>
</div>

<div id="AtomPairsPlots">
        <?php

                $cmd = "ls *.png";
                $out = exec($cmd,$files2);
                $length = count($files2);

		$pairCont = 0;
		print "<table align='center' cellpadding='0'><tr>\n";
	        for($cont=0;$cont<$length;$cont++){
			# distanceMean.contactMapSTDEV.png
                        $file = $files2[$cont];
                        $parts = preg_split('/\./',$file);
                        $num = $parts[1];
			$dat = "$parts[0].$parts[1].dat";
?>
			<td><div id='<?php echo $num ?>Plot' class='hidden'>

			<img border='1' width='600' src="<?php echo $GLOBALS[homeURL] ?>/<?php echo "$pwd/$dat.png" ?>" border="0" width="300" align="center">

			<table align="center" border="0"><tr><td>
			<p align="right" class="curvesDatText" onClick="window.open('<?php echo $GLOBALS[homeURL] ?>/<?php echo "$pwd/$file" ?>','', '_blank,resize=1,width=800,height=400');">Open in New Window</p><br/>
			</td><td>
			<a href="getFile.php?fileloc=<?php echo "$pwd/$dat" ?>&type=curves"> <p align="right" class="curvesDatText">Download Raw Data</p></a>
			</td></tr></table>

                        </div></td>
<?php
			$pairCont++;
			if($pairCont>=4){
				print "</tr><tr>\n";
			}
                }
		print "</tr></table>\n";
?>
</div>

