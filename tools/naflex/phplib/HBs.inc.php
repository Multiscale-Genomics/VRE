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
					$codeBP = "$code:$code2";
					logger("Seq1: -$letter1-");
                                        if($letter1 == 'X'){
                                                echo "<td id='divTableSeq1' class='divTableSeq' title='Unrecognized Nucleotide'>$letter1</td>\n";
                                        }
                                        else{
						echo "<td id='divTableSeq1' class='divTableSeq' title='Nucleotide Helical Parameters'> $letter1 </td>\n";
					}
					if($i != $length1-1)
						echo "<td id='divTableSeq1Seps' class='divTableSeqSeps unselected'> - </td>\n";
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
					$letter2 = $arrSeq1[$j];
					$letter3 = $arrSeq2[$j];
					$codeTet = "$j-$letter1$letter2$letter3$letter4";
					#if($i > 0 and $i < $length1 - 1)
					if($letter1 == 'X' or $letter4 == 'X'){
						echo "<td id='divTableSepsUpDown' class='divTableSeqSepsX unselected'> | </td>\n";
					}
					else{
						echo "<td id='divTableSepsUpDown' class='divTableSeqSepsX'><a id='$codeBPS' name='BPS' class='unselected' href=\"javascript:selectNucleotideNew('HBs','$codeBPS','$userPath');javascript:selectBasePairStep('$codeBPS_old','$length1');\" title='Base Pair Helical Parameters'> | </a></td>\n";
					}
					if($i >= 0 and $i < $length1 - 1)
						echo "<td id='divTableSepsX' class='divTableSeqSepsX unselected' > x </td>\n";
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
					$codeBP = "$code1:$code2";
                                        if($letter1 == 'X'){
                                                echo "<td id='divTableSeq2' class='divTableSeq' title='Unrecognized Nucleotide'>$letter1</td>\n";
                                        }
                                        else{
						echo "<td id='divTableSeq2' class='divTableSeq' title='Nucleotide Helical Parameters'> $letter1 </td>\n";
					}
					if($i != $length2-1)
						echo "<td id='divTableSeq2Seps' class='divTableSeqSeps unselected'> - </td>\n";
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
                        <td class="curvesText"><a id='AVGSel' class='unselected btn blue' href="javascript:unhideNmrAvgPlots('AVGSel','Proton_Pairs_Avg');" title='AVG Results'>Average Results</a></td>
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
		<tr><td style="vertical-align:top; text-align:left;">
                  <p class="curvesText"><a id='N1H3Sel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('N1H3Sel_Avg','N1H3-DNAAvgSelPlot');" title='HBs: N1-H3'>A@N1-T/U@H3</a></p><br/>
                  <p class="curvesText"><a id='H61O4Sel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('H61O4Sel_Avg','H61O4-DNAAvgSelPlot');" title='HBs: H61-O4'>A@H61-T/U@O4</a></p><br/>
		</td><td style="vertical-align:top; text-align:left;">
                  <p class="curvesText"><a id='O6H41Sel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('O6H41Sel_Avg','O6H41-DNAAvgSelPlot');" title='HBs: O6-H41'>G@O6-C@H41</a></p><br/>
                  <p class="curvesText"><a id='H1N3Sel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('H1N3Sel_Avg','H1N3-DNAAvgSelPlot');" title='HBs: H1-N3'>G@H1-C@N3</a></p><br/>
                  <p class="curvesText"><a id='H21O2Sel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('H21O2Sel_Avg','H21O2-DNAAvgSelPlot');" title='HBs: H21-O2'>G@H21-C@O2</a></p><br/>
		</td><td style="vertical-align:top; text-align:left;">
        	  <p class="curvesText"><a id='ALLSel_Avg' class='unselected' href="javascript:unhideSelNmrALL('ALLSel_Avg','nmrJ.ALL');" title='HBs: ALL'>ALL</a></p>
		</td>
		<td id="Rib_Avg_images" rowspan="6" align="center">
                <p id="Rib_Avg" class="unhidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/CG_AT_HB_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/CG_AT_HB_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>Base Pair Canonical Hydrogen Bonds</i></p>
                <p id="N1H3Sel_AvgRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/AT_HB_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/AT_HB_trans.png" border="0" width="400" align="center"></a><i style="font-size:12pt"><br/><br/>Adenine-Thymine/Uracil Canonical HBs</i></p>
                <p id="H61O4Sel_AvgRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/AT_HB_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/AT_HB_trans.png" border="0" width="400" align="center"></a><i style="font-size:12pt"><br/><br/>Adenine-Thymine/Uracil Canonical HBs</i></p>
                <p id="O6H41Sel_AvgRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/CG_HB_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/CG_HB_trans.png" border="0" width="400" align="center"></a><i style="font-size:12pt"><br/><br/>Guanine-Cytosine Canonical HBs</i></p>
                <p id="H1N3Sel_AvgRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/CG_HB_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/CG_HB_trans.png" border="0" width="400" align="center"></a><i style="font-size:12pt"><br/><br/>Guanine-Cytosine Canonical HBs</i></p>
                <p id="H21O2Sel_AvgRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/CG_HB_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/CG_HB_trans.png" border="0" width="400" align="center"></a><i style="font-size:12pt"><br/><br/>Guanine-Cytosine Canonical HBs</i></p>
		<br/><br/>

		</td></tr>
		</table>

        </div>
	</div>

	<div id="Time_Params">

        <div id="Proton_Pairs_Params" class="hidden" style="text-align:center;margin-left:40px;">
		<table align="" cellpadding="0" width="950">
		<tr><td style="vertical-align:top; text-align:left;">
		  <p id="AT_N1H3" class="curvesText"><a id='Nuc_N1-Nuc_H3Sel' class='unselected' href="javascript:unhideSelHBsPlots('Nuc_N1-Nuc_H3Sel','Nuc_N1-Nuc_H3TimeSelPlot');" title='HBs: N1-H3'>A@N1-T/U@H3</a></p><br/>
		  <p id="AT_H61O4" class="curvesText"><a id='Nuc_H61-Nuc_O4Sel' class='unselected' href="javascript:unhideSelHBsPlots('Nuc_H61-Nuc_O4Sel','Nuc_H61-Nuc_O4TimeSelPlot');" title='HBs: H61-O4'>A@H61-T/U@O4</a></p><br/>
		</td><td style="vertical-align:top; text-align:left;">
		  <p id="CG_O6H41" class="curvesText"><a id='Nuc_O6-Nuc_H41Sel' class='unselected' href="javascript:unhideSelHBsPlots('Nuc_O6-Nuc_H41Sel','Nuc_O6-Nuc_H41TimeSelPlot');" title='HBs: O6-H41'>G@O6-C@H41</a></p><br/>
		  <p id="CG_H1N3" class="curvesText"><a id='Nuc_H1-Nuc_N3Sel' class='unselected' href="javascript:unhideSelHBsPlots('Nuc_H1-Nuc_N3Sel','Nuc_H1-Nuc_N3TimeSelPlot');" title='HBs: H1-N3'>G@H1-C@N3</a></p><br/>
		  <p id="CG_H21O2" class="curvesText"><a id='Nuc_H21-Nuc_O2Sel' class='unselected' href="javascript:unhideSelHBsPlots('Nuc_H21-Nuc_O2Sel','Nuc_H21-Nuc_O2TimeSelPlot');" title='HBs: H21-O2'>G@H21-C@O2</a></p><br/>
		</td>
		<td id="Rib_images" rowspan="6" align="center">
                <p id="Rib" class="unhidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/CG_AT_HB_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/CG_AT_HB_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>Base Pair Canonical Hydrogen Bonds</i></p>
                <p id="ATSelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/AT_HB_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/AT_HB_trans.png" border="0" width="500" align="center"></a><i style="font-size:12pt"><br/><br/>Adenine-Thymine/Uracil Canonical HBs</i></p>
                <p id="CGSelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/CG_HB_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/CG_HB_trans.png" border="0" width="500" align="center"></a><i style="font-size:12pt"><br/><br/>Guanine-Cytosine Canonical HBs</i></p>
		</td></tr>
		</table>
        </div>
        </div>
	
	<!-- PLOTS -->

        <div id="NmrParams">

        <div id="HelicalParamsPlots">

                <div id="N1H3-DNAAvgSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;">
			<?php plotAVG ($userPath,"N1-H3.avg"); ?>
                </div>
                <div id="H61O4-DNAAvgSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;">
			<?php plotAVG ($userPath,"H61-O4.avg"); ?>
                </div>
                <div id="O6H41-DNAAvgSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;">
			<?php plotAVG ($userPath,"O6-H41.avg"); ?>
                </div>
                <div id="H1N3-DNAAvgSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;">
			<?php plotAVG ($userPath,"H1-N3.avg"); ?>
                </div>
                <div id="H21O2-DNAAvgSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;">
			<?php plotAVG ($userPath,"H21-O2.avg"); ?>
                </div>
        </div>

        <div id="StatsTable">
	<?php

		$cmd = "ls */*.stats";
		$out = exec($cmd,$files);
		$length = count($files);

		for($cont=0;$cont<$length;$cont++){
			# 22/J1p2p-RNA.stats 20/20_H1p-20_H2p.stats
			$file = $files[$cont];
			$dirs = preg_split('/\//',$file);
			$realFile = $dirs[1];
			$parts = preg_split('/\./',$realFile);
			$num = $parts[0]."-".$dirs[0];
			#$num = $parts[0];
			print "<div id='nmrJ.".$num."' class='hidden'>";
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

		<!-- Plot for ALL J-couplings -->
		<div id="nmrJ.ALL" class="hidden">
<?php
			$cmd = "cat ALL.stats";
			$out = exec($cmd,$lines);
			$length = count($lines);

			print "<table cellpadding='15' align='center' border='0' id='sortableTable' class='sortable tableNMR'>\n";
			print "<tr><td>Nucleotide Number</td><td>Hydrogen Bond</td><td>Mean</td><td>Stdev</td></tr>\n";

			for($cont=0;$cont<$length;$cont++){
				# 18 J1p2pp-DNA    5.320    1.504
				$line = $lines[$cont];
				if(preg_match('/^#/',$line)) continue;
				$arr = preg_split('/\s+/',$line);
				print "<tr><td>$arr[0]</td><td>$arr[1]</td><td>$arr[2]</td><td>$arr[3]</td></tr>\n";
			}
			print "</table>\n";
	?>

		<table align="" border="0"><tr><td>
		<a href="<?php echo $GLOBALS['BASEURL']; ?>tools/naflex/getFile.php?fileloc=<?php echo $userPath."/ALL.stats" ?>&type=curves" class="btn blue"> Download Raw Data</a>
		</td></tr></table>
	</div>
	</div>
	</div>

        <div id="HelicalParamsPlotsTime">

        <div id="Time_helical_bpstep">
        <div id="Nuc_N1-Nuc_H3TimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="Nuc_H61-Nuc_O4TimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="Nuc_O6-Nuc_H41TimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="Nuc_H1-Nuc_N3TimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="Nuc_H21-Nuc_O2TimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        </div>

	</div>

	<br/><br/>	
