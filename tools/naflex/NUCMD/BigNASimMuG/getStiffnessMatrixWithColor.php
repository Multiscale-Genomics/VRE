<?php

header('Access-Control-Allow-Origin: *'); 

#require "phplib/session.inc.php";

?>

<head>
<style>
.curvesDatText {
	-moz-box-shadow:inset 1px 0px 18px 7px #f29c93;
	-webkit-box-shadow:inset 1px 0px 18px 7px #f29c93;
	box-shadow:inset 1px 0px 18px 7px #f29c93;
	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #fe1a00), color-stop(1, #ce0100) );
	background:-moz-linear-gradient( center top, #fe1a00 5%, #ce0100 100% );
	-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr='#fe1a00', endColorstr='#ce0100')";
	background-color:#fe1a00;
	-moz-border-radius:6px;
	-webkit-border-radius:6px;
	border-radius:6px;
	border:1px solid #d83526;
	display:inline-block;
	color:#ffffff;
	font-family:arial;
	font-size:12px;
	font-weight:bold;
	padding:6px 24px;
	text-decoration:none;
	text-shadow:1px 3px 1px #b23e35;
}

.curvesDatText:hover {
	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #ce0100), color-stop(1, #fe1a00) );
	background:-moz-linear-gradient( center top, #ce0100 5%, #fe1a00 100% );
	-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr='#ce0100', endColorstr='#fe1a00')";
/*	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ce0100', endColorstr='#fe1a00'); */
	background-color:#ce0100;
}

.curvesDatText:active {
	position:relative;
	top:1px;
}
</style>
</head>

<?php

$name = $_REQUEST["code"];
$num = $_REQUEST["bps"];

if (!$name or !$num){
	echo "BIGNASim code and Base Pair Step input parameters are mandatory.\n";
	echo "Example: http://mmb.irbbarcelona.org/BigNASim/getStiffnessMatrix.php?code=NAFlex_DDD_bsc1&bps=2\n";
	exit;
}

$ls = exec("ls ../NAFlex2/NAFlex-Data/NAFlex_parmBSC1/$name/STIFFNESS/FORCE_CTES/*.$num.cte");

if ($ls){
	$f_location = $ls;

	$aname = preg_split("/\//",$ls);
	$aname2 = preg_split("/\./",$aname[7]);
	$napos = strtoupper($aname2[0])."-$aname2[1]";

	if (file_exists($f_location) && is_readable($f_location)) {
	
		$st = file_get_contents($f_location);

		$b = explode ('\n',$st);
		$c = implode ('',$b);
		$d = trim($c);
		$array = preg_split("/\s+/",$d);
		$a = array();
		$a["Stiffness for $napos"] = $array;

		#echo json_encode($a);
		#echo "\n";

		$parts = preg_split('/\./',$realFile);
		$num = $parts[1]."-".strtoupper($parts[0]);

print '<div id="stiffness.'.$num.'">';

		$code = generateStiffnessTable($f_location);
		print "$code";
		writeTableHtml($f_location,$code);
print '</div>';
print '<div style="clear:both";></div>';
print '<div>';
                ?>
    <p style='text-align:center;'><b>Units:<br/> </b><i>Diagonal Shift/Slide/Rise in kcal/(mol*&Aring;&sup2;), Diagonal Tilt/Roll/Twist in kcal/(mol*degree&sup2;)<br/>
                        Out of Diagonal: Shift/Slide/Rise in kcal/(mol*&Aring;), Out of Diagonal Tilt/Roll/Twist in kcal/(mol*degree)</i></p>

                        <table align="center" border="0"><tr><td>
                        </td><td>
                        <a href="getFile.php?fileloc=<?php echo $f_location ?>&type=curves"> <p align="right" class="curvesDatText">Download Raw Data</p></a>
                        </td><td>
                        <p align="right" class="curvesDatText" onClick="javascript:window.open('<?php echo $f_location.".html" ?>','Stiffness Params','_blank,resize=1,width=700,height=400');">Open in New Window</p><br/>
                        </td></tr></table>
	</div>
                <?php
	}
	else{
		echo "Sorry, stiffness matrix for BIGNASim code $name and Base Pair Step $num not found...\n";
	}
}
else{
	echo "Sorry, stiffness matrix for BIGNASim code $name and Base Pair Step $num not found...\n";
}

function generateStiffnessTable ($tableFile) {

        $hp = array(Shift,Slide,Rise,Tilt,Roll,Twist);

        $code = "<table cellpadding='15' border='0' style='margin-left:auto; margin-right:auto'>\n";
        $code .="<tr align='center' style='background-color:#dcdcdc'><td style='background-color:#ffffff'></td><td>Shift</td><td>Slide</td><td>Rise</td><td>Tilt</td><td>Roll</td><td>Twist</td></tr>\n";

        # gtac.8.cte
        #$tableFile = "FORCE_CTES/gtac.8.cte";
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
        while(!feof($ftable)) {
                $line=fgets($ftable);
                if($line!=""){
                        $array = preg_split("/\s+/",$line);
                        $code .= "<tr>\n";
                        $code .= "<td style='background-color:#dcdcdc'>$hp[$i]</td>\n";
                        foreach (array_values($array) as $value) {
                                $value = trim($value);
                                if($value != ""){
                                        $value = sprintf("%8.3f",$value);
                                        $rgb = tableColor($value,$min);
                                        $code .= "<td style='background-color: rgb($rgb)'>$value</td>\n";
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

function tableColor($number,$min) {

  $num = ($number - $min) / 10;
  $percent = 255 * $num;

  $r = 255;
  $g = sprintf("%d",240 - $percent);
  $b = sprintf("%d",240 - $percent);

  return "$r,$g,$b";
}


function writeTableHtml ($file,$code){

        $htmlFile = "$file.html";
        $fout=fopen($htmlFile, "w");
        fwrite ($fout, $code);
        fclose($fout);
}

?>
