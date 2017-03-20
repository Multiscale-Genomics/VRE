<!--
# MDWeb for Nucleic Acids
# Principal Component Analysis with PCAzip.
-->

<?php 

# Mathematic and physic constants (From FlexServ, for PCA analysis)
define ("kB", 1.3806503e-23);
define ("kA", 6.0221415e23);
define ("facJCal", 1.0/4186.0);
define ("pca_temp", 300);

$factorStiffness = kB*kA*facJCal*pca_temp;
logger("Factor Stiffness: $factorStiffness");

?>

<div class="container">
	<table style="text-align:center" border="0" cellpadding="15" align="" style="margin-left:40px">
	<tr><td align="center" rowspan="7" id="insertJmol">

<script type="text/javascript">
  var script="load <?php echo $GLOBALS['BASEURL'].'tools/naflex/'.$anim; ?>; cpk off; trace 0; select all; color red; anim mode palindrome; select backbone; trace 100; color blue; animation fps 5; anim on; background white;";
  var jmol = "jmol";
  jmol_isReady = function(applet) {
        Jmol._getElement(applet, "appletdiv");
  };

  var Info = {
      width: 448,
      height: 450,
      //debug: true,
      color: "white",
      use: "html5",
      //j2sPath: "http://mmb.pcb.ub.es/jsmol/j2s",
      //j2sPath: "http://mmb.irbbarcelona.org/NAFlex2/NUCMD/jsmol/j2s",
			j2sPath: "<?php echo $GLOBALS['BASEURL'].'tools/naflex/'; ?>NUCMD/jsmol/j2s",
      script: script,
      readyFunction: jmol_isReady,
      disableInitialConsole: true
  };


</script>

<!--
		<script type="text/javascript">
			jmolInitialize("<?php echo $GLOBALS['homeURL']?>/Jmol/", "JmolAppletSigned.jar");
			jmolCheckBrowser("popup", "error.php?idErr=15", "now");
		</script>
		<script type="text/javascript">
			var script="load <?php echo "getFile.php?type=animation&amp;fileloc=$anim"?>; cpk off; trace 0; select all; color red; anim mode palindrome; select backbone; trace 100; color blue; animation fps 5; anim on; background white;";
			jmolApplet(450, script);
		</script>
-->
	</td></tr><tr><td style="background-color: #ccc;">
		<form method="get" action="<?php echo $GLOBALS['BASEURL']; ?>tools/naflex/output.php">
			<p style="font-size:16px;"> Animation mode <br/><br/><select size="1" name="evec">
		<?php
			$numVectors = 10;
			for ($i=1; $i<=$numVectors; $i++) {
				if ($i==$_REQUEST["evec"]) {
					$selected=" selected=\"selected\"";
				} else {
					$selected="";
				}
				echo "<option value=\"$i\"".$selected.">$i</option>";
			}
		?>
			</select></p>

			<input type="hidden" name="type" value= "<?php echo $analysisType; ?>" />
			<input type="hidden" name="proj" value="<?php echo $proj; ?>" />
			<input type="hidden" name="op" value="<?php echo $op; ?>" />

			<input class="btn blue" type="submit" id="SendButton" value="View" />
		</form>

	</td></tr>

	<tr style="background-color: #86868c;"><td style="color:#fff;padding:5px 15px;">Eigen Value: <?php printf ("%.3f", getEigenvalue($op,$evec)); ?> &Aring;&sup2;</td></tr>
	<tr style="background-color: #86868c;"><td style="color:#fff;padding:5px 15px;">Collectivity Index: <?php printf ("%.3f", getCollectivity($op,$evec)); ?></td></tr>
	<tr style="background-color: #86868c;"><td style="color:#fff;padding:5px 15px;">Eigen Vector Stiffness Constant: <?php printf ("%.5f", $factorStiffness/getEigenvalue($op,$evec)); ?> kcal/(mol*&Aring;&sup2;)</td>
	<br/>
	<tr><td><a href="<?php echo $GLOBALS['BASEURL']; ?>tools/naflex/getFile.php?fileloc=<?php echo $anim ?>&type=curves"> <p align="right" class="btn blue">Download Animation</p></a></td>

	</tr></table>

        <div id="StatsTable" style="margin:0;">
        <?php

                $file = $op."_pcazipOut.proj$evec.stats";

                print "<div id='$file'>";
                print "<table cellpadding='15' align='center' border='0' class='tableNMR'>\n";

                        $cmd = "cat $file";
                        $out = exec($cmd,$content);

                        $dirs = preg_split('/,/',$out);
                        $value1 = preg_split('/:/',$dirs[0]);
                        $value2 = preg_split('/:/',$dirs[1]);
                        $mean = sprintf("%8.3f",$value1[1]);
                        $stdev = sprintf("%8.3f",$value2[1]);

                        print "<tr><td></td><td>Mean</td><td>Stdev</td></tr><tr><td>Eigen Vector $evec</td><td>$mean</td><td>$stdev</td></tr>\n";

                        print "</table>\n";
                        print "</div>\n";
        ?>

        </div>

	<div align="" style="">
        		<img border="1" src='<?php echo $GLOBALS['BASEURL']; ?>tools/naflex/<?php echo "$plot"?>' width="900" align="center">
	</div>
	<div style="clear: both"></div>
	<div id="download">
                <table border="0" align=""><tr><td>
        		<!--<p align="right" class="curvesDatText" onClick='window.open("<?php echo $plot ?>","Trajectory Projection to Vector <?php echo $evec ?>","_blank,resize=1,width=800,height=600");'>Open in New Window</p><br/></td><td>-->
                <a href="<?php echo $GLOBALS['BASEURL']; ?>tools/naflex/getFile.php?fileloc=<?php echo $dat ?>&type=curves"> <p align="right" class="btn blue">Download Raw Data</p></a>
        		</td></tr></table>
	</div>
</div>
<script>
	//$("#insertJmol").html(Jmol.getAppletHtml("jmol",Info));
        //loadJMol();
</script>

