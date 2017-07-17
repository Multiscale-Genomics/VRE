<?php

  # Finding out if Analysis is Local (we want initial and ending nucleotide)

	$cmd = "ls *.stats";
	$out = exec($cmd,$files);
	$length = count($files);

	$min = 99999;
	$max = -999;
	for($cont=0;$cont<$length;$cont++){
		# 43-G-C.shift.stats
		$file = $files[$cont];
		$parts = preg_split('/\-/',$file);
		$num = $parts[0];
		if($num < $min)
			$min = $num;
		if($num > $max)
			$max = $num;
	}
	$min = $min;
	$max = $max + 2;
	$length = $max - $min;

	logger("Min: $min, Max: $max, Length: $length");

  # Strand  1 has  12 bases (5'-3'): CGCGAGGACGCG
  # Strand  2 has  12 bases (3'-5'): GCGCTCCTGCGC

	$seqPair['A'] = 'T';
	$seqPair['C'] = 'G';
	$seqPair['G'] = 'C';
	$seqPair['T'] = 'A';

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

	#logger("Curves Seq1: -$seq1-");
	#logger("Curves Seq2: -$seq2-");

	$arrSeq1 = preg_split("//",$seq1);
	array_shift($arrSeq1);
	array_pop($arrSeq1);
	$arrSeq2 = preg_split("//",$seq2);
	array_shift($arrSeq2);
	array_pop($arrSeq2);

	$length1 = count($arrSeq1);
	$length2 = count($arrSeq2);

	if($length > $length1){
		$length = $length1;
		$max = $max - 1;
	}

	logger("Min: $min, Max: $max, Length: $length");

	# Initializing Nucleotide Selection Arrays.
	for($i=0;$i<$length1;$i++){
		$_SESSION['userData']['seq1'][$i] = 0;
		$_SESSION['userData']['seqBPS'][$i] = 0;
		$_SESSION['userData']['seq2'][$i] = 0;
	}

