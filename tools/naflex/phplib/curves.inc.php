<!--
# MDWeb for Nucleic Acids
# Helical Parameter Analysis with Curves+.
-->

<?php

  # Strand  1 has  12 bases (5'-3'): CGCGAGGACGCG
  # Strand  2 has  12 bases (3'-5'): GCGCTCCTGCGC

	$curvesLis = "/userData/$user/$proj/Curves/${op}_curvesOut.lis";
	$curvesCda = "/userData/$user/$proj/Curves/${op}_curvesOut.cda";

	$cmd = "grep 'Strand  1' $curvesFile";
	$out = exec($cmd,$strand1);
	$cmd = "grep 'Strand  2' $curvesFile";
	$out = exec($cmd,$strand2);

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
					echo "<td id='divTableSeq1' class='divTableSeq'><a id='$code' class='unselected' href=\"javascript:selectNucleotide('$code','$userPath');\" title='Nucleotide Helical Parameters'> $letter1 </a></td>\n";
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
						echo "<td id='divTableSepsUpDown' class='divTableSeqSepsX'><a id='$codeBPS' name='BPS' class='unselected' href=\"javascript:selectNucleotide('$codeBPS','$userPath');javascript:selectBasePairStep('$codeBPS','$length1');\" title='Base Pair Helical Parameters'> | </a></td>\n";
					if($i > 0 and $i < $length1 - 2)
						echo "<td id='divTableSepsX' class='divTableSeqSepsX' ><a id='$codeTet' name='TET' class='unselected' href=\"javascript:selectNucleotide('$codeTet','$userPath');javascript:selectTetramer('$codeTet','$length1');\" title='Base Pair Step Helical Parameters'> x </a></td>\n";
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
					echo "<td id='divTableSeq2' class='divTableSeq'><a id='$code' class='unselected' href=\"javascript:selectNucleotide('$code','$userPath');\" title='Nucleotide Helical Parameters'> $letter2 </a></td>\n";
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
<!--		
		<table cellpadding="5" align="center" border="0">
			<td><input type="button" onclick="javascript: selectAll(<?php echo $length1 ?>);" value="Select All"/></td>
			<td><input type="button" onclick="javascript: unselectAll(<?php echo $length1 ?>);" value="Unselect All" /></td>
			<td><input type="button" onclick="javascript: computeNuc();" value="Compute" /></td>
		</table>
