<?php

  # Strand  1 has  12 bases (5'-3'): CGCGAGGACGCG
  # Strand  2 has  12 bases (3'-5'): GCGCTCCTGCGC

	$cmd = "grep 'Strand  1' $curvesFile";
	$out = exec($cmd,$strand1);
	$cmd = "grep 'Strand  2' $curvesFile";
	$out = exec($cmd,$strand2);
	$cmd = "grep 'NucType:' $curvesFile";
	$out = exec($cmd,$nucleicType);
	logger("Stiffness: $curvesFile");

	$p = preg_split ("/:/",$nucleicType[0]);
	$naType = $p[sizeof($p)-1];
	logger("Nucleic Type: $naType");

	$l1 = preg_split ("/:/",$strand1[0]);
	$seq1 = $l1[sizeof($l1)-1];
	$l2 = preg_split ("/:/",$strand2[0]);
	$seq2 = $l2[sizeof($l2)-1];

	#$seq1 = "GATTACAGATTACA";
	#$seq2 = "CTAATGTCTAATGT";

	$seq1 = preg_replace("/ /","",$seq1);
	$seq2 = preg_replace("/ /","",$seq2);

	logger("Curves Seq1: -$seq1-");
	logger("Curves Seq2: -$seq2-");

	$arrSeq1 = preg_split("//",$seq1);
	array_shift($arrSeq1);
	array_pop($arrSeq1);
	$arrSeq2 = preg_split("//",$seq2);
	array_shift($arrSeq2);
	array_pop($arrSeq2);

	$length1 = count($arrSeq1);
	$length2 = count($arrSeq2);

	# Initializing Nucleotide Selection Arrays.
	for($i=0;$i<$length1;$i++){
		$_SESSION['userData']['seq1'][$i] = 0;
		$_SESSION['userData']['seqBPS'][$i] = 0;
		$_SESSION['userData']['seq2'][$i] = 0;
	}