?>

	<div id="divSeq2">

		<table cellpadding="5" align="center" border="0">
		<tr>
			<?php  
				echo "<td></td>\n";
				for($i=$min-1;$i<$max-1;$i++){
					$num = $i+1;
					echo "<td id='divTableSeqNum'> $num </td>\n";
					echo "<td></td>\n";
				}
				echo "</tr>\n";
				echo "<tr>\n";
				echo "<td width='30' class='divTableSeqPrimes'>5'</td>\n";
				for($i=$min-1;$i<$max-1;$i++){
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
					if($i != $max-2)
						#echo "<td id='divTableSeq1Seps' class='divTableSeqSeps unselected'> - </td>\n";
						echo "<td id='divTableSeq1Seps' class='divTableSeqSeps'><a id='$codeBP' class='unselected' title='Backbone'> - </a></td>\n";					
					$_SESSION['userData']['seq1'][$i] = 0;
				}
				echo "<td width='30' class='divTableSeqPrimes'>3'</td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				if($length1 > $length)
					echo "<td class='divTableSeqPrimes'>...</td>\n";
				else
					echo "<td></td>";
				for($i=$min-1;$i<$max-1;$i++){
					$letter1 = $arrSeq1[$i];
					$letter4 = $seqPair[$letter1];
					#$letter4 = $arrSeq2[$i];
					$j = $i + 1;
					$pair = ($length * 2) - $i;
					$codeBPS_old = "$j-$letter1$letter4";
					$codeBPS = "$j-$letter1:$pair-$letter4";
					$codeBPS_new = "$j-$pair";
					$letter2 = $arrSeq1[$j];
					$letter3 = $seqPair[$letter2];
					#$letter3 = $arrSeq2[$j];
					$codeTet = "$j-$letter1$letter2$letter3$letter4";
					#if($i > 0 and $i < $length1 - 1)
					if($letter1 == 'X' or $letter4 == 'X'){
						echo "<td id='divTableSepsUpDown' class='divTableSeqSepsX unselected'> | </td>\n";
					}
					else{
						echo "<td id='divTableSepsUpDown' class='divTableSeqSepsX'><a id='$codeBPS_old' name='BPS' class='unselected' title='Base Pair Helical Parameters'> | </a></td>\n";
					}
					if($i >= 0 and $i < $max - 2)
						echo "<td id='divTableSepsX' class='divTableSeqSepsX' ><a id='$codeTet' name='TET' class='unselected' href=\"javascript:selectNucleotideMontecarlo('$codeTet','$userPath');javascript:selectTetramerMontecarlo('$codeTet','$length','$min');\" title='Base Pair Step Helical Parameters'> x </a></td>\n";
						#echo "<td id='divTableSepsX' class='divTableSeqSepsX unselected' > x </td>\n";
					$_SESSION['userData']['seqBPS'][$i] = 0;
				}
				if($length1 > $length)
					echo "<td class='divTableSeqPrimes'>...</td>\n";
				else
					echo "<td></td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "<td class='divTableSeqPrimes'>3'</td>\n";
				for($i=0;$i<$length;$i++){
				#for($i=$min;$i<$max;$i++){
					$index = $i + $min - 1;
					$letter1 = $arrSeq1[$index];
					$letter2 = $arrSeq1[$index+1];
					$j = $min + ($length * 2) - $i - 1;
					$k = $j + 1;
					$p = $min + ($length * 2) - $i;
					$code = "$p-$letter1";
					$code1 = "$j-$letter2";
					$code2 = "$k-$letter1";
					$codeBP = "$j-";
                                        if($letter1 == 'X'){
                                                echo "<td class='divTableSeq' title='Unrecognized Nucleotide'><a id='divTableSeq2' class='unselected'> $letter1 </a></td>\n";
                                        }
                                        else{
						echo "<td class='divTableSeq' title='Nucleotide Helical Parameters'><a id='$j-$seqPair[$letter1]' class='unselected'> $seqPair[$letter1] </a></td>\n";
					}
					if($i != $length - 1)
						echo "<td id='divTableSeq1Seps' class='divTableSeqSeps'><a id='$codeBP' class='unselected' title='Backbone'> - </a></td>\n";
						#echo "<td id='divTableSeq2Seps' class='divTableSeqSeps unselected'> - </td>\n";
					$_SESSION['userData']['seq2'][$i] = 0;
				}
				echo "<td class='divTableSeqPrimes'>5'</td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "<td></td>\n";
				for($i=$length-1;$i>=0;$i--){
					$num = $max + $i ;
					echo "<td id='divTableSeqNum'> $num </td>\n";
					echo "<td></td>\n";
				}
			?>
		</tr>
		</table>
	</div>

	<br/><br/>	

	<div id="AvgVsTime">

         <table cellpadding="15" align="center" border="0" class="avgParms">
                <tr>
                        <td class="curvesText"><a id='AVGSel' class='unselected' href="javascript:unhideNmrAvgPlots('AVGSel','Proton_Pairs_Avg');" title='AVG Results'>Contact Maps</a></td>
			<td width="50"></td>
                        <td class="curvesText"><a id='TIMESel' class='unselected' href="javascript:unhideNmrTimePlots('TIMESel','Proton_Pairs_Params');" title='TIME Results'>Results by Time</a></td>
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

        <div id="Proton_Pairs_Avg" class="hidden" style="text-align:center;">
		<table align="center" cellpadding="0" width="950">
		<tr><td>
                  <p class="curvesText"><a id='stackingMeanSel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('stackingMeanSel_Avg','stackingAvgMeanSelPlot');" title='Contact Map - Mean'>Contact Map - Mean</a></p></td>
                  <td><p class="curvesText"><a id='stackingMinSel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('stackingMinSel_Avg','stackingAvgMinSelPlot');" title='Contact Map - Min'>Contact Map - Min</a></p></td>
                  <td><p class="curvesText"><a id='stackingMaxSel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('stackingMaxSel_Avg','stackingAvgMaxSelPlot');" title='Contact Map - Max'>Contact Map - Max</a></p></td>
                  <td><p class="curvesText"><a id='stackingStdevSel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('stackingStdevSel_Avg','stackingAvgStdevSelPlot');" title='Contact Map - Stdev'>Contact Map - Stdev</a></p></td>
		</td>
		<br/><br/>
		</td></tr>
		</table>

        </div>
	</div>

	<div id="Time_Params">
        <div id="Proton_Pairs_Params" class="hidden"  style="text-align:center;">
		<table align="center" cellpadding="50">
		<tr>
                <td>
                <ul>
		  <li class="curvesText"><a id='TimeRiseSel' class='unselected' href="javascript:unhideSelMontecarloPlots('TimeRiseSel','riseTimeSelPlot','<?php echo $length1 ?>');" title='Base Pair Step Helical 
Params: Rise'>Rise</a></li><br/><br/>
                  <li class="curvesText"><a id='TimeRollSel' class='unselected' href="javascript:unhideSelMontecarloPlots('TimeRollSel','rollTimeSelPlot','<?php echo $length1 ?>');" title='Base Pair Step Helical 
Params: Roll'>Roll</a></li><br/><br/>
		  <li class="curvesText"><a id='TimeShiftSel' class='unselected' href="javascript:unhideSelMontecarloPlots('TimeShiftSel','shiftTimeSelPlot','<?php echo $length1 ?>');" title='Base Pair Step Helic
