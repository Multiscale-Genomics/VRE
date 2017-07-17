<?php
	$cmd = "pwd";
	$pwd = exec($cmd);
	$dirs = preg_split('/\//',$pwd);
	array_shift($dirs);
	array_shift($dirs);
	array_shift($dirs);
	array_shift($dirs);
	$pwd = implode($dirs,'/');
?>

<div id="AtomPairs">
        <?php

                $cmd = "ls */*.dat";
                $out = exec($cmd,$files);
                $length = count($files);

		$pairCont = 0;
		print "<table align='center' cellpadding='10'><tr>\n";
	        for($cont=0;$cont<$length;$cont++){
			# 8@H61/18@H5-8@H61.dat
                        $file = $files[$cont];
                        $dirs = preg_split('/\//',$file);
                        $realFile = $dirs[1];
                        $parts = preg_split('/\./',$realFile);
                        #$num = $parts[0]."-".$dirs[0];
                        $num = $parts[0];
?>
			<td><div>
			<p id='dist<?php echo $num ?>' class='curvesText'><a id='dist<?php echo $num ?>Sel' class='unselected' href="javascript:selectDist('<?php echo $num ?>');" title='Dist: <?php echo $num ?>'><?php echo $num ?></a></p>
                        </div></td>
<?php
			$pairCont++;
			if($pairCont==5){
				print "</tr><tr>\n";
			}
                }
		print "</tr></table>\n";
?>
</div>

<div id="StatsTable">
<?php

                $cmd = "ls */*.stats";
                $out = exec($cmd,$stats);
                $length = count($stats);

                for($cont=0;$cont<$length;$cont++){
                        # 22/J1p2p-RNA.stats 20/20_H1p-20_H2p.stats
                        $file = $stats[$cont];
                        $dirs = preg_split('/\//',$file);
                        $realFile = $dirs[1];
                        $parts = preg_split('/\./',$realFile);
                        $num = $parts[0];
                        print "<div id='table.".$num."' class='hidden'>";
                        #print "<table cellpadding='15' align='center' border='0' style='background-color:#dcdcdc;text-align:center'>\n";
                        print "<table cellpadding='15' align='center' border='0' class='tableNMR'>\n";

                        $cmd = "cat $file";
                        $out = exec($cmd,$content);

                        $dirs = preg_split('/,/',$out);
                        $value1 = preg_split('/:/',$dirs[0]);
                        $value2 = preg_split('/:/',$dirs[1]);
                        $mean = sprintf("%8.3f",$value1[1]);
                        $stdev = sprintf("%8.3f",$value2[1]);

                        print "<tr><td></td><td>Mean</td><td>Stdev</td></tr><tr><td>$num</td><td>$mean</td><td>$stdev</td></tr>\n";

                        print "</table>\n";
                        print "</div>\n";
                }
?>
</div>

<div id="AtomPairsPlots">
        <?php

                $cmd = "ls */*.png";
                $out = exec($cmd,$files2);
                $length = count($files2);

		$pairCont = 0;
		print "<table align='center' cellpadding='10'><tr>\n";
	        for($cont=0;$cont<$length;$cont++){
			# 8@H61/18@H5-8@H61.dat
                        $file = $files2[$cont];
			$dat = preg_replace("/.png/","",$file);
                        $dirs = preg_split('/\//',$file);
                        $realFile = $dirs[1];
                        $parts = preg_split('/\./',$realFile);
                        #$num = $parts[0]."-".$dirs[0];
                        $num = $parts[0];
?>
			<td><div id='<?php echo $num ?>Plot' class='hidden'>

			<img border='1' width='900' src="<?php echo $GLOBALS['BASEURL']; ?>tools/naflex/<?php echo $GLOBALS[homeURL] ?>/<?php echo "$pwd/$file" ?>" border="0" width="300" align="center">

			<table align="center" border="0"><tr><td>
			<p align="right" class="curvesDatText" onClick="window.open('<?php echo $GLOBALS[homeURL] ?>/<?php echo "$pwd/$file" ?>','', '_blank,resize=1,width=800,height=400');">Open in New Window</p><br/>
			</td><td>
			<a href="<?php echo $GLOBALS['BASEURL']; ?>tools/naflex/getFile.php?fileloc=<?php echo $GLOBALS['BASEURL']; ?>tools/naflex/<?php echo "$pwd/$dat" ?>&type=curves"> <p align="right" class="curvesDatText">Download Raw Data</p></a>
			</td></tr></table>

                        </div></td>
<?php
			$pairCont++;
			if($pairCont>=5){
				print "</tr><tr>\n";
			}
                }
		print "</tr></table>\n";
?>
</div>