?>

	<div id="divSeq2">
		<!--<?php echo "$seq1" ?><br/>-->
		<!--<?php echo "$seq2" ?><br/>-->
		<!--<br/><br/>-->

		<table cellpadding="5" align="center" border="0">
		<tr>
			<?php  
				echo "<td></td>\n";
				for($i=0;$i<$length2;$i++){
					$num = $i+1;
					echo "<td id='divTableSeqNum'> $num </td>\n";
					echo "<td></td>\n";
				}
				echo "</tr>\n";
				echo "<tr>\n";
				echo "<td width='30' class='divTableSeqPrimes'>5'</td>\n";
				for($i=0;$i<$length1;$i++){
					$letter1 = $arrSeq1[$i];
					$letter2 = $arrSeq1[$i+1];
					$j = $i+1;
					$k = $j+1;
					$code = "$j-$letter1";
					$code2 = "$k-$letter2";
					$codeBP = "$j-";
					logger("Seq1: -$letter1-");
                                        if($letter1 == 'X'){
                                                echo "<td class='divTableSeq' title='Unrecognized Nucleotide'><a id='divTableSeq1' class='unselected'> $letter1</a></td>\n";
                                        }
                                        else{
						echo "<td class='divTableSeq' title='Nucleotide Helical Parameters'><a id='$j-$letter1' class='unselected'> $letter1 </a></td>\n";
					}
					if($i != $length1-1)
						#echo "<td id='divTableSeq1Seps' class='divTableSeqSeps unselected'> - </td>\n";
						echo "<td id='divTableSeq1Seps' class='divTableSeqSeps'><a id='$codeBP' class='unselected' title='Backbone'> - </a></td>\n";					
					$_SESSION['userData']['seq1'][$i] = 0;
				}
				echo "<td width='30' class='divTableSeqPrimes'>3'</td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "<td></td>\n";
				for($i=0;$i<$length1;$i++){
					$letter1 = $arrSeq1[$i];
					$letter4 = $arrSeq2[$i];
					$j = $i + 1;
					$pair = ($length1 * 2) - $i;
					$codeBPS_old = "$j-$letter1$letter4";
					$codeBPS = "$j-$letter1:$pair-$letter4";
					$codeBPS_new = "$j-$pair";
					$letter2 = $arrSeq1[$j];
					$letter3 = $arrSeq2[$j];
					$codeTet = "$j-$letter1$letter2$letter3$letter4";
					#if($i > 0 and $i < $length1 - 1)
					if($letter1 == 'X' or $letter4 == 'X'){
						echo "<td id='divTableSepsUpDown' class='divTableSeqSepsX unselected'> | </td>\n";
					}
					else{
						echo "<td id='divTableSepsUpDown' class='divTableSeqSepsX'><a id='$codeBPS_old' name='BPS' class='unselected' href=\"javascript:selectNucleotideStackingHB('$codeBPS_new','$userPath');javascript:selectBasePairStep('$codeBPS_old','$length1');\" title='Base Pair Helical Parameters'> | </a></td>\n";
					}
					if($i >= 0 and $i < $length1 - 1)
						echo "<td id='divTableSepsX' class='divTableSeqSepsX' ><a id='$codeTet' name='TET' class='unselected' href=\"javascript:selectNucleotideStacking('$codeTet','$userPath','$length1');javascript:selectTetramer('$codeTet','$length1');\" title='Base Pair Step Helical Parameters'> x </a></td>\n";
						#echo "<td id='divTableSepsX' class='divTableSeqSepsX unselected' > x </td>\n";
					$_SESSION['userData']['seqBPS'][$i] = 0;
				}
				echo "</tr>\n";
				echo "<tr>\n";
				echo "<td class='divTableSeqPrimes'>3'</td>\n";
				for($i=0;$i<$length2;$i++){
					$letter1 = $arrSeq2[$i];
					$letter2 = $arrSeq2[$i+1];
					$j = ($length1 * 2) - $i - 1;
					$k = $j + 1;
					$p = ($length1 * 2) - $i;
					$code = "$p-$letter1";
					$code1 = "$j-$letter2";
					$code2 = "$k-$letter1";
					$codeBP = "$p-";
                                        if($letter1 == 'X'){
                                                echo "<td class='divTableSeq' title='Unrecognized Nucleotide'><a id='divTableSeq2' class='unselected'> $letter1 </a></td>\n";
                                        }
                                        else{
						echo "<td class='divTableSeq' title='Nucleotide Helical Parameters'><a id='$k-$letter1' class='unselected'> $letter1 </a></td>\n";
					}
					if($i != $length2-1)
						echo "<td id='divTableSeq1Seps' class='divTableSeqSeps'><a id='$codeBP' class='unselected' title='Backbone'> - </a></td>\n";
						#echo "<td id='divTableSeq2Seps' class='divTableSeqSeps unselected'> - </td>\n";
					$_SESSION['userData']['seq2'][$i] = 0;
				}
				echo "<td class='divTableSeqPrimes'>5'</td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "<td></td>\n";
				for($i=$length1;$i>0;$i--){
					$num = $length1 + $i ;
					echo "<td id='divTableSeqNum'> $num </td>\n";
					echo "<td></td>\n";
				}
			?>
		</tr>
		</table>
	</div>

        <p style="float: left;
    text-align: left;
    width: 50%;"><b>Interactive Sequence (<a href="<?php echo $GLOBALS['homeURL']?>/help.php?id=tutorialAnalysisNA#HelicalParms" target="_blank">see Help</a>)</b></p>

	<br/><br/>	

	<div id="AvgVsTime">

         <table cellpadding="15" align="" border="0" class="avgParms">
                <tr>
                        <td class="curvesText"><a id='AVGSel' class='unselected btn blue' href="javascript:unhideNmrAvgPlots('AVGSel','Proton_Pairs_Avg');" title='AVG Results'>Contact Maps</a></td>
			<td width="50"></td>
                        <td class="curvesText"><a id='TIMESel' class='unselected btn blue' href="javascript:unhideNmrTimePlots('TIMESel','Proton_Pairs_Params');" title='TIME Results'>Results by Time</a></td>
		</tr>
	</table>
	</div>

	<br/>
	
	<div id="Avg_Params">


	<!--
	<input type="hidden" name="user" value="$user" />
	<input type="hidden" name="proj" value="$project" />
	<input type="hidden" name="op" value="$op" />
	<input type="hidden" name="type" value="$analysis" />
	-->
	<input type="hidden" name="path" value="<?php echo $path ?>" />

        <div id="Proton_Pairs_Avg" class="hidden" style="text-align:center;margin-left:40px;">
		<table align="" cellpadding="0" width="950">
		<tr><td style="text-align:left; vertical-align:top;">
                  <p class="curvesText"><a id='stackingMeanSel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('stackingMeanSel_Avg','stackingAvgMeanSelPlot');" title='Stacking energies - Mean'>HB/Stacking Energies - Mean</a></p><br/>
                  <p class="curvesText"><a id='stackingMinSel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('stackingMinSel_Avg','stackingAvgMinSelPlot');" title='Stacking energies - Min'>HB/Stacking Energies - Min</a></p><br/>
                  <p class="curvesText"><a id='stackingMaxSel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('stackingMaxSel_Avg','stackingAvgMaxSelPlot');" title='Stacking energies - Max'>HB/Stacking Energies - Max</a></p><br/>
                  <p class="curvesText"><a id='stackingStdevSel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('stackingStdevSel_Avg','stackingAvgStdevSelPlot');" title='Stacking energies - Stdev'>HB/Stacking Energies - Stdev</a></p><br/>
                  <p class="curvesText"><a id='stackingZscoreSel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('stackingZscoreSel_Avg','stackingAvgZscoreSelPlot');" title='Stacking energies - Relative Standard Deviation'>HB/Stacking Energies - RSD</a></p><br/>
		</td>
		<td id="Rib_Avg_images" rowspan="6" style="text-align:left; vertical-align:top;">
                <p id="Rib_Avg" class="unhidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/stacking.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/stacking.png" border="0" width="400" align="center"></a><i style="font-size:12pt"><br/><br/>HB/Stacking Energies</i></p>
                <p id="stackingMeanSel_AvgRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/stacking.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/stacking.png" border="0" width="400" align="center"></a><i style="font-size:12pt"><br/><br/>HB/Stacking Energies</i></p>
                <p id="stackingMinSel_AvgRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/stacking.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/stacking.png" border="0" width="400" align="center"></a><i style="font-size:12pt"><br/><br/>HB/Stacking Energies</i></p>
                <p id="stackingMaxSel_AvgRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/stacking.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/stacking.png" border="0" width="400" align="center"></a><i style="font-size:12pt"><br/><br/>HB/Stacking Energies</i></p>
                <p id="stackingStdevSel_AvgRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/stacking.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/stacking.png" border="0" width="400" align="center"></a><i style="font-size:12pt"><br/><br/>HB/Stacking Energies</i></p>
                <p id="stackingZscoreSel_AvgRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/stacking.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/stacking.png" border="0" width="400" align="center"></a><i style="font-size:12pt"><br/><br/>HB/Stacking Energies</i></p>
		<a href="http://iopenshell.usc.edu/research/projects/uracil-dimer/" target="_blank"><i style="font-size:8pt">[Image courtesy of iOpenShell]</i></a>
		<br/><br/>
		</td></tr>
		</table>

        </div>
	</div>

	<div id="Time_Params">

        <div id="Proton_Pairs_Params" class="hidden" style="text-align:center;margin-left:40px;">
		<table align="" cellpadding="0" width="950">
		<tr><td style="text-align:left; vertical-align:top;">
		  <p id="TimeStacking" class="curvesText"><a id='Nuc-Nuc_StackingTimeSel' class='unselected' href="javascript:unhideSelStackingPlots('Nuc-Nuc_StackingTimeSel','Nuc-Nuc_StackingTimeSelPlot','<? echo $length1 ?>');" title='Stacking energies'>Stacking Energies</a></p><br/>
		  <p id="TimeHB" class="curvesText"><a id='Nuc-Nuc_HBTimeSel' class='unselected' href="javascript:unhideSelStackingPlots('Nuc-Nuc_HBTimeSel','Nuc-Nuc_HBTimeSelPlot');" title='Hydrogen Bond energies'>Hydrogen Bond Energies</a></p><br/>
		</td>
		<td id="Rib_images" rowspan="6" style="text-align:left; vertical-align:top;">
                <p id="Rib" class="unhidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/stacking.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/stacking.png" border="0" width="400" align="center"></a><i style="font-size:12pt"><br/><br/>HB/Stacking Energies</i></p>
                <p id="ATSelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/stacking.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/stacking.png" border="0" width="400" align="center"></a><i style="font-size:12pt"><br/><br/>HB/Stacking Energies</i></p>
                <p id="CGSelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/stacking.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/stacking.png" border="0" width="400" align="center"></a><i style="font-size:12pt"><br/><br/>HB/Stacking Energies</i></p>
		<a href="http://iopenshell.usc.edu/research/projects/uracil-dimer/" target="_blank"><i style="font-size:8pt">[Image courtesy of iOpenShell]</i></a>
		</td></tr>
		</table>
        </div>
        </div>
	
	<!-- PLOTS -->

        <div id="NmrParams">

        <div id="HelicalParamsPlots">

                <div id="stackingAvgMeanSelPlot" class="hidden" style="text-align:left;padding: 15px 40px;">
			<?php plotAVG ($userPath,"energies/stackingContactMapsMEAN"); ?>
                </div>
                <div id="stackingAvgMinSelPlot" class="hidden" style="text-align:left;padding: 15px 40px;">
			<?php plotAVG ($userPath,"energies/stackingContactMapsMIN"); ?>
                </div>
                <div id="stackingAvgMaxSelPlot" class="hidden" style="text-align:left;padding: 15px 40px;">
			<?php plotAVG ($userPath,"energies/stackingContactMapsMAX"); ?>
                </div>
                <div id="stackingAvgStdevSelPlot" class="hidden" style="text-align:left;padding: 15px 40px;">
			<?php plotAVG ($userPath,"energies/stackingContactMapsSTDEV"); ?>
                </div>
                <div id="stackingAvgZscoreSelPlot" class="hidden" style="text-align:left;padding: 15px 40px;">
			<?php plotAVG ($userPath,"energies/stackingContactMapsRSD"); ?>
                </div>
        </div>

	</div>

        <div id="HelicalParamsPlotsTime">

        <div id="Time_Stacking">
