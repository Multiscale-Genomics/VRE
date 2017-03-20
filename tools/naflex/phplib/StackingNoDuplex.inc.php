	<div id="AvgVsTime">
	</br></br></br>
         <table cellpadding="15" align="" border="0" class="avgParms" style="margin-left:40px;">
                <tr>
                        <td class="curvesText"><a id='AVGSel' class='unselected' href="javascript:unhideNmrAvgPlots('AVGSel','Proton_Pairs_Avg');" title='AVG Results'>Contact Maps</a></td>
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
		<table align="" cellpadding="0" width="950" style="margin-left:40px;">
		<tr><td style="vertical-align:top; text-align:left">
                  <p class="curvesText"><a id='stackingMeanSel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('stackingMeanSel_Avg','stackingAvgMeanSelPlot');" title='Stacking energies - Mean'>HB/Stacking Energies - Mean</a></p><br/>
                  <p class="curvesText"><a id='stackingMinSel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('stackingMinSel_Avg','stackingAvgMinSelPlot');" title='Stacking energies - Min'>HB/Stacking Energies - Min</a></p><br/>
                  <p class="curvesText"><a id='stackingMaxSel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('stackingMaxSel_Avg','stackingAvgMaxSelPlot');" title='Stacking energies - Max'>HB/Stacking Energies - Max</a></p><br/>
                  <p class="curvesText"><a id='stackingStdevSel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('stackingStdevSel_Avg','stackingAvgStdevSelPlot');" title='Stacking energies - Stdev'>HB/Stacking Energies - Stdev</a></p><br/>
                  <p class="curvesText"><a id='stackingZscoreSel_Avg' class='unselected' href="javascript:unhideSelNmrAVGPlots('stackingZscoreSel_Avg','stackingAvgZscoreSelPlot');" title='Stacking energies - Relative Standard Deviation'>HB/Stacking Energies - RSD</a></p><br/>
		</td>
		<td id="Rib_Avg_images" rowspan="6" align="center" style="vertical-align:top; text-align:left">
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

        <div id="Proton_Pairs_Params" class="hidden" style="text-align:center;">
		<table align="center" cellpadding="0" width="950" style="margin-left:40px;">
		<tr><td>
		  <p id="TimeStacking" class="curvesText"><a id='Nuc-Nuc_StackingTimeSel' class='unselected' href="javascript:unhideSelStackingPlots('Nuc-Nuc_StackingTimeSel','Nuc-Nuc_StackingTimeSelPlot','<? echo $length1 ?>');" title='Stacking energies'>Stacking Energies</a></p><br/>
		  <p id="TimeHB" class="curvesText"><a id='Nuc-Nuc_HBTimeSel' class='unselected' href="javascript:unhideSelStackingPlots('Nuc-Nuc_HBTimeSel','Nuc-Nuc_HBTimeSelPlot');" title='Hydrogen Bond energies'>Hydrogen Bond Energies</a></p><br/>
		</td>
		<td id="Rib_images" rowspan="6" align="center">
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

                <div id="stackingAvgMeanSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;">
			<?php plotAVG ($userPath,"energies/stackingContactMapsMEAN"); ?>
                </div>
                <div id="stackingAvgMinSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;">
			<?php plotAVG ($userPath,"energies/stackingContactMapsMIN"); ?>
                </div>
                <div id="stackingAvgMaxSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;">
			<?php plotAVG ($userPath,"energies/stackingContactMapsMAX"); ?>
                </div>
                <div id="stackingAvgStdevSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;">
			<?php plotAVG ($userPath,"energies/stackingContactMapsSTDEV"); ?>
                </div>
                <div id="stackingAvgZscoreSelPlot" class="hidden" style="text-align:left; padding: 15px 40px;">
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
		print "<table cellpadding='15' align='center' border='0' class='tableNMR'>\n";	
		print "<tr><td></td><td>Mean</td><td>Stdev</td></tr><tr><td>$num</td><td>$mean</td><td>$stdev</td></tr>\n";
		print "</table>\n";
		print "</div>\n";
	}
?>
        <div id="Nuc1-Nuc2_StackingTimeSelPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;"></div>
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
		print "<table cellpadding='15' align='center' border='0' class='tableNMR'>\n";	
		print "<tr><td></td><td>Mean</td><td>Stdev</td></tr><tr><td>$num</td><td>$mean</td><td>$stdev</td></tr>\n";
		print "</table>\n";
		print "</div>\n";
	}
?>
        <div id="Nuc3-Nuc4_StackingTimeSelPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;"></div>
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
		print "<table cellpadding='15' align='center' border='0' class='tableNMR'>\n";	
		print "<tr><td></td><td>Mean</td><td>Stdev</td></tr><tr><td>$num</td><td>$mean</td><td>$stdev</td></tr>\n";
		print "</table>\n";
		print "</div>\n";
	}
?>
        <div id="Nuc1-Nuc3_StackingTimeSelPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;"></div>
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
		print "<table cellpadding='15' align='center' border='0' class='tableNMR'>\n";	
		print "<tr><td></td><td>Mean</td><td>Stdev</td></tr><tr><td>$num</td><td>$mean</td><td>$stdev</td></tr>\n";
		print "</table>\n";
		print "</div>\n";
	}
?>
        <div id="Nuc2-Nuc4_StackingTimeSelPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;"></div>
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
			print "<table cellpadding='15' align='center' border='0' class='tableNMR'>\n";	

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

        <div id="Nuc-Nuc_HBTimeSelPlot" class="hidden" style="text-align:center; background-color: #:#E6E6FA;padding: 15px 20px;"></div>
        </div>

	</div>

	<br/><br/>	