al Params: Shift'>Shift</a></li><br/><br/>
	          <li class="curvesText"><a id='TimeSlideSel' class='unselected' href="javascript:unhideSelMontecarloPlots('TimeSlideSel','slideTimeSelPlot','<?php echo $length1 ?>');" title='Base Pair Step Helic
al Params: Slide'>Slide</a></li><br/><br/>
        	  <li class="curvesText"><a id='TimeTiltSel' class='unselected' href="javascript:unhideSelMontecarloPlots('TimeTiltSel','tiltTimeSelPlot','<?php echo $length1 ?>');" title='Base Pair Step Helical 
Params: Tilt'>Tilt</a></li><br/><br/>
               	  <li class="curvesText"><a id='TimeTwistSel' class='unselected' href="javascript:unhideSelMontecarloPlots('TimeTwistSel','twistTimeSelPlot','<?php echo $length1 ?>');" title='Base Pair Step Helic
al Params: Twist'>Twist</a></li><br/>
                </ul>
		</td>
		<td rowspan="6">
                <img src="images/helicalParamsBPS.png" align="center" border="0" usemap="#helicalParamsBPS_TimeMap">
		<a href="http://gbio-pbil.ibcp.fr/Curves_plus/Helical_parameters.html"><i style="font-size:8pt"><br/><br/>[Image courtesy of Curves+]</i></a> 
		</td></tr>
		</table>
        </div>
        </div>
	
	<!-- PLOTS -->

        <div id="NmrParams">

        <div id="HelicalParamsPlots">

                <div id="stackingAvgMeanSelPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;">
			<?php plotAVG ($userPath,"distanceMean.contactMapMEAN"); ?>
                </div>
                <div id="stackingAvgMinSelPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;">
			<?php plotAVG ($userPath,"distanceMean.contactMapMIN"); ?>
                </div>
                <div id="stackingAvgMaxSelPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;">
			<?php plotAVG ($userPath,"distanceMean.contactMapMAX"); ?>
                </div>
                <div id="stackingAvgStdevSelPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;">
			<?php plotAVG ($userPath,"distanceMean.contactMapSTDEV"); ?>
                </div>
        </div>

	</div>
        
	<div id="StatsTable">
        <?php

                $cmd = "ls *.stats";
                $out = exec($cmd,$files);
                $length = count($files);

                for($cont=0;$cont<$length;$cont++){
			# 43-G-C.shift.stats
                        $file = $files[$cont];
                        $parts = preg_split('/\./',$file);
                        $num = $parts[0].".".$parts[1];
                        $analysis = $parts[1];
                        print "<div id='stats".$num."' class='hidden'>";
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

        <div id="HelicalParamsPlotsTime">

        <div id="riseTimeSelPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;"></div>
        <div id="rollTimeSelPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;"></div>
        <div id="shiftTimeSelPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;"></div>
        <div id="slideTimeSelPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;"></div>
        <div id="tiltTimeSelPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;"></div>
        <div id="twistTimeSelPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;"></div>

        </div>

	<br/><br/>	

<map name="helicalParamsBPS_TimeMap">
<area shape="rect" coords="1,0,109,87" title="Base Pair Step Helical Params: Shift" href="javascript:unhideSelMontecarloPlots('TimeShiftSel','shiftTimeSelPlot','<?php echo $length1 ?>');" />
<area shape="rect" coords="112,3,247,92" title="Base Pair Step Helical Params: Slide" href="javascript:unhideSelMontecarloPlots('TimeSlideSel','slideTimeSelPlot','<?php echo $length1 ?>');" />
<area shape="rect" coords="248,1,377,89" title="Base Pair Step Helical Params: Rise" href="javascript:unhideSelMontecarloPlots('TimeRiseSel','riseTimeSelPlot','<?php echo $length1 ?>');" />
<area shape="rect" coords="1,91,105,186" title="Base Pair Step Helical Params: Tilt" href="javascript:unhideSelMontecarloPlots('TimeTiltSel','tiltTimeSelPlot','<?php echo $length1 ?>');" />
<area shape="rect" coords="108,92,246,185" title="Base Pair Step Helical Params: Roll" href="javascript:unhideSelMontecarloPlots('TimeRollSel','rollTimeSelPlot','<?php echo $length1 ?>');" />
<area shape="rect" coords="247,91,376,186" title="Base Pair Step Helical Params: Twist" href="javascript:unhideSelMontecarloPlots('TimeTwistSel','twistTimeSelPlot','<?php echo $length1 ?>');" />
</map>