<?php
	for($cont=1;$cont<$length1;$cont++){
		$j = $cont+1;
		$file = "$cont-$j.stats";
		$num = "$cont-$j";

		$cmd = "cat $cont/$file";
		$out = exec($cmd,$content);
		
		$dirs = preg_split('/,/',$out);
		$value1 = preg_split('/:/',$dirs[0]);
		$value2 = preg_split('/:/',$dirs[1]);
		$mean = sprintf("%8.3f",$value1[1]);
		$stdev = sprintf("%8.3f",$value2[1]);

		print "<div id='nmrJ.".$num."' class='hidden'>";
		print "<table cellpadding='15' align='' border='0' class='tableNMR' style='margin-left:40px;'>\n";	
		print "<tr><td></td><td>Mean</td><td>Stdev</td></tr><tr><td>$num</td><td>$mean</td><td>$stdev</td></tr>\n";
		print "</table>\n";
		print "</div>\n";
	}
?>
        <div id="Nuc1-Nuc2_StackingTimeSelPlot" class="hidden" style="text-align:left;padding: 15px 40px;"></div>
<?php
	for($cont=$length1;$cont<($length1 * 2);$cont++){
		$j = $cont+1;
		$file = "$cont-$j.stats";
		$num = "$cont-$j";

		$cmd = "cat $cont/$file";
		$out = exec($cmd,$content);
		
		$dirs = preg_split('/,/',$out);
		$value1 = preg_split('/:/',$dirs[0]);
		$value2 = preg_split('/:/',$dirs[1]);
		$mean = sprintf("%8.3f",$value1[1]);
		$stdev = sprintf("%8.3f",$value2[1]);

		print "<div id='nmrJ.".$num."' class='hidden'>";
		print "<table cellpadding='15' align='' border='0' class='tableNMR'>\n";	
		print "<tr><td></td><td>Mean</td><td>Stdev</td></tr><tr><td>$num</td><td>$mean</td><td>$stdev</td></tr>\n";
		print "</table>\n";
		print "</div>\n";
	}
