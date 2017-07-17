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
					$code = "$j-$letter1";
					$codeBP = "$j-";
					logger("Seq1: -$letter1-");
                                        if($letter1 == 'X'){
                                                echo "<td id='divTableSeq1' class='divTableSeq' title='Unrecognized Nucleotide'>$letter1</td>\n";
                                        }
                                        else{
						echo "<td id='divTableSeq1' class='divTableSeq'><a id='$code' class='unselected' href=\"javascript:selectNucleotideNew('nmrJ','$code','$userPath');\" title='Nucleotide Helical Parameters'> $letter1 </a></td>\n";
					}
					if($i != $length1-1)
						echo "<td id='divTableSeq1Seps' class='divTableSeqSeps'><a id='$codeBP' class='unselected' title='Backbone Helical Parameters'> - </a></td>\n";
					$_SESSION['userData']['seq1'][$i] = 0;
				}
				echo "<td width='30' class='divTableSeqPrimes'>3'</td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "<td></td>\n";
				echo "<td></td>\n";
				echo "<td></td>\n";
				for($i=0;$i<$length1;$i++){
					$letter1 = $arrSeq1[$i];
					$letter4 = $arrSeq2[$i];
					$j = $i + 1;
					$codeBPS = "$j-$letter1$letter4";
					$letter2 = $arrSeq1[$j];
					$letter3 = $arrSeq2[$j];
					$codeTet = "$j-$letter1$letter2$letter3$letter4";
					if($i > 0 and $i < $length1 - 1)
						echo "<td id='divTableSepsUpDown' class='divTableSeqSepsX'><a id='$codeBPS' name='BPS' class='unselected' href=\"javascript:selectNucleotideNew('nmrJ','$codeBPS','$userPath');javascript:selectBasePairStep('$codeBPS','$length1');\" title='Base Pair Helical Parameters'> | </a></td>\n";
					if($i > 0 and $i < $length1 - 2)
						echo "<td id='divTableSepsX' class='divTableSeqSepsX' ><a id='$codeTet' name='TET' class='unselected' href=\"javascript:selectNucleotideNew('nmrJ','$codeTet','$userPath');javascript:selectTetramer('$codeTet','$length1');\" title='Base Pair Step Helical Parameters'> x </a></td>\n";
					$_SESSION['userData']['seqBPS'][$i] = 0;
				}
				echo "</tr>\n";
				echo "<tr>\n";
				echo "<td class='divTableSeqPrimes'>3'</td>\n";
				for($i=0;$i<$length2;$i++){
					$letter1 = $arrSeq2[$i];
					$letter2 = $arrSeq2[$i];
					$j = ($length1 * 2) - $i;
					$code = "$j-$letter2";
					$codeBP = "$j-";
                                        if($letter2 == 'X'){
                                                echo "<td id='divTableSeq2' class='divTableSeq' title='Unrecognized Nucleotide'>$letter2</td>\n";
                                        }
                                        else{
						echo "<td id='divTableSeq2' class='divTableSeq'><a id='$code' class='unselected' href=\"javascript:selectNucleotideNew('nmrJ','$code','$userPath');\" title='Nucleotide Helical Parameters'> $letter2 </a></td>\n";
					}
					if($i != $length2-1)
						echo "<td id='divTableSeq2Seps' class='divTableSeqSeps'><a id='$codeBP' class='unselected' title='Backbone Helical Parameters'> - </a></td>\n";
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

        <p align="center"><b><i>Interactive Sequence (<a href="<?php echo $GLOBALS['homeURL']?>/help.php?id=tutorialAnalysisNA#HelicalParms">see Help</a>)</i></b></p>

	<br/><br/>	

	<div id="AvgVsTime">

         <table align="center" border="0">
                <tr>
                        <td class="curvesText"><a id='AVGSel' class='unselected' href="javascript:unhideNmrAvgPlots('AVGSel','Proton_Pairs_Avg');" title='AVG Results'>Average Results</a></td>
			<td width="50"></td>
                        <td class="curvesText"><a id='TIMESel' class='unselected' href="javascript:unhideNmrTimePlots('TIMESel','Proton_Pairs_Params');" title='TIME Results'>Results by Time</a></td>
		</tr>
	</table>
	</div>

	<div id="Avg_Params">

        <div id="Proton_Pairs_Avg" class="hidden" style="text-align:center;">
		<table align="center" cellpadding="5">
		<tr>
                <td>
                  <p class="curvesText"><a id='J1p2pSel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('J1p2pSel_Avg','J1p2p-RNAAvgSelPlot');" title='J-couplings: J1p2p'>H1'-H2'</a></p><br/>
	          <p class="curvesText"><a id='J2p3pSel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('J2p3pSel_Avg','J2p3p-RNAAvgSelPlot');" title='J-couplings: J2p3p'>H2'-H3'</a></p><br/>
        	  <p class="curvesText"><a id='J3p4pSel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('J3p4pSel_Avg','J3p4p-RNAAvgSelPlot');" title='J-couplings: J3p4p'>H3'-H4'</a></p><br/>
        	  <p class="curvesText"><a id='ALLSel_Avg' class='unselected' href="javascript:unhideSelNmrALL('ALLSel_Avg','nmrJ.ALL');" title='J-couplings: ALL'>ALL</a></p><br/>
		</td>
		<td width="50"></td>
		<td id="Rib_Avg_images" rowspan="20">
                <img id="Rib_Avg" src="images/ribose_trans.png" width="300" align="center" class="unhidden">
                <img id="J1p2pSel_AvgRib" src="images/ribose_J1p2p_trans.png" width="300" align="center" class="hidden">
                <img id="J2p3pSel_AvgRib" src="images/ribose_J2p3p_trans.png" width="300" align="center" class="hidden">
                <img id="J3p4pSel_AvgRib" src="images/ribose_J3p4p_trans.png" width="300" align="center" class="hidden">
		<p><i style="font-size:12pt">RNA Ribose</i></p>
		</td></tr>
		</table>
        </div>
	</div>

	<div id="Time_Params">

        <div id="Proton_Pairs_Params" class="hidden" style="text-align:center;">
		<table align="center" cellpadding="5">
		<tr>
                <td>
                  <p class="curvesText"><a id='J1p2pSel' class='unselected' href="javascript:unhideSelNmrPlots('J1p2pSel','J1p2p-RNATimeSelPlot');" title='J-couplings: J1p2p'>H1'-H2'</a></p><br/>
	          <p class="curvesText"><a id='J2p3pSel' class='unselected' href="javascript:unhideSelNmrPlots('J2p3pSel','J2p3p-RNATimeSelPlot');" title='J-couplings: J2p3p'>H2'-H3'</a></p><br/>
        	  <p class="curvesText"><a id='J3p4pSel' class='unselected' href="javascript:unhideSelNmrPlots('J3p4pSel','J3p4p-RNATimeSelPlot');" title='J-couplings: J3p4p'>H3'-H4'</a></p>

		</td>
		<td width="50"></td>
		<td id="Rib_images" rowspan="20">
                <img id="Rib" src="images/ribose_trans.png" width="300" align="center" class="unhidden">
                <img id="J1p2p-RNASelRib" src="images/ribose_J1p2p_trans.png" width="300" align="center" class="hidden">
                <img id="J2p3p-RNASelRib" src="images/ribose_J2p3p_trans.png" width="300" align="center" class="hidden">
                <img id="J3p4p-RNASelRib" src="images/ribose_J3p4p_trans.png" width="300" align="center" class="hidden">
		<p><i style="font-size:12pt">RNA Ribose</i></p>
		</td></tr>
		</table>
        </div>
        </div>
	
	<!-- PLOTS -->

        <div id="NmrParams">

        <div id="HelicalParamsPlots">

                <div id="J1p2p-RNAAvgSelPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;">
			<?php plotAVG ($userPath,"J1p2p-RNA.avg"); ?>
                </div>
                <div id="J2p3p-RNAAvgSelPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;">
			<?php plotAVG ($userPath,"J2p3p-RNA.avg"); ?>
                </div>
                <div id="J3p4p-RNAAvgSelPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;">
			<?php plotAVG ($userPath,"J3p4p-RNA.avg"); ?>
                </div>
        </div>

        <div id="StatsTable">
	<?php

		$cmd = "ls */*.stats";
		$out = exec($cmd,$files);
		$length = count($files);

		for($cont=0;$cont<$length;$cont++){
			# 22/J1p2p-RNA.stats
			$file = $files[$cont];
			$dirs = preg_split('/\//',$file);
			$realFile = $dirs[1];
			$parts = preg_split('/\./',$realFile);
			$num = $parts[0]."-".$dirs[0];
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
			$cmd = "cat Jcoupling.stats";
			$out = exec($cmd,$lines);
			$length = count($lines);

			print "<table cellpadding='15' align='center' border='0' class='tableNMR'>\n";
			print "<tr><td>Nucleotide Number</td><td>Jcoupling Type</td><td>Mean</td><td>Stdev</td></tr>\n";

			for($cont=0;$cont<$length;$cont++){
				# 18 J1p2p-RNA    5.320    1.504
				$line = $lines[$cont];
				if(preg_match('/^#/',$line)) continue;
				$arr = preg_split('/\s+/',$line);
				print "<tr><td>$arr[0]</td><td>$arr[1]</td><td>$arr[2]</td><td>$arr[3]</td></tr>\n";
			}
			print "</table>\n";
	?>

		<table align="center" border="0"><tr><td>
		<a href="getFile.php?fileloc=<?php echo $userPath."/Jcoupling.stats" ?>&type=curves"> <p align="right" class="curvesDatText">Download Raw Data</p></a>
		</td></tr></table>
	</div>
	</div>
	</div>

        <div id="HelicalParamsPlotsTime">

        <div id="Time_helical_bpstep">
        <div id="J1p2p-RNATimeSelPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;"></div>
        <div id="J2p3p-RNATimeSelPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;"></div>
        <div id="J3p4p-RNATimeSelPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;"></div>
        </div>

	</div>

	<br/><br/>	
