<?php

  # Strand  1 has  12 bases (5'-3'): CGCGAGGACGCG
  # Strand  2 has  12 bases (3'-5'): GCGCTCCTGCGC

	$cmd = "grep 'Strand  1' $curvesFile";
	$out = exec($cmd,$strand1);
	$cmd = "grep 'Strand  2' $curvesFile";
	$out = exec($cmd,$strand2);
	logger("Stiffness: $curvesFile");

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
					echo "<td id='divTableSeq1' class='divTableSeq'><a id='$code' class='unselected' href=\"javascript:selectNucleotideStiffness('$code','$userPath');\" title='Nucleotide Helical Parameters'> $letter1 </a></td>\n";
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
						echo "<td id='divTableSepsUpDown' class='divTableSeqSepsX'><a id='$codeBPS' name='BPS' class='unselected' href=\"javascript:selectNucleotideStiffness('$codeBPS','$userPath');javascript:selectBasePairStep('$codeBPS','$length1');\" title='Base Pair Helical Parameters'> | </a></td>\n";
					if($i > 0 and $i < $length1 - 2)
						echo "<td id='divTableSepsX' class='divTableSeqSepsX' ><a id='$codeTet' name='TET' class='unselected' href=\"javascript:selectNucleotideStiffness('$codeTet','$userPath');javascript:selectTetramer('$codeTet','$length1');\" title='Base Pair Step Helical Parameters'> x </a></td>\n";
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
					echo "<td id='divTableSeq2' class='divTableSeq'><a id='$code' class='unselected' href=\"javascript:selectNucleotideStiffness('$code','$userPath');\" title='Nucleotide Helical Parameters'> $letter2 </a></td>\n";
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
                        <td class="curvesText"><a id='AVGSel' class='unselected btn blue' href="javascript:unhideStiffnessAvgPlots('AVGSel','HelicalParams');" title='AVG Results'>Average Results</a></td>
			<td width="50"></td>
                        <td class="curvesText"><a id='TIMESel' class='unselected btn blue' href="javascript:unhideStiffnessTimePlots('TIMESel','BPS_HelicalParmsTime');" title='TIME Results'>Results by Time</a></td>
		</tr>
	</table>
	</div>

	<br/><br/>	

	<div id="HelicalParams" class="hidden">
	 <table cellpadding="15" align="" border="0" class="avgParms">
                <tr>
			<td class="curvesText"><a id='BPS_HelicalParmsSel' class='unselected' href="javascript:unhideStiffnessHPPlots('BPS_HelicalParmsSel','BPS_HelicalParms');" title='Base Pair Step Helical Parameters'>Stiffness by Helical Parameters </a></td>
			<td></td>
			<td class="curvesText"><a id='StiffnessBPSel' class='unselected' href="javascript:unhideStiffnessBPPlots('StiffnessBPSel','BPParamsPlots');" title='Stiffness Helical Parameters'>Stiffness By Base Pair Step</a></td>
		</tr>
	</table>
	</div>

	<div id ="BPS_Stiffness">

        <div id="BPS_HelicalParmsTime" class="hidden" style="text-align:center;">
		<table align="" cellpadding="50">
		<tr>
                <td>
                <ul>
		  <li class="curvesText"><a id='TimeShiftSel' class='unselected' href="javascript:unhideSelStiffnessPlots('TimeShiftSel','shiftTimeSelPlot');" title='Base Pair Step Helical Params: Shift'>Shift</a></li><br/><br/>
	          <li class="curvesText"><a id='TimeSlideSel' class='unselected' href="javascript:unhideSelStiffnessPlots('TimeSlideSel','slideTimeSelPlot');" title='Base Pair Step Helical Params: Slide'>Slide</a></li><br/><br/>
		  <li class="curvesText"><a id='TimeRiseSel' class='unselected' href="javascript:unhideSelStiffnessPlots('TimeRiseSel','riseTimeSelPlot');" title='Base Pair Step Helical Params: Rise'>Rise</a></li><br/><br/>
        	  <li class="curvesText"><a id='TimeTiltSel' class='unselected' href="javascript:unhideSelStiffnessPlots('TimeTiltSel','tiltTimeSelPlot');" title='Base Pair Step Helical Params: Tilt'>Tilt</a></li><br/><br/>
                  <li class="curvesText"><a id='TimeRollSel' class='unselected' href="javascript:unhideSelStiffnessPlots('TimeRollSel','rollTimeSelPlot');" title='Base Pair Step Helical Params: Roll'>Roll</a></li><br/><br/>
               	  <li class="curvesText"><a id='TimeTwistSel' class='unselected' href="javascript:unhideSelStiffnessPlots('TimeTwistSel','twistTimeSelPlot');" title='Base Pair Step Helical Params: Twist'>Twist</a></li>
                </ul>
		</td>
		<td rowspan="6">
                <img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/helicalParamsBPS.png" align="center"  border="0" usemap="#helicalParamsBPS_TimeMap">
		<a href="http://gbio-pbil.ibcp.fr/Curves_plus/Helical_parameters.html" target="_blank"><i style="font-size:8pt"><br/><br/>[Image courtesy of Curves+]</i></a> 
		</td></tr>
		</table>
        </div>

        <div id="BPS_HelicalParms" class="hidden" style="text-align:center;">
		<table align="" cellpadding="50">
		<tr>
                <td>
                <ul>
		  <li class="curvesText"><a id='ShiftSel' class='unselected' href="javascript:unhideStiffnessHPPlots('ShiftSel','shiftSelPlot');" title='Base Pair Step Helical Params: Shift'>Shift</a></li><br/><br/>
	          <li class="curvesText"><a id='SlideSel' class='unselected' href="javascript:unhideStiffnessHPPlots('SlideSel','slideSelPlot');" title='Base Pair Step Helical Params: Slide'>Slide</a></li><br/><br/>
		  <li class="curvesText"><a id='RiseSel' class='unselected' href="javascript:unhideStiffnessHPPlots('RiseSel','riseSelPlot');" title='Base Pair Step Helical Params: Rise'>Rise</a></li><br/><br/>
        	  <li class="curvesText"><a id='TiltSel' class='unselected' href="javascript:unhideStiffnessHPPlots('TiltSel','tiltSelPlot');" title='Base Pair Step Helical Params: Tilt'>Tilt</a></li><br/><br/>
                  <li class="curvesText"><a id='RollSel' class='unselected' href="javascript:unhideStiffnessHPPlots('RollSel','rollSelPlot');" title='Base Pair Step Helical Params: Roll'>Roll</a></li><br/><br/>
               	  <li class="curvesText"><a id='TwistSel' class='unselected' href="javascript:unhideStiffnessHPPlots('TwistSel','twistSelPlot');" title='Base Pair Step Helical Params: Twist'>Twist</a></li>
                </ul>
		</td>
		<td rowspan="6">
                <img src="<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>images/helicalParamsBPS.png" align="center"  border="0" usemap="#helicalParamsBPS_Map">
		<a href="http://gbio-pbil.ibcp.fr/Curves_plus/Helical_parameters.html" target="_blank"><i style="font-size:8pt"><br/><br/>[Image courtesy of Curves+]</i></a> 
		</td></tr>
		</table>
        </div>
	</div>

	<!-- PLOTS -->

	<!-- Backbone Torsions -->

	<div id="StiffnessParams">

	<div id="HelicalParamsPlots">

	        <div id="riseSelPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 40px;">
                	<?php plotAVG ($userPath,"FORCE_CTES/rise_avg"); ?>
		</div>
	        <div id="rollSelPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 40px;">
                	<?php plotAVG ($userPath,"FORCE_CTES/roll_avg"); ?>
		</div>
	        <div id="shiftSelPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 40px;">
                	<?php plotAVG ($userPath,"FORCE_CTES/shift_avg"); ?>
		</div>
        	<div id="slideSelPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 40px;">
                	<?php plotAVG ($userPath,"FORCE_CTES/slide_avg"); ?>
		</div>
	        <div id="tiltSelPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 40px;">
                	<?php plotAVG ($userPath,"FORCE_CTES/tilt_avg"); ?>
		</div>
	        <div id="twistSelPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 40px;">
                	<?php plotAVG ($userPath,"FORCE_CTES/twist_avg"); ?>
		</div>

	</div>


        <div id="BPParamsPlots" class="hidden">

	<br/><br/>

	<?php

		$cmd = "ls FORCE_CTES/*.cte";
		$out = exec($cmd,$files);

		$length = count($files);

		for($cont=0;$cont<$length;$cont++){
			# FORCE_CTES/gcgc.10.cte
			$file = $files[$cont];
			$dirs = preg_split('/\//',$file);
			$realFile = $dirs[1];
			$parts = preg_split('/\./',$realFile);
			$num = $parts[1]."-".strtoupper($parts[0]);
			print '<div id="stiffness.'.$num.'" class="hidden">';

			$code = generateStiffnessTable($file);
			print "$code";

			writeTableHtml($file,$code);
			
			$image = printCurvesImage();
			print "$image";

		?>
                        <p align='left' style="clear: both;padding: 30px 25px;"><b>Units:<br/> </b><i>Diagonal Shift/Slide/Rise in kcal/(mol*&Aring;&sup2;), Diagonal Tilt/Roll/Twist in kcal/(mol*degree&sup2;)<br/>
                        Out of Diagonal: Shift/Slide/Rise in kcal/(mol*&Aring;), Out of Diagonal Tilt/Roll/Twist in kcal/(mol*degree)</i></p>

			<table align="" border="0" style="margin:0 25px;"><tr><td>
			</td><td>
			<a href="<?php echo $GLOBALS['BASEURL']; ?>tools/naflex/getFile.php?fileloc=<?php echo $userPath."/".$file ?>&type=curves" class="btn blue">Download Raw Data</a>
			</td><!--<td>
			<p align="right" class="curvesDatText" onClick="javascript:window.open('<?php echo $GLOBALS['homeURL'].'/'.$userPath.'/'.$file.'.html' ?>','Stiffness Params','_blank,resize=1,width=700,height=400');">Open in New Window</p><br/>
			</td>--></tr></table>
		<?php

			print "\n</div>\n";
		}		
	?>

	</div>

        <div id="StatsTable">
        <?php

                $cmd = "ls FORCE_CTES/*/*.stats";
                $out = exec($cmd,$files);
                $length = count($files);

                for($cont=0;$cont<$length;$cont++){
                        # helical_bp/10-GC/buckle.stats
                        # 22/J1p2p-RNA.stats
			# FORCE_CTES/5-AGCT/shift.stats
                        $file = $files[$cont];
                        $dirs = preg_split('/\//',$file);
                        $realFile = $dirs[2];
                        #$realFile = preg_replace('/ /','_',$realFile);
                        $parts = preg_split('/\./',$realFile);
                        $num = $parts[0]."-".$dirs[1];
                        $analysis = $dirs[0];
                        #$id = $dirs[0]."-".$num;
                        print "<div id='stiffness.".$num."' class='hidden'>";
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

	<div id="HelicalParamsPlotsTime">

	<div id="Time_helical_bpstep">
        <div id="riseTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="rollTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="shiftTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="slideTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="tiltTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="twistTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
	</div>

	</div>

	</div>

<!--
         <table cellpadding="15" align="center" border="0" class="avgParms">
                <tr>
                        <td class="curvesText"><a id='bckTorsionsTimeSel' class='unselected' title='Backbone Torsions'>Stiffness Time Plot x BP</a></td>
                </tr>
        </table>
-->

	<br/><br/>

<map name="helicalParamsBPS_TimeMap">
<area shape="rect" coords="1,0,109,87" title="Base Pair Step Helical Params: Shift" href="javascript:unhideSelStiffnessPlots('TimeShiftSel','shiftTimeSelPlot');" />
<area shape="rect" coords="112,3,247,92" title="Base Pair Step Helical Params: Slide" href="javascript:unhideSelStiffnessPlots('TimeSlideSel','slideTimeSelPlot');" />
<area shape="rect" coords="248,1,377,89" title="Base Pair Step Helical Params: Rise" href="javascript:unhideSelStiffnessPlots('TimeRiseSel','riseTimeSelPlot');" />
<area shape="rect" coords="1,91,105,186" title="Base Pair Step Helical Params: Tilt" href="javascript:unhideSelStiffnessPlots('TimeTiltSel','tiltTimeSelPlot');" />
<area shape="rect" coords="108,92,246,185" title="Base Pair Step Helical Params: Roll" href="javascript:unhideSelStiffnessPlots('TimeRollSel','rollTimeSelPlot');" />
<area shape="rect" coords="247,91,376,186" title="Base Pair Step Helical Params: Twist" href="javascript:unhideSelStiffnessPlots('TimeTwistSel','twistTimeSelPlot');" />
</map>

<map name="helicalParamsBPS_Map">
<area shape="rect" coords="1,0,109,87" title="Base Pair Step Helical Params: Shift" href="javascript:unhideStiffnessHPPlots('ShiftSel','shiftSelPlot');" />
<area shape="rect" coords="112,3,247,92" title="Base Pair Step Helical Params: Slide" href="javascript:unhideStiffnessHPPlots('SlideSel','slideSelPlot');" />
<area shape="rect" coords="248,1,377,89" title="Base Pair Step Helical Params: Rise" href="javascript:unhideStiffnessHPPlots('RiseSel','riseSelPlot');" />
<area shape="rect" coords="1,91,105,186" title="Base Pair Step Helical Params: Tilt" href="javascript:unhideStiffnessHPPlots('TiltSel','tiltSelPlot');" />
<area shape="rect" coords="108,92,246,185" title="Base Pair Step Helical Params: Roll" href="javascript:unhideStiffnessHPPlots('RollSel','rollSelPlot');" />
<area shape="rect" coords="247,91,376,186" title="Base Pair Step Helical Params: Twist" href="javascript:unhideStiffnessHPPlots('TwistSel','twistSelPlot');" />
</map>


<?php 

function printCurvesImage() {

	$code = '<table align="" border="0"><tr><td>';
	$code .= '<img border="0" align="right" src="images/helicalParamsBPS2.png">';
	$code .= '</td></tr><tr><td align="center">';
	$code .= '<a href="http://gbio-pbil.ibcp.fr/Curves_plus/Helical_parameters.html" target="_blank"><i style="font-size:8pt"><br/><br/>[Image courtesy of Curves+]</i></a>';
	$code .= '</td></tr></table>';

	return $code;
}

function tableColor($number,$min) {

  $num = ($number - $min) / 10;
  $percent = 255 * $num;

  $r = 255;
  $g = sprintf("%d",240 - $percent);
  $b = sprintf("%d",240 - $percent);

  return "$r,$g,$b";
}

function generateStiffnessTable ($tableFile) {

	$hp = array(Shift,Slide,Rise,Tilt,Roll,Twist);

	$code = "<table cellpadding='15' align='left' border='0'>\n";	
	$code .="<tr align='center' style='background-color:#dcdcdc'><td style='background-color:#ffffff'></td><td>Shift</td><td>Slide</td><td>Rise</td><td>Tilt</td><td>Roll</td><td>Twist</td></tr>\n";

	# gtac.8.cte
	#$tableFile = "FORCE_CTES/gtac.8.cte";
logger("TableFile: $tableFile");
	# Getting Min & Max Values
	$max = -999;
	$min = 999;
	$ftable=fopen($tableFile, "r");
	while(!feof($ftable)) {
		$line=fgets($ftable);
		$array = preg_split("/\s+/",$line);
		foreach (array_values($array) as $value) {
			if($value != ""){
				if($value > $max)
					$max = $value;
				if($value < $min)
					$min = $value;
			}
		}
	}
	fclose($ftable);

	# Writting Table
	$i = 0;
	$ftable=fopen($tableFile, "r");
#logger("Table: $tableFile");
	while(!feof($ftable)) {
		$line=fgets($ftable);
		if($line!=""){
			$array = preg_split("/\s+/",$line);
#logger("Line: $line");		
			$code .= "<tr>\n";
			$code .= "<td style='background-color:#dcdcdc'>$hp[$i]</td>\n";
			foreach (array_values($array) as $value) {
				$value = trim($value);
				if($value != ""){
					$value = sprintf("%8.3f",$value);
					$rgb = tableColor($value,$min);
					$code .= "<td style='background-color: rgb($rgb)'>$value</td>\n";
#logger("Code: <td style='background-color: rgb($rgb)'>$value</td>");
				}
			}
			$i++;
			$code .= "</tr>\n";
		}
	}
	$code .= "</table>\n";
	
	fclose($ftable);

	return $code;
}

function writeTableHtml ($file,$code){

	$htmlFile = "$file.html";
	$fout=fopen($htmlFile, "w");
	fwrite ($fout, $code);
	fclose($fout);
}

?>