?>
        <div id="Nuc3-Nuc4_StackingTimeSelPlot" class="hidden" style="text-align:left;padding: 15px 40px;"></div>
<?php
	for($cont=1;$cont<$length1;$cont++){
		$j = ($length1 * 2) - $cont;
		$file = "$cont-$j.stats";
		$num = "$cont-$j";

		$cmd = "cat $cont/$file";
		$out = exec($cmd,$content);
		
		$dirs = preg_split('/,/',$out);
		$value1 = preg_split('/:/',$dirs[0]);
		$value2 = preg_split('/:/',$dirs[1]);
		$mean = sprintf("%8.3f",$value1[1]);
		$stdev = sprintf("%8.3f",$value2[1]);

		print "<div id='nmrJ.".$num."' class='hidden'>";
		print "<table cellpadding='15' align='' border='0' class='tableNMR'>\n";	
		print "<tr><td></td><td>Mean</td><td>Stdev</td></tr><tr><td>$num</td><td>$mean</td><td>$stdev</td></tr>\n";
		print "</table>\n";
		print "</div>\n";
	}
?>
        <div id="Nuc1-Nuc3_StackingTimeSelPlot" class="hidden" style="text-align:left;padding: 15px 40px;"></div>
<?php
	for($cont=2;$cont<=$length1;$cont++){
		$j = ($length1 * 2) - $cont+2;
		$file = "$cont-$j.stats";
		$num = "$cont-$j";

		$cmd = "cat $cont/$file";
		$out = exec($cmd,$content);
		
		$dirs = preg_split('/,/',$out);
		$value1 = preg_split('/:/',$dirs[0]);
		$value2 = preg_split('/:/',$dirs[1]);
		$mean = sprintf("%8.3f",$value1[1]);
		$stdev = sprintf("%8.3f",$value2[1]);

		print "<div id='nmrJ.".$num."' class='hidden'>";
		print "<table cellpadding='15' align='' border='0' class='tableNMR'>\n";	
		print "<tr><td></td><td>Mean</td><td>Stdev</td></tr><tr><td>$num</td><td>$mean</td><td>$stdev</td></tr>\n";
		print "</table>\n";
		print "</div>\n";
	}
