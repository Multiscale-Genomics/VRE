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

        $duplex = 1;
        if(empty($seq2)){
                $duplex = 0;
                $seq2 = preg_replace("/./","/",$seq1);
        }

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
						if($duplex) {echo "<td id='divTableSeq2' class='divTableSeq'><a id='$code' class='unselected' href=\"javascript:selectNucleotideNew('nmrJ','$code','$userPath');\" title='Nucleotide Helical Parameters'> $letter2 </a></td>\n"; }
						else { echo "<td id='divTableSeq2' class='divTableSeq'> $letter2 </td>\n";}
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

	<div id="Avg_Params">

        <div id="Proton_Pairs_Avg" class="hidden" style="text-align:center;">
		<table align="" cellpadding="50">
		<tr>
                <td style="vertical-align:top; text-align:left;">
                <ul>
		  <li class="curvesText"><a id='J1p2ppSel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('J1p2ppSel_Avg','J1p2pp-DNAAvgSelPlot');" title='J-couplings: J1p2pp'>Jcoupling: H1'-H2''</a></li><br/><br/>
                  <li class="curvesText"><a id='J1p2pSel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('J1p2pSel_Avg','J1p2p-DNAAvgSelPlot');" title='J-couplings: J1p2p'>Jcoupling: H1'-H2'</a></li><br/><br/>
		  <li class="curvesText"><a id='J2pp3pSel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('J2pp3pSel_Avg','J2pp3p-DNAAvgSelPlot');" title='J-couplings: J2pp3p'>Jcoupling: H2''-H3'</a></li><br/><br/>
	          <li class="curvesText"><a id='J2p3pSel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('J2p3pSel_Avg','J2p3p-DNAAvgSelPlot');" title='J-couplings: J2p3p'>Jcoupling: H2'-H3'</a></li><br/><br/>
        	  <li class="curvesText"><a id='J3p4pSel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('J3p4pSel_Avg','J3p4p-DNAAvgSelPlot');" title='J-couplings: J3p4p'>Jcoupling: H3'-H4'</a></li><br/><br/>
        	  <li class="curvesText"><a id='ALLSel_Avg' class='unselected' href="javascript:unhideSelNmrALL('ALLSel_Avg','nmrJ.ALL');" title='J-couplings: ALL'>ALL</a></li><br/><br/>
                </ul>
		</td>
		<td id="Rib_Avg_images" rowspan="6">
                <img id="Rib_Avg" src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/desoxyribose_trans.png" width="300" align="center" class="unhidden">
                <img id="J1p2ppSel_AvgRib" src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/desoxyribose_J1p2pp_trans.png" width="300" align="center" class="hidden">
                <img id="J1p2pSel_AvgRib" src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/desoxyribose_J1p2p_trans.png" width="300" align="center" class="hidden">
                <img id="J2pp3pSel_AvgRib" src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/desoxyribose_J2pp3p_trans.png" width="300" align="center" class="hidden">
                <img id="J2p3pSel_AvgRib" src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/desoxyribose_J2p3p_trans.png" width="300" align="center" class="hidden">
                <img id="J3p4pSel_AvgRib" src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/desoxyribose_J3p4p_trans.png" width="300" align="center" class="hidden">
		<p><i style="font-size:12pt">DNA 2-Deoxyribose</i></p>
		</td></tr>
		</table>
        </div>
	</div>

	<div id="Time_Params">

        <div id="Proton_Pairs_Params" class="hidden" style="text-align:center;">
		<table align="" cellpadding="50">
		<tr>
                <td style="vertical-align:top; text-align:left;">
                <ul>
		  <li class="curvesText"><a id='J1p2ppSel' class='unselected' href="javascript:unhideSelNmrPlots('J1p2ppSel','J1p2pp-DNATimeSelPlot');" title='J-couplings: J1p2pp'>Jcoupling: H1'-H2''</a></li><br/><br/>
                  <li class="curvesText"><a id='J1p2pSel' class='unselected' href="javascript:unhideSelNmrPlots('J1p2pSel','J1p2p-DNATimeSelPlot');" title='J-couplings: J1p2p'>Jcoupling: H1'-H2'</a></li><br/><br/>
		  <li class="curvesText"><a id='J2pp3pSel' class='unselected' href="javascript:unhideSelNmrPlots('J2pp3pSel','J2pp3p-DNATimeSelPlot');" title='J-couplings: J2pp3p'>Jcoupling: H2''-H3'</a></li><br/><br/>
	          <li class="curvesText"><a id='J2p3pSel' class='unselected' href="javascript:unhideSelNmrPlots('J2p3pSel','J2p3p-DNATimeSelPlot');" title='J-couplings: J2p3p'>Jcoupling: H2'-H3'</a></li><br/><br/>
        	  <li class="curvesText"><a id='J3p4pSel' class='unselected' href="javascript:unhideSelNmrPlots('J3p4pSel','J3p4p-DNATimeSelPlot');" title='J-couplings: J3p4p'>Jcoupling: H3'-H4'</a></li><br/><br/>
                </ul>

		</td>
		<td id="Rib_images" rowspan="6">
                <img id="Rib" src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/desoxyribose_trans.png" width="300" align="center" class="unhidden">
                <img id="J1p2pp-DNASelRib" src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/desoxyribose_J1p2pp_trans.png" width="300" align="center" class="hidden">
                <img id="J1p2p-DNASelRib" src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/desoxyribose_J1p2p_trans.png" width="300" align="center" class="hidden">
                <img id="J2pp3p-DNASelRib" src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/desoxyribose_J2pp3p_trans.png" width="300" align="center" class="hidden">
                <img id="J2p3p-DNASelRib" src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/desoxyribose_J2p3p_trans.png" width="300" align="center" class="hidden">
                <img id="J3p4p-DNASelRib" src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/desoxyribose_J3p4p_trans.png" width="300" align="center" class="hidden">
		<p><i style="font-size:12pt">DNA 2-Deoxyribose</i></p>
		</td></tr>
		</table>
        </div>
        </div>
	
	<!-- PLOTS -->

        <div id="NmrParams">

        <div id="HelicalParamsPlots">

                <div id="J1p2pp-DNAAvgSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;">
			<?php plotAVG ($userPath,"J1p2pp-DNA_avg"); ?>
                </div>
                <div id="J1p2p-DNAAvgSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;">
			<?php plotAVG ($userPath,"J1p2p-DNA_avg"); ?>
                </div>
                <div id="J2pp3p-DNAAvgSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;">
			<?php plotAVG ($userPath,"J2pp3p-DNA_avg"); ?>
                </div>
                <div id="J2p3p-DNAAvgSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;">
			<?php plotAVG ($userPath,"J2p3p-DNA_avg"); ?>
                </div>
                <div id="J3p4p-DNAAvgSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;">
			<?php plotAVG ($userPath,"J3p4p-DNA_avg"); ?>
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
				# 18 J1p2pp-DNA    5.320    1.504
				$line = $lines[$cont];
				if(preg_match('/^#/',$line)) continue;
				$arr = preg_split('/\s+/',$line);
				print "<tr><td>$arr[0]</td><td>$arr[1]</td><td>$arr[2]</td><td>$arr[3]</td></tr>\n";
			}
			print "</table>\n";
	?>

		<table align="" border="0"><tr><td>
		<a href="<?php echo $GLOBALS['BASEURL']; ?>tools/naflex/getFile.php?fileloc=<?php echo $userPath."/Jcoupling.stats" ?>&type=curves" class="btn blue"> Download Raw Data</a>
		</td></tr></table>
	</div>
	</div>
	</div>

        <div id="HelicalParamsPlotsTime">

        <div id="Time_helical_bpstep">
        <div id="J1p2pp-DNATimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="J1p2p-DNATimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="J2pp3p-DNATimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="J2p3p-DNATimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="J3p4p-DNATimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        </div>

	</div>

	<br/><br/>	
