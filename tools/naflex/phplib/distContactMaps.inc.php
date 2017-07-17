<?php
	$cmd = "pwd";
	$pwd = exec($cmd);
	$dirs = preg_split('/\//',$pwd);
	#array_shift($dirs);
	#array_shift($dirs);
	array_shift($dirs);
	array_shift($dirs);
	$pwd = implode($dirs,'/');
logger("PWD: $pwd");
?>

<div id="AtomPairs">
        <?php

                $cmd = "ls -t *.png";
                $out = exec($cmd,$files);
                $length = count($files);

		$pairCont = 0;
		print "<table align='' cellpadding='10' style='margin-left:40px;'><tr>\n";
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
			if($pairCont==4){
				print "</tr><tr>\n";
			}
                }
		print "</tr></table>\n";
?>
</div>

<div id="AtomPairsPlots">
        <?php

                $cmd = "ls -t *.png";
                $out = exec($cmd,$files2);
                $length = count($files2);

		$pairCont = 0;
		print "<table align='' cellpadding='0' style='margin-left:40px;'><tr>\n";
	        for($cont=0;$cont<$length;$cont++){
			# distanceMean.contactMapSTDEV.png
                        $file = $files2[$cont];
                        $parts = preg_split('/\./',$file);
                        $num = $parts[1];
			$dat = "$parts[0].$parts[1].dat";
?>
			<td><div id='<?php echo $num ?>Plot' class='hidden'>

			<!--<img border='1' width='600' src="<?php echo $GLOBALS[homeURL] ?>/<?php echo "$pwd/$file" ?>" border="0" width="300" align="center">-->

			<img border='1' width='600' src="<?php echo $webdir.$_REQUEST['type']."/$file" ?>" border="0" width="300" align="center">

			<table align="" border="0" style="margin-top:30px;"><tr><!--<td>
			<p align="right" class="curvesDatText" onClick="window.open('tools/naflex/NAFlex-Data/NAFlex_parmBSC1/<?php echo $_REQUEST['proj']."/".$_REQUEST['type']."/$file" ?>','', '_blank,resize=1,width=800,height=400');">Open in New Window</p><br/>
			</td>--><td>
			<a href="<?php echo $GLOBALS['BASEURL']; ?>tools/naflex/getFile.php?fileloc=<?php echo $downdir.$_REQUEST['type']."/$file" ?>&type=curves" class="btn blue"> Download Raw Data</a>
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