?>
        <div id="Nuc2-Nuc4_StackingTimeSelPlot" class="hidden" style="text-align:left;padding: 15px 40px;"></div>
	</div>
        <div id="Time_HBs">

        <div id="StatsTable">
	<?php

		$cmd = "ls */*_HB.dat";
		$out = exec($cmd,$files);
		$length = count($files);

		for($cont=0;$cont<$length;$cont++){
			$file = $files[$cont];
			$dirs = preg_split('/\//',$file);
			$realFile = $dirs[1];
			$parts = preg_split('/_/',$realFile);
			$newFile = preg_replace("/_HB.dat/",".stats",$file);
			$num = $parts[0];

			if($done{$num}) {
				continue;
			}

			print "<div id='hb.".$num."' class='hidden'>";
			print "<table cellpadding='15' align='' border='0' class='tableNMR'>\n";	

			$cmd = "cat $newFile";
			$out = exec($cmd,$content);

			$dirs = preg_split('/,/',$out);
			$value1 = preg_split('/:/',$dirs[0]);
			$value2 = preg_split('/:/',$dirs[1]);
			$mean = sprintf("%8.3f",$value1[1]);
			$stdev = sprintf("%8.3f",$value2[1]);

			print "<tr><td></td><td>Mean</td><td>Stdev</td></tr><tr><td>$num</td><td>$mean</td><td>$stdev</td></tr>\n";

			print "</table>\n";
			print "</div>\n";

			$done{$num} = 1;
		}		
?>
	</div>

        <div id="Nuc-Nuc_HBTimeSelPlot" class="hidden" style="text-align:left;padding: 15px 40px;"></div>
        </div>

	</div>

	<br/><br/>	
