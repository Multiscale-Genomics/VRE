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
					$k = $j+1;
					$code = "$j-$letter1";
					$code2 = "$k-$letter2";
					$codeBP = "$code:$code2";
					logger("Seq1: -$letter1-");
                                        if($letter1 == 'X'){
                                                echo "<td id='divTableSeq1' class='divTableSeq' title='Unrecognized Nucleotide'>$letter1</td>\n";
                                        }
                                        else{
						echo "<td id='divTableSeq1' class='divTableSeq'><a id='$code' class='unselected' href=\"javascript:selectNucleotideNew('nmrNOE','$code','$userPath');\" title='Nucleotide Helical Parameters'> $letter1 </a></td>\n";
					}
					if($i != $length1-1)
						echo "<td id='divTableSeq1Seps' class='divTableSeqSeps'><a id='$codeBP' name='BS' class='unselected' href=\"javascript:selectNucleotideNew('nmrNOE','$codeBP','$userPath');javascript:selectBaseStep('$codeBP');\" title='Backbone Helical Parameters'> - </a></td>\n";
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
						echo "<td id='divTableSepsUpDown' class='divTableSeqSepsX'><a id='$codeBPS' name='BPS' class='unselected' href=\"javascript:selectNucleotideNew('nmrNOE','$codeBPS','$userPath');javascript:selectBasePairStep('$codeBPS','$length1');\" title='Base Pair Helical Parameters'> | </a></td>\n";
					if($i > 0 and $i < $length1 - 2)
						echo "<td id='divTableSepsX' class='divTableSeqSepsX' ><a id='$codeTet' name='TET' class='unselected' href=\"javascript:selectNucleotideNew('nmrNOE','$codeTet','$userPath');javascript:selectTetramer('$codeTet','$length1');\" title='Base Pair Step Helical Parameters'> x </a></td>\n";
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
						if($duplex) { echo "<td id='divTableSeq2' class='divTableSeq'><a id='$code' class='unselected' href=\"javascript:selectNucleotideNew('nmrNOE','$code','$userPath');\" title='Nucleotide Helical Parameters'> $letter1 </a></td>\n";}
						else { echo "<td id='divTableSeq2' class='divTableSeq'> $letter1 </td>\n";}
					}
					if($i != $length2-1)
						if($duplex) { echo "<td id='divTableSeq2Seps' class='divTableSeqSeps'><a id='$codeBP' name='BS' class='unselected' href=\"javascript:selectNucleotideNew('nmrNOE','$codeBP','$userPath');javascript:selectBaseStep('$codeBP');\" title='Backbone Helical Parameters'> - </a></td>\n";}
						else { echo "<td id='divTableSeq2Seps' class='divTableSeqSeps'><a id='' class='unselected'> - </a></td>\n";}
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
                        <td class="curvesText"><a id='INTSel' class='unselected btn blue' href="javascript:unhideNmrIntensities('INTSel','IntensitiesPlot');" title='NOE intensities'>NOE Intensities</a></td>
                        <td width="50"></td>
                        <td class="curvesText"><a id='AVGSel' class='unselected btn blue' href="javascript:unhideNmrAvgPlots('AVGSel','Proton_Pairs_Avg');" title='AVG Results'>Average Results</a></td>
			<td width="50"></td>
                        <td class="curvesText"><a id='TIMESel' class='unselected btn blue' href="javascript:unhideNmrTimePlots('TIMESel','Proton_Pairs_Params');" title='TIME Results'>Results by Time</a></td>
		</tr>
	</table>
	</div>

	<div id="Avg_Params">

	<form name="Avg_Params_Form" action="<?php echo $GLOBALS['BASEURL']; ?>tools/naflex/showPlot.php" method="post" target="_blank">

	<!--
	<input type="hidden" name="user" value="$user" />
	<input type="hidden" name="proj" value="$project" />
	<input type="hidden" name="op" value="$op" />
	<input type="hidden" name="type" value="$analysis" />
	-->
	<input type="hidden" name="path" value="<?php echo $path ?>" />

        <div id="Proton_Pairs_Avg" class="hidden" style="text-align:left;">
		<table align="" cellpadding="0" width="950" style="margin-left:40px;">
		<tr><td style="vertical-align:top; text-align:left;">
                  <p class="curvesText"><a id='J1p2pSel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('J1p2pSel_Avg','J1p2p-DNAAvgSelPlot');" title='NOEs: J1p2p'>H1'-H2'</a></p>&nbsp;&nbsp;<input type="checkbox" id="checkSugar_H1p-H2p" name="avg[H1p-H2p]"/><br/>
                  <p class="curvesText"><a id='J1p2ppSel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('J1p2ppSel_Avg','J1p2pp-DNAAvgSelPlot');" title='NOEs: J1p2pp'>H1'-H2''</a></p>&nbsp;&nbsp;<input type="checkbox" id="checkSugar_H1p-H2pp" name="avg[H1p-H2pp]"/><br/>
                  <p class="curvesText"><a id='J1p3pSel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('J1p3pSel_Avg','J1p3p-DNAAvgSelPlot');" title='NOEs: J1p3p'>H1'-H3'</a></p>&nbsp;&nbsp;<input type="checkbox" id="checkSugar_H1p-H3p" name="avg[H1p-H3p]"/><br/>
                  <p class="curvesText"><a id='J1p4pSel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('J1p4pSel_Avg','J1p4p-DNAAvgSelPlot');" title='NOEs: J1p4p'>H1'-H4'</a></p>&nbsp;&nbsp;<input type="checkbox" id="checkSugar_H1p-H4p" name="avg[H1p-H4p]"/><br/>
	          <p class="curvesText"><a id='J2p3pSel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('J2p3pSel_Avg','J2p3p-DNAAvgSelPlot');" title='NOEs: J2p3p'>H2'-H3'</a></p>&nbsp;&nbsp;<input type="checkbox" id="checkSugar_H2p-H3p" name="avg[H2p-H3p]"/><br/>
	          <p class="curvesText"><a id='J2p4pSel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('J2p4pSel_Avg','J2p4p-DNAAvgSelPlot');" title='NOEs: J2p4p'>H2'-H4'</a></p>&nbsp;&nbsp;<input type="checkbox" id="checkSugar_H2p-H4p" name="avg[H2p-H4p]"/><br/>
	          <p class="curvesText"><a id='J2pp3pSel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('J2pp3pSel_Avg','J2pp3p-DNAAvgSelPlot');" title='NOEs: J2pp3p'>H2''-H3'</a></p>&nbsp;&nbsp;<input type="checkbox" id="checkSugar_H2pp-H3p" name="avg[H2pp-H3p]"/><br/>
	          <p class="curvesText"><a id='J2pp4pSel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('J2pp4pSel_Avg','J2pp4p-DNAAvgSelPlot');" title='NOEs: J2pp4p'>H2''-H4'</a></p>&nbsp;&nbsp;<input type="checkbox" id="checkSugar_H2pp-H4p" name="avg[H2pp-H4p]"/><br/>
        	  <p class="curvesText"><a id='J3p4pSel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('J3p4pSel_Avg','J3p4p-DNAAvgSelPlot');" title='NOEs: J3p4p'>H3'-H4'</a></p>&nbsp;&nbsp;<input type="checkbox" id="checkSugar_H3p-H4p" name="avg[H3p-H4p]"/><br/>
		</td><td style="vertical-align:top; text-align:left;">
        	  <p class="curvesText"><a id='J1p68Sel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('J1p68Sel_Avg','J1p68-DNAAvgSelPlot');" title='NOEs: J1p68'>H1'-H6/H8</a></p>&nbsp;&nbsp;<input type="checkbox" id="checkBase_H1p-H6H8" name="avg[H1p-H6H8]"/><br/>
        	  <p class="curvesText"><a id='J2p68Sel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('J2p68Sel_Avg','J2p68-DNAAvgSelPlot');" title='NOEs: J2p68'>H2'-H6/H8</a></p>&nbsp;&nbsp;<input type="checkbox" id="checkBase_H2p-H6H8" name="avg[H2p-H6H8]"/><br/>
        	  <p class="curvesText"><a id='J2pp68Sel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('J2pp68Sel_Avg','J2pp68-DNAAvgSelPlot');" title='NOEs: J2pp68'>H2''-H6/H8</a></p>&nbsp;&nbsp;<input type="checkbox" id="checkBase_H2pp-H6H8" name="avg[H2pp-H6H8]"/><br/>
        	  <p class="curvesText"><a id='J3p68Sel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('J3p68Sel_Avg','J3p68-DNAAvgSelPlot');" title='NOEs: J3p68'>H3'-H6/H8</a></p>&nbsp;&nbsp;<input type="checkbox" id="checkBase_H3p-H6H8" name="avg[H3p-H6H8]"/><br/>
        	  <p class="curvesText"><a id='J4p68Sel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('J4p68Sel_Avg','J4p68-DNAAvgSelPlot');" title='NOEs: J4p68'>H4'-H6/H8</a></p>&nbsp;&nbsp;<input type="checkbox" id="checkBase_H4p-H6H8" name="avg[H4p-H6H8]"/><br/>
        	  <p class="curvesText"><a id='J56Sel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('J56Sel_Avg','J56-DNAAvgSelPlot');" title='NOEs: J56'>H5-H6</a></p>&nbsp;&nbsp;<input type="checkbox" id="checkBase_H5-H6" name="avg[H5-H6]"/>
		</td><td style="vertical-align:top; text-align:left;">
        	  <p class="curvesText"><a id='J1p68-StepSel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('J1p68-StepSel_Avg','J1p68-Step-DNAAvgSelPlot');" title='NOEs: J1p68'>H1'-H6/H8 (+1)</a></p>&nbsp;&nbsp;<input type="checkbox" id="checkStep_H1p-H6H8" name="avg[H1p-H6H8-Step]"/><br/>
        	  <p class="curvesText"><a id='J2p68-StepSel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('J2p68-StepSel_Avg','J2p68-Step-DNAAvgSelPlot');" title='NOEs: J2p68'>H2'-H6/H8 (+1)</a></p>&nbsp;&nbsp;<input type="checkbox" id="checkStep_H2p-H6H8" name="avg[H2p-H6H8-Step]"/><br/>
        	  <p class="curvesText"><a id='J2pp68-StepSel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('J2pp68-StepSel_Avg','J2pp68-Step-DNAAvgSelPlot');" title='NOEs: J2pp68'>H2''-H6/H8 (+1)</a></p>&nbsp;&nbsp;<input type="checkbox" id="checkStep_H2pp-H6H8" name="avg[H2pp-H6H8-Step]"/><br/>
        	  <p class="curvesText"><a id='J3p68-StepSel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('J3p68-StepSel_Avg','J3p68-Step-DNAAvgSelPlot');" title='NOEs: J3p68'>H3'-H6/H8 (+1)</a></p>&nbsp;&nbsp;<input type="checkbox" id="checkStep_H3p-H6H8" name="avg[H3p-H6H8-Step]"/><br/>
        	  <p class="curvesText"><a id='J4p68-StepSel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('J4p68-StepSel_Avg','J4p68-Step-DNAAvgSelPlot');" title='NOEs: J4p68'>H4'-H6/H8 (+1)</a></p>&nbsp;&nbsp;<input type="checkbox" id="checkStep_H4p-H6H8" name="avg[H4p-H6H8-Step]"/><br/>
        	  <p class="curvesText"><a id='ALLSel_Avg' class='unselected' href="javascript:unhideSelNmrALL('ALLSel_Avg','nmrJ.ALL');" title='NOEs: ALL'>ALL</a></p>
		</td>
		<td id="Rib_Avg_images" rowspan="6" style="vertical-align:top; text-align:left;margin-left:40px">
                <p id="Rib_Avg" class="unhidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/desoxyribose_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/desoxyribose_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>DNA 2-Deoxyribose</i></p>
                <p id="J1p2ppSel_AvgRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/desoxyribose_J1p2pp_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/desoxyribose_J1p2pp_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>DNA 2-Deoxyribose</i></p>
                <p id="J1p2pSel_AvgRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/desoxyribose_J1p2p_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/desoxyribose_J1p2p_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>DNA 2-Deoxyribose</i></p>
                <p id="J2pp3pSel_AvgRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/desoxyribose_J2pp3p_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/desoxyribose_J2pp3p_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>DNA 2-Deoxyribose</i></p>
                <p id="J2p3pSel_AvgRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/desoxyribose_J2p3p_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/desoxyribose_J2p3p_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>DNA 2-Deoxyribose</i></p>
                <p id="J2p4pSel_AvgRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/desoxyribose_J2p4p_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/desoxyribose_J2p4p_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>DNA 2-Deoxyribose</i></p>
                <p id="J2pp4pSel_AvgRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/desoxyribose_J2pp4p_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/desoxyribose_J2pp4p_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>DNA 2-Deoxyribose</i></p>
                <p id="J3p4pSel_AvgRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/desoxyribose_J3p4p_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/desoxyribose_J3p4p_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>DNA 2-Deoxyribose</i></p>
                <p id="J1p3pSel_AvgRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/desoxyribose_J1p3p_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/desoxyribose_J1p3p_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>DNA 2-Deoxyribose</i></p>
                <p id="J1p4pSel_AvgRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/desoxyribose_J1p4p_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/desoxyribose_J1p4p_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>DNA 2-Deoxyribose</i></p>
                <p id="J56Sel_AvgRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/DPyrimidine_56_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/DPyrimidine_56_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>DNA Pyrimidine (segment)</i></p>
                <p id="J1p68Sel_AvgRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/D_1p68_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/D_1p68_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>DNA Pyrimidine/Purine (segment)</i></p>
                <p id="J2p68Sel_AvgRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/D_2p68_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/D_2p68_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>DNA Pyrimidine/Purine (segment)</i></p>
                <p id="J2pp68Sel_AvgRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/D_2pp68_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/D_2pp68_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>DNA Pyrimidine/Purine (segment)</i></p>
                <p id="J3p68Sel_AvgRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/D_3p68_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/D_3p68_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>DNA Pyrimidine/Purine (segment)</i></p>
                <p id="J4p68Sel_AvgRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/D_4p68_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/D_4p68_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>DNA Pyrimidine/Purine (segment)</i></p>
                <p id="J1p68-StepSel_AvgRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/D_1p68_Step_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/D_1p68_Step_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>DNA Pyrimidine/Purine (segment)</i></p>
                <p id="J2p68-StepSel_AvgRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/D_2p68_Step_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/D_2p68_Step_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>DNA Pyrimidine/Purine (segment)</i></p>
                <p id="J2pp68-StepSel_AvgRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/D_2pp68_Step_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/D_2pp68_Step_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>DNA Pyrimidine/Purine (segment)</i></p>
                <p id="J3p68-StepSel_AvgRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/D_3p68_Step_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/D_3p68_Step_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>DNA Pyrimidine/Purine (segment)</i></p>
                <p id="J4p68-StepSel_AvgRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/D_4p68_Step_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/D_4p68_Step_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>DNA Pyrimidine/Purine (segment)</i></p>

		<br/><br/>

		</td></tr>
		</table>

        <table border="0" align="" style="text-align:center;margin-left:40px;">
                <tr><td>
                <p class="btn green" style="margin-right:1px;" onClick="javascript:checkSugar('Proton_Pairs_Avg');">Sugar-Sugar</p>
                </td><td>
                <p class="btn green" style="margin-right:1px;" onClick="javascript:checkBase('Proton_Pairs_Avg');">Sugar-Base</p>
                </td><td>
                <p class="btn green" style="margin-right:1px;" onClick="javascript:checkStep('Proton_Pairs_Avg');">Sugar-Base-Step</p>
                </td><td>
                <p class="btn green" style="margin-right:1px;" onClick="javascript:checkProton('Proton_Pairs_Avg','H1p');">Check H1'</p>
                </td><td>
                <p class="btn green" style="margin-right:1px;" onClick="javascript:checkProton('Proton_Pairs_Avg','H2p');">Check H2'</p>
                </td><td>
								<p class="btn green" style="margin-right:1px;" onClick="javascript:checkProton('Proton_Pairs_Avg','H2pp');">Check H2''</p>
                </td><td>
                <p class="btn green" style="margin-right:1px;" onClick="javascript:checkProton('Proton_Pairs_Avg','H3p');">Check H3'</p>
                </td><td>
                <p class="btn green" style="margin-right:1px;" onClick="javascript:checkProton('Proton_Pairs_Avg','H4p');">Check H4'</p>
                </td><td>
                <p class="btn green" style="margin-right:1px;" onClick="javascript:uncheckAll('Proton_Pairs_Avg');">Uncheck All</p>
                </td></tr>
        </table>

        <input class="btn blue" type="submit" value="Show Selected Observables" style="margin:20px 0 0 40px;">

        </div>
	</form>
	</div>

	<div id="Time_Params">

        <div id="Proton_Pairs_Params" class="hidden" style="text-align:center;">
		<table align="" cellpadding="0" width="950" style="margin-left:40px;">
		<tr><td style="vertical-align:top; text-align:left;">
		  <p class="curvesText"><a id='Nuc_H1p-Nuc_H2pSel' class='unselected' href="javascript:unhideSelNmrPlots('Nuc_H1p-Nuc_H2pSel','Nuc_H1p-Nuc_H2pTimeSelPlot');" title='NOEs: H1p-H2p'>H1'-H2'</a></p><br/>
		  <p class="curvesText"><a id='Nuc_H1p-Nuc_H2ppSel' class='unselected' href="javascript:unhideSelNmrPlots('Nuc_H1p-Nuc_H2ppSel','Nuc_H1p-Nuc_H2ppTimeSelPlot');" title='NOEs: H1p-H2pp'>H1'-H2''</a></p><br/>
		  <p class="curvesText"><a id='Nuc_H1p-Nuc_H3pSel' class='unselected' href="javascript:unhideSelNmrPlots('Nuc_H1p-Nuc_H3pSel','Nuc_H1p-Nuc_H3pTimeSelPlot');" title='NOEs: H1p-H3p'>H1'-H3'</a></p><br/>
		  <p class="curvesText"><a id='Nuc_H1p-Nuc_H4pSel' class='unselected' href="javascript:unhideSelNmrPlots('Nuc_H1p-Nuc_H4pSel','Nuc_H1p-Nuc_H4pTimeSelPlot');" title='NOEs: H1p-H4p'>H1'-H4'</a></p><br/>
		  <p class="curvesText"><a id='Nuc_H2p-Nuc_H3pSel' class='unselected' href="javascript:unhideSelNmrPlots('Nuc_H2p-Nuc_H3pSel','Nuc_H2p-Nuc_H3pTimeSelPlot');" title='NOEs: H2p-H3p'>H2'-H3'</a></p><br/>
		  <p class="curvesText"><a id='Nuc_H2pp-Nuc_H3pSel' class='unselected' href="javascript:unhideSelNmrPlots('Nuc_H2pp-Nuc_H3pSel','Nuc_H2pp-Nuc_H3pTimeSelPlot');" title='NOEs: H2pp-H3p'>H2''-H3'</a></p><br/>
		  <p class="curvesText"><a id='Nuc_H2p-Nuc_H4pSel' class='unselected' href="javascript:unhideSelNmrPlots('Nuc_H2p-Nuc_H4pSel','Nuc_H2p-Nuc_H4pTimeSelPlot');" title='NOEs: H2p-H4p'>H2'-H4'</a></p><br/>
		  <p class="curvesText"><a id='Nuc_H2pp-Nuc_H4pSel' class='unselected' href="javascript:unhideSelNmrPlots('Nuc_H2pp-Nuc_H4pSel','Nuc_H2pp-Nuc_H4pTimeSelPlot');" title='NOEs: H2pp-H4p'>H2''-H4'</a></p><br/>
		  <p class="curvesText"><a id='Nuc_H3p-Nuc_H4pSel' class='unselected' href="javascript:unhideSelNmrPlots('Nuc_H3p-Nuc_H4pSel','Nuc_H3p-Nuc_H4pTimeSelPlot');" title='NOEs: H3p-H4p'>H3'-H4'</a></p><br/>
		</td><td style="vertical-align:top; text-align:left;">
		  <p class="curvesText"><a id='Nuc_H1p-Nuc_H6Sel' class='unselected' href="javascript:unhideSelNmrPlots('Nuc_H1p-Nuc_H6Sel','Nuc_H1p-Nuc_H6TimeSelPlot');" title='NOEs: H1p-H6'>H1'-H6/H8</a></p><br/>
		  <p class="curvesText"><a id='Nuc_H2p-Nuc_H6Sel' class='unselected' href="javascript:unhideSelNmrPlots('Nuc_H2p-Nuc_H6Sel','Nuc_H2p-Nuc_H6TimeSelPlot');" title='NOEs: H2p-H6'>H2'-H6/H8</a></p><br/>
		  <p class="curvesText"><a id='Nuc_H2pp-Nuc_H6Sel' class='unselected' href="javascript:unhideSelNmrPlots('Nuc_H2pp-Nuc_H6Sel','Nuc_H2pp-Nuc_H6TimeSelPlot');" title='NOEs: H2pp-H6'>H2''-H6/H8</a></p><br/>
		  <p class="curvesText"><a id='Nuc_H3p-Nuc_H6Sel' class='unselected' href="javascript:unhideSelNmrPlots('Nuc_H3p-Nuc_H6Sel','Nuc_H3p-Nuc_H6TimeSelPlot');" title='NOEs: H3p-H6'>H3'-H6/H8</a></p><br/>
		  <p class="curvesText"><a id='Nuc_H4p-Nuc_H6Sel' class='unselected' href="javascript:unhideSelNmrPlots('Nuc_H4p-Nuc_H6Sel','Nuc_H4p-Nuc_H6TimeSelPlot');" title='NOEs: H4p-H6'>H4'-H6/H8</a></p><br/>
		  <p class="curvesText_hidden hidden" id="H5-H6"><a id='Nuc_H5-Nuc_H6Sel' class='unselected' href="javascript:unhideSelNmrPlots('Nuc_H5-Nuc_H6Sel','Nuc_H5-Nuc_H6TimeSelPlot');" title='NOEs: H5-H6'>H5-H6</a></p><br/>
		</td><td style="vertical-align:top; text-align:left;">
		  <p class="curvesText"><a id='Nuc_H1p-Nuc_H6-StepSel' class='unselected' href="javascript:unhideSelNmrPlots('Nuc_H1p-Nuc_H6-StepSel','Nuc_H1p-Nuc_H6-StepTimeSelPlot');" title='NOEs: H1p-H6-Step'>H1'-H6/H8 (+1)</a></p><br/>
		  <p class="curvesText"><a id='Nuc_H2p-Nuc_H6-StepSel' class='unselected' href="javascript:unhideSelNmrPlots('Nuc_H2p-Nuc_H6-StepSel','Nuc_H2p-Nuc_H6-StepTimeSelPlot');" title='NOEs: H2p-H6-Step'>H2'-H6/H8 (+1)</a></p><br/>
		  <p class="curvesText"><a id='Nuc_H2pp-Nuc_H6-StepSel' class='unselected' href="javascript:unhideSelNmrPlots('Nuc_H2pp-Nuc_H6-StepSel','Nuc_H2pp-Nuc_H6-StepTimeSelPlot');" title='NOEs: H2pp-H6-Step'>H2''-H6/H8 (+1)</a></p><br/>
		  <p class="curvesText"><a id='Nuc_H3p-Nuc_H6-StepSel' class='unselected' href="javascript:unhideSelNmrPlots('Nuc_H3p-Nuc_H6-StepSel','Nuc_H3p-Nuc_H6-StepTimeSelPlot');" title='NOEs: H3p-H6-Step'>H3'-H6/H8 (+1)</a></p><br/>
		  <p class="curvesText"><a id='Nuc_H4p-Nuc_H6-StepSel' class='unselected' href="javascript:unhideSelNmrPlots('Nuc_H4p-Nuc_H6-StepSel','Nuc_H4p-Nuc_H6-StepTimeSelPlot');" title='NOEs: H4p-H6-Step'>H4'-H6/H8 (+1)</a></p><br/>
		</td>
		<td id="Rib_images" rowspan="6"  style="vertical-align:top; text-align:left;margin-left:40px">
                <p id="Rib" class="unhidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/desoxyribose_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/desoxyribose_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>DNA 2-Deoxyribose</i></p>
                <p id="Nuc_H1p-Nuc_H2pSelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/desoxyribose_J1p2p_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/desoxyribose_J1p2p_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>DNA 2-Deoxyribose</i></p>
                <p id="Nuc_H1p-Nuc_H2ppSelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/desoxyribose_J1p2pp_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/desoxyribose_J1p2pp_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>DNA 2-Deoxyribose</i></p>
                <p id="Nuc_H1p-Nuc_H3pSelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/desoxyribose_J1p3p_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/desoxyribose_J1p3p_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>DNA 2-Deoxyribose</i></p>
                <p id="Nuc_H1p-Nuc_H4pSelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/desoxyribose_J1p4p_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/desoxyribose_J1p4p_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>DNA 2-Deoxyribose</i></p>
                <p id="T_Nuc_H1p-Nuc_H6SelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/DT_1p6_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/DT_1p6_trans.png" border="0" width="400" align="center"></a><i style="font-size:12pt"><br/><br/>DNA Thymine</i></p>
                <p id="T_Nuc_H2p-Nuc_H6SelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/DT_2p6_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/DT_2p6_trans.png" border="0" width="400" align="center"></a><i style="font-size:12pt"><br/><br/>DNA Thymine</i></p>
                <p id="T_Nuc_H2pp-Nuc_H6SelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/DT_2pp6_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/DT_2pp6_trans.png" border="0" width="400" align="center"></a><i style="font-size:12pt"><br/><br/>DNA Thymine</i></p>
                <p id="T_Nuc_H3p-Nuc_H6SelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/DT_3p6_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/DT_3p6_trans.png" border="0" width="400" align="center"></a><i style="font-size:12pt"><br/><br/>DNA Thymine</i></p>
                <p id="T_Nuc_H4p-Nuc_H6SelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/DT_4p6_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/DT_4p6_trans.png" border="0" width="400" align="center"></a><i style="font-size:12pt"><br/><br/>DNA Thymine</i></p>
                <p id="C_Nuc_H1p-Nuc_H6SelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/DC_1p6_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/DC_1p6_trans.png" border="0" width="400" align="center"></a><i style="font-size:12pt"><br/><br/>DNA Cytosine</i></p>
                <p id="C_Nuc_H2p-Nuc_H6SelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/DC_2p6_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/DC_2p6_trans.png" border="0" width="400" align="center"></a><i style="font-size:12pt"><br/><br/>DNA Cytosine</i></p>
                <p id="C_Nuc_H2pp-Nuc_H6SelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/DC_2pp6_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/DC_2pp6_trans.png" border="0" width="400" align="center"></a><i style="font-size:12pt"><br/><br/>DNA Cytosine</i></p>
                <p id="C_Nuc_H3p-Nuc_H6SelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/DC_3p6_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/DC_3p6_trans.png" border="0" width="400" align="center"></a><i style="font-size:12pt"><br/><br/>DNA Cytosine</i></p>
                <p id="C_Nuc_H4p-Nuc_H6SelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/DC_4p6_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/DC_4p6_trans.png" border="0" width="400" align="center"></a><i style="font-size:12pt"><br/><br/>DNA Cytosine</i></p>
                <p id="C_Nuc_H5-Nuc_H6SelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/DC_56_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/DC_56_trans.png" border="0" width="400" align="center"></a><i style="font-size:12pt"><br/><br/>DNA Cytosine</i></p>
                <p id="G_Nuc_H1p-Nuc_H8SelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/DG_1p8_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/DG_1p8_trans.png" border="0" width="400" align="center"></a><i style="font-size:12pt"><br/><br/>DNA Guanine</i></p>
                <p id="G_Nuc_H2p-Nuc_H8SelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/DG_2p8_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/DG_2p8_trans.png" border="0" width="400" align="center"></a><i style="font-size:12pt"><br/><br/>DNA Guanine</i></p>
                <p id="G_Nuc_H2pp-Nuc_H8SelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/DG_2pp8_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/DG_2pp8_trans.png" border="0" width="400" align="center"></a><i style="font-size:12pt"><br/><br/>DNA Guanine</i></p>
                <p id="G_Nuc_H3p-Nuc_H8SelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/DG_3p8_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/DG_3p8_trans.png" border="0" width="400" align="center"></a><i style="font-size:12pt"><br/><br/>DNA Guanine</i></p>
                <p id="G_Nuc_H4p-Nuc_H8SelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/DG_4p8_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/DG_4p8_trans.png" border="0" width="400" align="center"></a><i style="font-size:12pt"><br/><br/>DNA Guanine</i></p>
                <p id="A_Nuc_H1p-Nuc_H8SelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/DA_1p8_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/DA_1p8_trans.png" border="0" width="400" align="center"></a><i style="font-size:12pt"><br/><br/>DNA Adenine</i></p>
                <p id="A_Nuc_H2p-Nuc_H8SelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/DA_2p8_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/DA_2p8_trans.png" border="0" width="400" align="center"></a><i style="font-size:12pt"><br/><br/>DNA Adenine</i></p>
								<p id="A_Nuc_H2pp-Nuc_H8SelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/DA_2pp8_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/DA_2pp8_trans.png" border="0" width="400" align="center"></a><i style="font-size:12pt"><br/><br/>DNA Adenine</i></p>
                <p id="A_Nuc_H3p-Nuc_H8SelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/DA_3p8_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/DA_3p8_trans.png" border="0" width="400" align="center"></a><i style="font-size:12pt"><br/><br/>DNA Adenine</i></p>
                <p id="A_Nuc_H4p-Nuc_H8SelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/DA_4p8_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/DA_4p8_trans.png" border="0" width="400" align="center"></a><i style="font-size:12pt"><br/><br/>DNA Adenine</i></p>
                <p id="Nuc_H2p-Nuc_H3pSelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/desoxyribose_J2p3p_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/desoxyribose_J2p3p_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>DNA 2-Deoxyribose</i></p>
                <p id="Nuc_H2pp-Nuc_H3pSelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/desoxyribose_J2pp3p_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/desoxyribose_J2pp3p_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>DNA 2-Deoxyribose</i></p>
                <p id="Nuc_H2p-Nuc_H4pSelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/desoxyribose_J2p4p_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/desoxyribose_J2p4p_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>DNA 2-Deoxyribose</i></p>
                <p id="Nuc_H2pp-Nuc_H4pSelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/desoxyribose_J2pp4p_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/desoxyribose_J2pp4p_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>DNA 2-Deoxyribose</i></p>
                <p id="Nuc_H3p-Nuc_H4pSelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/desoxyribose_J3p4p_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/desoxyribose_J3p4p_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>DNA 2-Deoxyribose</i></p>
                <p id="Nuc_H1p-Nuc_H6-StepSelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/DPurines_Pyrimidines_1p6_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/DPurines_Pyrimidines_1p6_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>* - Pyrimidine (Segment)</i></p>
                <p id="Nuc_H2p-Nuc_H6-StepSelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/DPurines_Pyrimidines_2p6_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/DPurines_Pyrimidines_2p6_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>* - Pyrimidine (Segment)</i></p>
                <p id="Nuc_H2pp-Nuc_H6-StepSelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/DPurines_Pyrimidines_2pp6_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/DPurines_Pyrimidines_2pp6_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>* - Pyrimidine (Segment)</i></p>
                <p id="Nuc_H3p-Nuc_H6-StepSelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/DPurines_Pyrimidines_3p6_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/DPurines_Pyrimidines_3p6_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>* - Pyrimidine (Segment)</i></p>
                <p id="Nuc_H4p-Nuc_H6-StepSelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/DPurines_Pyrimidines_4p6_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/DPurines_Pyrimidines_4p6_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>* - Pyrimidine (Segment)</i></p>
                <p id="Nuc_H1p-Nuc_H8-StepSelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/DPyrimidines_Purines_1p8_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/DPyrimidines_Purines_1p8_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>* - Purine (Segment)</i></p>
                <p id="Nuc_H2p-Nuc_H8-StepSelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/DPyrimidines_Purines_2p8_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/DPyrimidines_Purines_2p8_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>* - Purine (Segment)</i></p>
                <p id="Nuc_H2pp-Nuc_H8-StepSelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/DPyrimidines_Purines_2pp8_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/DPyrimidines_Purines_2pp8_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>* - Purine (Segment)</i></p>
                <p id="Nuc_H3p-Nuc_H8-StepSelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/DPyrimidines_Purines_3p8_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/DPyrimidines_Purines_3p8_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>* - Purine (Segment)</i></p>
                <p id="Nuc_H4p-Nuc_H8-StepSelRib" class="hidden"><a href="javascript:openIMG('<?php echo $GLOBALS['BASEURL'].'tools/naflex'; ?>/images/DPyrimidines_Purines_4p8_trans.png');" target="_blank"><img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/DPyrimidines_Purines_4p8_trans.png" border="0" width="300" align="center"></a><i style="font-size:12pt"><br/><br/>* - Purine (Segment)</i></p>
		</td></tr>
		</table>
        </div>
        </div>
	
	<!-- PLOTS -->

        <div id="NmrParams">

        <div id="HelicalParamsPlots">

                <div id="J1p2p-DNAAvgSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;">
			<?php plotAVG ($userPath,"H1p-H2p.avg"); ?>
                </div>
                <div id="J1p2pp-DNAAvgSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;">
			<?php plotAVG ($userPath,"H1p-H2pp.avg"); ?>
                </div>
                <div id="J1p3p-DNAAvgSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;">
			<?php plotAVG ($userPath,"H1p-H3p.avg"); ?>
                </div>
                <div id="J1p4p-DNAAvgSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;">
			<?php plotAVG ($userPath,"H1p-H4p.avg"); ?>
                </div>
                <div id="J1p68-DNAAvgSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;">
			<?php plotAVG ($userPath,"H1p-H6H8.avg"); ?>
                </div>
                <div id="J2p3p-DNAAvgSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;">
			<?php plotAVG ($userPath,"H2p-H3p.avg"); ?>
                </div>
                <div id="J2p4p-DNAAvgSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;">
			<?php plotAVG ($userPath,"H2p-H4p.avg"); ?>
                </div>
                <div id="J2pp4p-DNAAvgSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;">
			<?php plotAVG ($userPath,"H2pp-H4p.avg"); ?>
                </div>
                <div id="J2pp3p-DNAAvgSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;">
			<?php plotAVG ($userPath,"H2pp-H3p.avg"); ?>
                </div>
                <div id="J2p68-DNAAvgSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;">
			<?php plotAVG ($userPath,"H2p-H6H8.avg"); ?>
                </div>
                <div id="J2pp68-DNAAvgSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;">
			<?php plotAVG ($userPath,"H2pp-H6H8.avg"); ?>
                </div>
                <div id="J3p4p-DNAAvgSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;">
			<?php plotAVG ($userPath,"H3p-H4p.avg"); ?>
                </div>
                <div id="J3p68-DNAAvgSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;">
			<?php plotAVG ($userPath,"H3p-H6H8.avg"); ?>
                </div>
                <div id="J4p68-DNAAvgSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;">
			<?php plotAVG ($userPath,"H4p-H6H8.avg"); ?>
                </div>
                <div id="J56-DNAAvgSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;">
			<?php plotAVG ($userPath,"H5-H6.avg"); ?>
                </div>
                <div id="J1p68-Step-DNAAvgSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;">
			<?php plotAVG ($userPath,"H1p-H6H8-Step.avg"); ?>
                </div>
                <div id="J2p68-Step-DNAAvgSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;">
			<?php plotAVG ($userPath,"H2p-H6H8-Step.avg"); ?>
                </div>
                <div id="J2pp68-Step-DNAAvgSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;">
			<?php plotAVG ($userPath,"H2pp-H6H8-Step.avg"); ?>
                </div>
                <div id="J3p68-Step-DNAAvgSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;">
			<?php plotAVG ($userPath,"H3p-H6H8-Step.avg"); ?>
                </div>
                <div id="J4p68-Step-DNAAvgSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;">
			<?php plotAVG ($userPath,"H4p-H6H8-Step.avg"); ?>
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
			$cmd = "cat NOE.stats";
			$out = exec($cmd,$lines);
			$length = count($lines);

			print "<table cellpadding='15' align='center' border='0' id='sortableTable' class='sortable tableNMR'>\n";
			print "<tr><td>Nucleotide Number</td><td>NOE</td><td>Mean</td><td>Stdev</td></tr>\n";

			for($cont=0;$cont<$length;$cont++){
				# 18 J1p2pp-DNA    5.320    1.504
				$line = $lines[$cont];
				if(preg_match('/^#/',$line)) continue;
				$arr = preg_split('/\s+/',$line);
			$mean = sprintf("%8.2f",$arr[2]);
			$stdev = sprintf("%8.2f",$arr[3]);
				#print "<tr><td>$arr[0]</td><td>$arr[1]</td><td>$arr[2]</td><td>$arr[3]</td></tr>\n";
				print "<tr><td>$arr[0]</td><td>$arr[1]</td><td>$mean</td><td>$stdev</td></tr>\n";
			}
			print "</table>\n";
	?>

		<table align="" border="0"><tr><td>
		<a href="<?php echo $GLOBALS['BASEURL']; ?>tools/naflex/getFile.php?fileloc=<?php echo $userPath."/NOE.stats" ?>&type=curves" class="btn blue"> Download Raw Data</a>
		</td></tr></table>
	</div>
	</div>
	</div>

        <div id="HelicalParamsPlotsTime">

        <div id="Time_helical_bpstep">
        <div id="Nuc_H1p-Nuc_H2pTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="Nuc_H1p-Nuc_H2ppTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="Nuc_H1p-Nuc_H3pTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="Nuc_H1p-Nuc_H4pTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="Nuc_H1p-Nuc_H6TimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="Nuc_H1p-Nuc_H8TimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="Nuc_H2p-Nuc_H3pTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="Nuc_H2p-Nuc_H4pTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="Nuc_H2p-Nuc_H6TimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="Nuc_H2pp-Nuc_H3pTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="Nuc_H2pp-Nuc_H4pTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="Nuc_H2pp-Nuc_H6TimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="Nuc_H3p-Nuc_H4pTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="Nuc_H3p-Nuc_H6TimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="Nuc_H4p-Nuc_H6TimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="Nuc_H5-Nuc_H6TimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="Nuc_H1p-Nuc_H6-StepTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="Nuc_H2p-Nuc_H6-StepTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="Nuc_H2pp-Nuc_H6-StepTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="Nuc_H3p-Nuc_H6-StepTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="Nuc_H4p-Nuc_H6-StepTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="Nuc_H1p-Nuc_H8-StepTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="Nuc_H2p-Nuc_H8-StepTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="Nuc_H2pp-Nuc_H8-StepTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="Nuc_H3p-Nuc_H8-StepTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="Nuc_H4p-Nuc_H8-StepTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        </div>

	</div>

        <div id="IntensitiesPlot" class="hidden" align="center">
                <?php
                        $dat = "NOE.intMat.out";
                        $file = "NOE.intMat.png";

                        $cmd = "cat intParams.txt";
                        $out = exec($cmd,$params);
                        $length = count($params);

                        print "<table cellpadding='15' align='center' border='0' id='NOE_IntParams' class='tableNMR'>\n";
                        for($cont=0;$cont<$length;$cont++){
                                $arr = preg_split('/:/',$params[$cont]);
                                print "<tr><td>$arr[0]</td><td>$arr[1]</td></tr>\n";
                        }
                        print "</table>\n";
                ?>

                <table><tr>
                <td><b>Hide Proton Pairs belonging to the same Nucleotide:</b><input id="intProtonPairs" type="checkbox" onclick="intPlotExt()" ></td>
                </tr></table>
                <br/><br/>

                <img border='1' width='700' src="<?php echo "$userPath/$file" ?>" border="0" width="300" align="center">

                        <table align="" border="0" style="margin-top:30px;">
                        <tr><!--<td>
                        <p align="right" class="curvesDatText" onClick="window.open('<?php echo $GLOBALS[homeURL] ?>/<?php echo "$userPath/$file" ?>','', '_blank,resize=1,width=800,height=400');">Open in New Window</p><br/>
                        </td>--><td>
                        <a href="<?php echo $GLOBALS['BASEURL']; ?>tools/naflex/getFile.php?fileloc=<?php echo "$userPath/$dat" ?>&type=curves" class="btn blue"> Download Raw Data</a>
                        </td></tr>
                        </table>
        </div>

	<br/><br/>	