-->
	</div>

	<p style="float: left;
    text-align: left;
    width: 50%;"><b>Interactive Sequence (<a href="<?php echo $GLOBALS['homeURL']?>/help.php?id=tutorialAnalysisNA#HelicalParms" target="_blank">see Help</a>)</b></p>

	<br/><br/>	

	<div id="AvgVsTime">

         <table cellpadding="15"  border="0" class="avgParms">
                <tr>
                        <td class="curvesText"><a id='AVGSel' class='unselected btn blue' href="javascript:unhideSections('AVGSel','HelicalParams');" title='AVG Results'>Average Results</a></td>
			<?php if($analysis == "PDB"){ 

			print "<td width=\"50\"></td>\n";
                        print "<td><a id='TIMESel' class='unselected' title='TIME Results'></a></td>\n";
			#print "<td width=\"50\"></td>\n";
                        #print "<td class=\"curvesText_hidden\"><a id='TIMESel' class='unselected' title='TIME Results'>Results by Time</a></td>\n";
			}
			else {
			?>
			<td width="50"></td>
                        <td class="curvesText"><a id='TIMESel' class='unselected btn blue' href="javascript:unhideSections('TIMESel','TimeParams');" title='TIME Results'>Results by Time</a></td>
			<?php }; ?>
		</tr>
	</table>
	</div>

	<br/><br/>	

	<div id="CurvesParams">
	<div id="HelicalParams" class="hidden">
	 <table cellpadding="15" align="" border="0" class="avgParms">
                <tr>
			<td class="curvesText"><a id='bckTorsionsSel' class='unselected btn blue' href="javascript:unhideSections('bckTorsionsSel','backboneTorsions');" title='Backbone Torsions'>Backbone Torsions</a></td>
			<td></td>
			<td class="curvesText"><a id='AxisSel' class='unselected btn blue' href="javascript:unhideSections('AxisSel','Axis');" title='Axis'>Axis Base Pair</a></td>
			<td></td>
			<td class="curvesText"><a id='BP_HelicalParmsSel' class='unselected btn blue' href="javascript:unhideSections('BP_HelicalParmsSel','BP_HelicalParms');" title='Base Pair Helical Parameters'>Intra-Base Pair Helical Parameters </a></td>
			<td></td>
			<td class="curvesText"><a id='BPS_HelicalParmsSel' class='unselected btn blue' href="javascript:unhideSections('BPS_HelicalParmsSel','BPS_HelicalParms');" title='Base Pair Step Helical Parameters'>Inter-Base Pair Helical Parameters </a></td>
			<td></td>
			<td class="curvesText"><a id='GroovesSel' class='unselected btn blue' href="javascript:unhideSections('GroovesSel','Grooves');" title='Grooves'>Grooves </a></td>
		</tr>
	</table>
	</div>
        <div id="TimeParams" class="hidden">
         <table cellpadding="15" align="" border="0" class="avgParms">
                <tr>
                        <td class="curvesText"><a id='bckTorsionsTimeSel' class='unselected btn blue' href="javascript:unhideSections('bckTorsionsTimeSel','backboneTorsionsTime');" title='Backbone Torsions'>Backbone Torsions</a></td>
			<td></td>
			<td class="curvesText"><a id='AxisTimeSel' class='unselected btn blue' href="javascript:unhideSections('AxisTimeSel','AxisTime');" title='Axis'>Axis Base Pair</a></td>
			<td></td>
                        <td class="curvesText"><a id='BP_HelicalParmsTimeSel' class='unselected btn blue' href="javascript:unhideSections('BP_HelicalParmsTimeSel','BP_HelicalParmsTime');" title='Base Pair Helical Parameters'>Intra-Base Pair Helical Parameters </a></td>
			<td></td>
                        <td class="curvesText"><a id='BPS_HelicalParmsTimeSel' class='unselected btn blue' href="javascript:unhideSections('BPS_HelicalParmsTimeSel','BPS_HelicalParmsTime');" title='Base Pair Step Helical Parameters'>Inter-Base Pair Helical Parameters </a></td>
			<td></td>
                        <td class="curvesText"><a id='GroovesTimeSel' class='unselected btn blue' href="javascript:unhideSections('GroovesTimeSel','GroovesTime');" title='Grooves'>Grooves </a></td>
                </tr>
        </table>
	</div>
	</div>

	<br/><br/>

	<!-- List of Different Curves Parameters -->

	<div id="CurvesHelicalParamsSections" style="text-align:center;">

	<div id="HelicalParamsSectionsTime" style="text-align:center;">

        <div id="backboneTorsionsTime" class="hidden">

		<table align="" cellpadding="10">
		<tr>
                <td>
                <ul>
                <li class="curvesText"><a id='AlphaSel' class='unselected btn blue' href="javascript:unhideSelPlots('AlphaSel','alphaTimeSelPlot');" title='Alpha Torsions'>Alpha Torsions</a></li><br/><br/>
                <li class="curvesText"><a id='BetaSel' class='unselected btn blue' href="javascript:unhideSelPlots('BetaSel','betaTimeSelPlot');" title='Beta Torsions'>Beta Torsions</a></li><br/><br/>
                <li class="curvesText"><a id='GammaSel' class='unselected btn blue' href="javascript:unhideSelPlots('GammaSel','gammaTimeSelPlot');" title='Gamma Torsions'>Gamma Torsions</a></li><br/><br/>
                <li class="curvesText"><a id='EpsilonSel' class='unselected btn blue' href="javascript:unhideSelPlots('EpsilonSel','epsilTimeSelPlot');" title='Epsilon Torsions'>Epsilon Torsions</a></li><br/><br/>
                <li class="curvesText"><a id='ZetaSel' class='unselected btn blue' href="javascript:unhideSelPlots('ZetaSel','zetaTimeSelPlot');" title='Zeta Torsions'>Zeta Torsions</a></li><br/><br/>
                <li class="curvesText"><a id='ChiSel' class='unselected btn blue' href="javascript:unhideSelPlots('ChiSel','chiTimeSelPlot');" title='Chi Torsions'>Chi Torsions</a></li><br/><br/>
                <li class="curvesText"><a id='PhaseSel' class='unselected btn blue' href="javascript:unhideSelPlots('PhaseSel','phaseTimeSelPlot');" title='Phase Torsions'>Phase Torsions</a></li>
                </ul>
		</td>
		<td rowspan="7">
                <img src="tools/naflex/images/backboneAngles.png" width="70%" align="center" target="_blank">
		<i style="font-size:8pt"><br/><br/>[Image courtesy of Neidle, S. (2007). Principles of nucleic acid structure.]</i> 
		</td></tr>
		</table>
        </div>

        <div id="AxisTime" class="hidden">
		<table align="" cellpadding="40">
		<tr>
                <td>
                <ul>
                <li class="curvesText"><a id='TimeInclinationSel' class='unselected btn blue' href="javascript:unhideSelPlots('TimeInclinationSel','inclinTimeSelPlot');" title='Inclination'>Inclination</a></li><br/><br/>
                <li class="curvesText"><a id='TimeTipSel' class='unselected btn blue' href="javascript:unhideSelPlots('TimeTipSel','tipTimeSelPlot');" title='Tip'>Tip</a></li><br/><br/>
                <li class="curvesText"><a id='TimeXdispSel' class='unselected btn blue' href="javascript:unhideSelPlots('TimeXdispSel','xdispTimeSelPlot');" title='X-displacement'>X-displacement</a></li><br/><br/>
                <li class="curvesText"><a id='TimeYdispSel' class='unselected btn blue' href="javascript:unhideSelPlots('TimeYdispSel','ydispTimeSelPlot');" title='Y-displacement'>Y-displacement</a></li>
                </ul>
		</td>
		<td rowspan="4">
                <img src="tools/naflex/images/axis-bp.png" align="center" border="0" usemap="#axisBP_TimeMap">
		<a href="http://gbio-pbil.ibcp.fr/Curves_plus/Helical_parameters.html" target="_blank"><i style="font-size:8pt"><br/><br/>[Image courtesy of Curves+]</i></a> 
		</td></tr>
		</table>
	</div>

        <div id="BP_HelicalParmsTime" class="hidden">
		<table align="" cellpadding="50">
		<tr>
                <td>
                <ul>
                <li class="curvesText"><a id='TimeShearSel' class='unselected btn blue' href="javascript:unhideSelPlots('TimeShearSel','shearTimeSelPlot');" title='Base Pair Helical Params: Shear'>Shear</a></li><br/><br/>
                <li class="curvesText"><a id='TimeStretchSel' class='unselected btn blue' href="javascript:unhideSelPlots('TimeStretchSel','stretchTimeSelPlot');" title='Base Pair Helical Params: Stretch'>Stretch</a></li><br/><br/>
                <li class="curvesText"><a id='TimeStaggerSel' class='unselected btn blue' href="javascript:unhideSelPlots('TimeStaggerSel','staggerTimeSelPlot');" title='Base Pair Helical Params: Stagger'>Stagger</a></li><br/><br/>
                <li class="curvesText"><a id='TimeBuckleSel' class='unselected btn blue' href="javascript:unhideSelPlots('TimeBuckleSel','buckleTimeSelPlot');" title='Base Pair Helical Params: Buckle'>Buckle</a></li><br/><br/>
                <li class="curvesText"><a id='TimePropelSel' class='unselected btn blue' href="javascript:unhideSelPlots('TimePropelSel','propelTimeSelPlot');" title='Base Pair Helical Params: Propeller'>Propeller</a></li><br/><br/>
                <li class="curvesText"><a id='TimeOpeningSel' class='unselected btn blue' href="javascript:unhideSelPlots('TimeOpeningSel','openingTimeSelPlot');" title='Base Pair Helical Params: Opening'>Opening</a></li>
                </ul>
		</td>
		<td rowspan="6">
                <img src="tools/naflex/images/helicalParamsBP.png" align="center" border="0" usemap="#helicalParamsBP_TimeMap" >
		<a href="http://gbio-pbil.ibcp.fr/Curves_plus/Helical_parameters.html" target="_blank"><i style="font-size:8pt"><br/><br/>[Image courtesy of Curves+]</i></a> 
		</td></tr>
		</table>
        </div>

        <div id="BPS_HelicalParmsTime" class="hidden">
		<table align="" cellpadding="50">
		<tr>
                <td>
                <ul>
		  <li class="curvesText"><a id='TimeRiseSel' class='unselected btn blue' href="javascript:unhideSelPlots('TimeRiseSel','riseTimeSelPlot');" title='Base Pair Step Helical Params: Rise'>Rise</a></li><br/><br/>
                  <li class="curvesText"><a id='TimeRollSel' class='unselected btn blue' href="javascript:unhideSelPlots('TimeRollSel','rollTimeSelPlot');" title='Base Pair Step Helical Params: Roll'>Roll</a></li><br/><br/>
		  <li class="curvesText"><a id='TimeShiftSel' class='unselected btn blue' href="javascript:unhideSelPlots('TimeShiftSel','shiftTimeSelPlot');" title='Base Pair Step Helical Params: Shift'>Shift</a></li><br/><br/>
	          <li class="curvesText"><a id='TimeSlideSel' class='unselected btn blue' href="javascript:unhideSelPlots('TimeSlideSel','slideTimeSelPlot');" title='Base Pair Step Helical Params: Slide'>Slide</a></li><br/><br/>
        	  <li class="curvesText"><a id='TimeTiltSel' class='unselected btn blue' href="javascript:unhideSelPlots('TimeTiltSel','tiltTimeSelPlot');" title='Base Pair Step Helical Params: Tilt'>Tilt</a></li><br/><br/>
               	  <li class="curvesText"><a id='TimeTwistSel' class='unselected btn blue' href="javascript:unhideSelPlots('TimeTwistSel','twistTimeSelPlot');" title='Base Pair Step Helical Params: Twist'>Twist</a></li><br/>
                </ul>
		</td>
		<td rowspan="6">
                <img src="tools/naflex/images/helicalParamsBPS.png" align="center" border="0" usemap="#helicalParamsBPS_TimeMap">
		<a href="http://gbio-pbil.ibcp.fr/Curves_plus/Helical_parameters.html" target="_blank"><i style="font-size:8pt"><br/><br/>[Image courtesy of Curves+]</i></a> 
		</td></tr>
		</table>
        </div>
        <div id="GroovesTime" class="hidden">
		<table align="" cellpadding="50">
		<tr>
                <td>
                <ul>
                <li class="curvesText"><a id='TimeMajDepthSel' class='unselected btn blue' href="javascript:unhideSelPlots('TimeMajDepthSel','majdTimeSelPlot');" title='Groove Params: Major Groove Depth'>Major Groove Depth</a></li><br/><br/>
                <li class="curvesText"><a id='TimeMajWidthSel' class='unselected btn blue' href="javascript:unhideSelPlots('TimeMajWidthSel','majwTimeSelPlot');" title='Groove Params: Major Groove Width'>Major Groove Width</a></li><br/><br/>
                <li class="curvesText"><a id='TimeMinDepthSel' class='unselected btn blue' href="javascript:unhideSelPlots('TimeMinDepthSel','mindTimeSelPlot');" title='Groove Params: Minor Groove Depth'>Minor Groove Depth</a></li><br/><br/>
                <li class="curvesText"><a id='TimeMinWidthSel' class='unselected btn blue' href="javascript:unhideSelPlots('TimeMinWidthSel','minwTimeSelPlot');" title='Groove Params: Minor Groove Width'>Minor Groove Width</a></li>
                </ul>
		</td>
		<td rowspan="4">
                <img src="tools/naflex/images/DnaMajorMinorGroove.gif" width="60%" align="center">
		<a href="http://en.wikibooks.org/wiki/Structural_Biochemistry/Nucleic_Acid/DNA/DNA_structure#Major_and_Minor_Grooves" target="_blank"><i style="font-size:8pt"><br/><br/>[Image courtesy of Wikibooks]</i></a> 
		</td></tr>
		</table>
        </div>

	</div>

	<div id="HelicalParamsSections">

	<div id="backboneTorsions" class="hidden">
		<table align="" cellpadding="50">
		<tr>
                <td>
		<ul>
		<li class="curvesText"><a id='BISel' class='unselected btn blue' href="javascript:unhidePlots('BISel','BIPlot');" title='Backbone Torsions: BI/II Population'>BI / BII Population</a></li><br/><br/>
		<li class="curvesText"><a id='AGSel' class='unselected btn blue' href="javascript:unhidePlots('AGSel','AGPlot');" title='Backbone Torsions: Canonical Alpha-Gamma'>Canonical Alpha-Gamma</a></li><br/><br/>
		<li class="curvesText"><a id='PuckSel' class='unselected btn blue' href="javascript:unhidePlots('PuckSel','PuckPlot');" title='Backbone Torsions: Puckering'>Puckering</a></li>
		</ul>
		</td>
		<td rowspan="3">
                <div id="BI-BII-graphic-div" > <div id="BI-BII-graphic" class="hidden"> <img src="tools/naflex/images/BI-BII.png" width="500" align="center">
		<a href="http://www.sciencedirect.com/science/article/pii/S0022283609012637?via=ihub" target="_blank"><i style="font-size:8pt"><br/><br/>[Image courtesy of Heddi B. et al, JMB 2010, 395:1, 123-133]</i></a> </div></div>
                <div id="Alpha-Gamma-graphic-div" > <div id="Alpha-Gamma-graphic" class="hidden"> <img src="tools/naflex/images/AlphaGamma.png" width="400" align="center">
		<i style="font-size:8pt"><br/><br/>[Image courtesy of Neidle, S. (2007). Principles of nucleic acid structure.]</i> </div></div>
                <div id="Puckering-graphic-div" > <div id="Puckering-graphic" class="hidden"> <img src="tools/naflex/images/Puckering2.png" width="600" align="center">
		<i style="font-size:8pt"><br/><br/>[Image courtesy of Neidle, S. (2007). Principles of nucleic acid structure.]</i> </div></div>
		</td></tr>
		</table>
	</div>

	<div id="Axis" class="hidden">
		<table align="" cellpadding="50">
		<tr>
                <td>
		<ul>
		<li class="curvesText"><a id='InclinationSel' class='unselected btn blue' href="javascript:unhidePlots('InclinationSel','InclinationPlot');" title='Inclination'>Inclination</a></li><br/><br/>
		<li class="curvesText"><a id='TipSel' class='unselected btn blue' href="javascript:unhidePlots('TipSel','TipPlot');" title='Tip'>Tip</a></li><br/><br/>
		<li class="curvesText"><a id='XdispSel' class='unselected btn blue' href="javascript:unhidePlots('XdispSel','XdispPlot');" title='X-Displacement'>X-displacement</a></li><br/><br/>
		<li class="curvesText"><a id='YdispSel' class='unselected btn blue' href="javascript:unhidePlots('YdispSel','YdispPlot');" title='Y-Displacement'>Y-displacement</a></li>
                </ul>
		</td>
		<td rowspan="4">
                <img src="tools/naflex/images/axis-bp.png" align="center" border="0" usemap="#axisBP_Map">
		<a href="http://gbio-pbil.ibcp.fr/Curves_plus/Helical_parameters.html" target="_blank"><i style="font-size:8pt"><br/><br/>[Image courtesy of Curves+]</i></a> 
		</td></tr>
		</table>
	</div>

	<div id="BP_HelicalParms" class="hidden">
		<table align="" cellpadding="50">
		<tr>
                <td>
		<ul>
		<li class="curvesText"><a id='ShearSel' class='unselected btn blue' href="javascript:unhidePlots('ShearSel','ShearPlot');" title='Base Pair Helical Params: Shear'>Shear</a></li><br/><br/>
		<li class="curvesText"><a id='StretchSel' class='unselected btn blue' href="javascript:unhidePlots('StretchSel','StretchPlot');" title='Base Pair Helical Params: Stretch'>Stretch</a></li><br/><br/>
		<li class="curvesText"><a id='StaggerSel' class='unselected btn blue' href="javascript:unhidePlots('StaggerSel','StaggerPlot');" title='Base Pair Helical Params: Stagger'>Stagger</a></li><br/><br/>
		<li class="curvesText"><a id='BuckleSel' class='unselected btn blue' href="javascript:unhidePlots('BuckleSel','BucklePlot');" title='Base Pair Helical Params: Buckle'>Buckle</a></li><br/><br/>
		<li class="curvesText"><a id='PropelSel' class='unselected btn blue' href="javascript:unhidePlots('PropelSel','PropelPlot');" title='Base Pair Helical Params: Propeller'>Propeller</a></li><br/><br/>
		<li class="curvesText"><a id='OpeningSel' class='unselected btn blue' href="javascript:unhidePlots('OpeningSel','OpeningPlot');" title='Base Pair Helical Params: Opening'>Opening</a></li>
		</ul>
		</td>
		<td rowspan="6">
                <img src="tools/naflex/images/helicalParamsBP.png" align="center" border="0" usemap="#helicalParamsBP_Map">
		<a href="http://gbio-pbil.ibcp.fr/Curves_plus/Helical_parameters.html" target="_blank"><i style="font-size:8pt"><br/>[Image courtesy of Curves+]</i></a> 
		</td></tr>
		</table>
	</div>

	<div id="BPS_HelicalParms" class="hidden">
		<table align="" cellpadding="50">
		<tr>
                <td>
		<ul>
		<li class="curvesText"><a id='RiseSel' class='unselected btn blue' href="javascript:unhidePlots('RiseSel','RisePlot');" title='Base Pair Step Helical Params: Rise'>Rise</a></li><br/><br/>
		<li class="curvesText"><a id='RollSel' class='unselected btn blue' href="javascript:unhidePlots('RollSel','RollPlot');" title='Base Pair Step Helical Params: Roll'>Roll</a></li><br/><br/>
		<li class="curvesText"><a id='ShiftSel' class='unselected btn blue' href="javascript:unhidePlots('ShiftSel','ShiftPlot');" title='Base Pair Step Helical Params: Shift'>Shift</a></li><br/><br/>
		<li class="curvesText"><a id='SlideSel' class='unselected btn blue' href="javascript:unhidePlots('SlideSel','SlidePlot');" title='Base Pair Step Helical Params: Slide'>Slide</a></li><br/><br/>
		<li class="curvesText"><a id='TiltSel' class='unselected btn blue' href="javascript:unhidePlots('TiltSel','TiltPlot');" title='Base Pair Step Helical Params: Tilt'>Tilt</a></li><br/><br/>
		<li class="curvesText"><a id='TwistSel' class='unselected btn blue' href="javascript:unhidePlots('TwistSel','TwistPlot');" title='Base Pair Step Helical Params: Twist'>Twist</a></li>
		</ul>
		</td>
		<td rowspan="6">
                <img src="tools/naflex/images/helicalParamsBPS.png" align="center" border="0" usemap="#helicalParamsBPS_Map">
		<a href="http://gbio-pbil.ibcp.fr/Curves_plus/Helical_parameters.html" target="_blank"><i style="font-size:8pt"><br/>[Image courtesy of Curves+]</i></a> 
		</td></tr>
		</table>
	</div>

	<div id="Grooves" class="hidden">
		<table align="" cellpadding="50">
		<tr>
                <td>
		<ul>
		<li class="curvesText"><a id='MajDepthSel' class='unselected btn blue' href="javascript:unhidePlots('MajDepthSel','MajDepthPlot');" title='Groove Params: Major Groove Depth'>Major Groove Depth</a></li><br/><br/>
		<li class="curvesText"><a id='MajWidthSel' class='unselected btn blue' href="javascript:unhidePlots('MajWidthSel','MajWidthPlot');" title='Groove Params: Major Groove Width'>Major Groove Width</a></li><br/><br/>
		<li class="curvesText"><a id='MinDepthSel' class='unselected btn blue' href="javascript:unhidePlots('MinDepthSel','MinDepthPlot');" title='Groove Params: Minor Groove Depth'>Minor Groove Depth</a></li><br/><br/>
		<li class="curvesText"><a id='MinWidthSel' class='unselected btn blue' href="javascript:unhidePlots('MinWidthSel','MinWidthPlot');" title='Groove Params: Minor Groove Width'>Minor Groove Width</a></li>
		</ul>
		</td>
		<td rowspan="4">
                <img src="tools/naflex/images/DnaMajorMinorGroove.gif" width="60%" align="center">
		<a href="http://en.wikibooks.org/wiki/Structural_Biochemistry/Nucleic_Acid/DNA/DNA_structure#Major_and_Minor_Grooves" target="_blank"><i style="font-size:8pt"><br/><br/>[Image courtesy of Wikibooks]</i></a>
		</td></tr>
		</table>
	</div>

	</div>

	<!-- PLOTS -->

	<!-- Backbone Torsions -->

	<div id="CurvesPlots">

        </div>

	<div id="HelicalParamsPlots">

	<div id="BIPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;">
		<?php plotAVG ($userPath,"backbone_torsions/BI_population"); ?>
	</div>

	<div id="AGPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;">
		<?php plotAVG ($userPath,"backbone_torsions/canonical_alpha_gamma"); ?>
	</div>
 
	<div id="PuckPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;">
		<?php plotAVG ($userPath,"backbone_torsions/puckering"); ?>
	</div>

	<!-- Axis Base Pair Parameters -->

	<div id="XdispPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;">
		<?php plotAVG ($userPath,"axis_bp/xdisp_avg"); ?>
	</div>

	<div id="YdispPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;">
		<?php plotAVG ($userPath,"axis_bp/ydisp_avg"); ?>
	</div>

	<div id="InclinationPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;">
		<?php plotAVG ($userPath,"axis_bp/inclin_avg"); ?>
	</div>

	<div id="TipPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;">
		<?php plotAVG ($userPath,"axis_bp/tip_avg"); ?>
	</div>

	<!-- Base Pair Helical Parameters -->

	<div id="BucklePlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;">
		<?php plotAVG ($userPath,"helical_bp/buckle_avg"); ?>
	</div>

	<div id="OpeningPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;">
		<?php plotAVG ($userPath,"helical_bp/opening_avg"); ?>
	</div>

	<div id="PropelPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;">
		<?php plotAVG ($userPath,"helical_bp/propel_avg"); ?>
	</div>

	<div id="ShearPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;">
		<?php plotAVG ($userPath,"helical_bp/shear_avg"); ?>
	</div>

	<div id="StaggerPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;">
		<?php plotAVG ($userPath,"helical_bp/stagger_avg"); ?>
	</div>

	<div id="StretchPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;">
		<?php plotAVG ($userPath,"helical_bp/stretch_avg"); ?>
	</div>

	<!-- Base Pair Step Helical Parameters -->

	<div id="RisePlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;">
		<?php plotAVG ($userPath,"helical_bpstep/rise_avg"); ?>
	</div>

	<div id="RollPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;">
		<?php plotAVG ($userPath,"helical_bpstep/roll_avg"); ?>
	</div>

	<div id="ShiftPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;">
		<?php plotAVG ($userPath,"helical_bpstep/shift_avg"); ?>
	</div>

	<div id="SlidePlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;">
		<?php plotAVG ($userPath,"helical_bpstep/slide_avg"); ?>
	</div>

	<div id="TiltPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;">
		<?php plotAVG ($userPath,"helical_bpstep/tilt_avg"); ?>
	</div>

	<div id="TwistPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;">
		<?php plotAVG ($userPath,"helical_bpstep/twist_avg"); ?>
	</div>

	<!-- Grooves Parameters -->

	<div id="MajDepthPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;">
		<?php plotAVG ($userPath,"grooves/majd_avg"); ?>
	</div>

	<div id="MajWidthPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;">
		<?php plotAVG ($userPath,"grooves/majw_avg"); ?>
	</div>

	<div id="MinDepthPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;">
		<?php plotAVG ($userPath,"grooves/mind_avg"); ?>
	</div>

	<div id="MinWidthPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;">
		<?php plotAVG ($userPath,"grooves/minw_avg"); ?>
	</div>

	</div>

        <div id="StatsTable">
        <?php

                $cmd = "ls */*/*.stats";
                $out = exec($cmd,$files);
                $length = count($files);

                for($cont=0;$cont<$length;$cont++){
			# helical_bp/10-GC/buckle.stats
                        # 22/J1p2p-RNA.stats
                        $file = $files[$cont];
                        $dirs = preg_split('/\//',$file);
                        $realFile = $dirs[2];
			$realFile = preg_replace('/ /','_',$realFile);
                        $parts = preg_split('/\./',$realFile);
                        $num = $parts[0]."-".$dirs[1];
			$analysis = $dirs[0];
			#$id = $dirs[0]."-".$num;
                        print "<div id='curves.".$num."' class='hidden'>";
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

	<div id="Time_backbone_torsions">
        <div id="alphaTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="betaTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="gammaTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="epsilTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="zetaTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="chiTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="phaseTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
	</div>

	<div id="Time_axis_bp">
        <div id="inclinTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="tipTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="xdispTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="ydispTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
	</div>

	<div id="Time_helical_bp">
        <div id="buckleTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="openingTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="propelTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="shearTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="staggerTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="stretchTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
	</div>

	<div id="Time_helical_bpstep">
        <div id="riseTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="rollTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="shiftTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="slideTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="tiltTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="twistTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
	</div>

	<div id="Time_grooves">
        <div id="majdTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="majwTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="mindTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
        <div id="minwTimeSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;"></div>
	</div>

	</div>

</div>

<map name="helicalParamsBP_TimeMap">
<area shape="rect" coords="1,0,109,87" title="Base Pair Helical Params: Shear" href="javascript:unhideSelPlots('TimeShearSel','shearTimeSelPlot');" />
<area shape="rect" coords="112,3,247,92" title="Base Pair Helical Params: Stretch" href="javascript:unhideSelPlots('TimeStretchSel','stretchTimeSelPlot');" />
<area shape="rect" coords="248,1,377,89" title="Base Pair Helical Params: Stagger" href="javascript:unhideSelPlots('TimeStaggerSel','staggerTimeSelPlot');" />
<area shape="rect" coords="1,91,105,186" title="Base Pair Helical Params: Buckle" href="javascript:unhideSelPlots('TimeBuckleSel','buckleTimeSelPlot');" />
<area shape="rect" coords="108,92,246,185" title="Base Pair Helical Params: Propeller" href="javascript:unhideSelPlots('TimePropelSel','propelTimeSelPlot');" />
<area shape="rect" coords="247,91,376,186" title="Base Pair Helical Params: Opening" href="javascript:unhideSelPlots('TimeOpeningSel','openingTimeSelPlot');" />
</map>

<map name="helicalParamsBP_Map">
<area shape="rect" coords="1,0,109,87" title="Base Pair Helical Params: Shear" href="javascript:unhidePlots('ShearSel','ShearPlot');" />
<area shape="rect" coords="112,3,247,92" title="Base Pair Helical Params: Stretch" href="javascript:unhidePlots('StretchSel','StretchPlot');" />
<area shape="rect" coords="248,1,377,89" title="Base Pair Helical Params: Stagger" href="javascript:unhidePlots('StaggerSel','StaggerPlot');" />
<area shape="rect" coords="1,91,105,186" title="Base Pair Helical Params: Buckle" href="javascript:unhidePlots('BuckleSel','BucklePlot');" />
<area shape="rect" coords="108,92,246,185" title="Base Pair Helical Params: Propeller" href="javascript:unhidePlots('PropelSel','PropelPlot');" />
<area shape="rect" coords="247,91,376,186" title="Base Pair Helical Params: Opening" href="javascript:unhidePlots('OpeningSel','OpeningPlot');" />
</map>

<map name="helicalParamsBPS_TimeMap">
<area shape="rect" coords="1,0,109,87" title="Base Pair Step Helical Params: Shift" href="javascript:unhideSelPlots('TimeShiftSel','shiftTimeSelPlot');" />
<area shape="rect" coords="112,3,247,92" title="Base Pair Step Helical Params: Slide" href="javascript:unhideSelPlots('TimeSlideSel','slideTimeSelPlot');" />
<area shape="rect" coords="248,1,377,89" title="Base Pair Step Helical Params: Rise" href="javascript:unhideSelPlots('TimeRiseSel','riseTimeSelPlot');" />
<area shape="rect" coords="1,91,105,186" title="Base Pair Step Helical Params: Tilt" href="javascript:unhideSelPlots('TimeTiltSel','tiltTimeSelPlot');" />
<area shape="rect" coords="108,92,246,185" title="Base Pair Step Helical Params: Roll" href="javascript:unhideSelPlots('TimeRollSel','rollTimeSelPlot');" />
<area shape="rect" coords="247,91,376,186" title="Base Pair Step Helical Params: Twist" href="javascript:unhideSelPlots('TimeTwistSel','twistTimeSelPlot');" />
</map>

<map name="helicalParamsBPS_Map">
<area shape="rect" coords="1,0,109,87" title="Base Pair Step Helical Params: Shift" href="javascript:unhidePlots('ShiftSel','ShiftPlot');" />
<area shape="rect" coords="112,3,247,92" title="Base Pair Step Helical Params: Slide" href="javascript:unhidePlots('SlideSel','SlidePlot');" />
<area shape="rect" coords="248,1,377,89" title="Base Pair Step Helical Params: Rise" href="javascript:unhidePlots('RiseSel','RisePlot');" />
<area shape="rect" coords="1,91,105,186" title="Base Pair Step Helical Params: Tilt" href="javascript:unhidePlots('TiltSel','TiltPlot');" />
<area shape="rect" coords="108,92,246,185" title="Base Pair Step Helical Params: Roll" href="javascript:unhidePlots('RollSel','RollPlot');" />
<area shape="rect" coords="247,91,376,186" title="Base Pair Step Helical Params: Twist" href="javascript:unhidePlots('TwistSel','TwistPlot');" />
</map>

<map name="axisBP_TimeMap">
<area shape="rect" coords="0,0,110,100" href="javascript:unhideSelPlots('TimeInclinationSel','inclinTimeSelPlot');" />
<area shape="rect" coords="112,2,238,100" href="javascript:unhideSelPlots('TimeTipSel','tipTimeSelPlot');" />
<area shape="rect" coords="0,103,119,188" href="javascript:unhideSelPlots('TimeXdispSel','xdispTimeSelPlot');" />
<area shape="rect" coords="119,102,237,187" href="javascript:unhideSelPlots('TimeYdispSel','ydispTimeSelPlot');" />
</map>

<map name="axisBP_Map">
<area shape="rect" coords="0,0,110,100" href="javascript:unhidePlots('InclinationSel','InclinationPlot');" />
<area shape="rect" coords="112,2,238,100" href="javascript:unhidePlots('TipSel','TipPlot');" />
<area shape="rect" coords="0,103,119,188" href="javascript:unhidePlots('XdispSel','XdispPlot');" />
<area shape="rect" coords="119,102,237,187" href="javascript:unhidePlots('YdispSel','YdispPlot');" />
</map>

