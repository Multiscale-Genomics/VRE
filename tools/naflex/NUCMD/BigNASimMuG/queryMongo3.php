<?php
require "phplib/global.inc.php";
require "phplib/aux.inc.php";
#

# Getting REQUEST parameters from Ajax -- php call.
$query = $_REQUEST;

# ajaxCode: info about analysis name, analysis depth and nucleotide type (base/pair/step).
# trajs: list of trajectories to be used as query subset.
# seq: nucleotide subsequence to be used as query subset.
$ajaxCode = $query['anal'];
$trajs = $query['filterTrajs'];
$seq = $query['subSeq'];

# ajaxCode Ex: CURVES,2,step
list($id,$block,$nucType) = preg_split('/,/',$ajaxCode);

# Important session variables:
# Block: determines analysis query depth. Ex: [A][CURVES][backbone_torsions] (0,1,2,...)
#	Block 1 --> Base/Base-Pair/Base-Pair Step
#	Block 2 --> Curves / Stiffness / HBs, etc.
#	Block 3 --> 
# index: Array index, must begin with 0, so we subtract 1 from block to match both vars.

# Php arrays begin with 0.
$index = $block - 1;

# Initializing Session vars if Analysis Depth < 2 (first level)
if ( $block < 2 ) {
        $_SESSION['mg_analId'] = Array();
	$_SESSION['mg_links'] = Array();
}

# Base-Pair case, adding "-" between bases.
$codeIdIni = $id;
if ($nucType != "step" and strlen($id) > 1 and $block < 2){
	$codeIdIni = implode("-",str_split($id));
}

# Saving analysis code for this level and building mongoId string
# with all analysis joined by "."
$_SESSION['mg_links'][$index] = $ajaxCode;
$_SESSION['mg_analId'][$index] = $codeIdIni;
foreach ($_SESSION['mg_analId'] as $k => $v) {
	$mongoId.=".$v";
}
$mongoId= ltrim ($mongoId, '.'); # Removing first dot

# New level, block++
$newblock = $block + 1;

# Now depending on the analysis depth, act accordingly:
switch ($block) {
        case "1":

		$listAnalysis = $GLOBALS["listAnalysis"];
		$title = buildTitleNav ($_SESSION['mg_links'],$_SESSION['mg_analId'],$block);

		?>
		<hr/>
			<?=$title?>
		<hr/>
		<?php
		    $regexp = buildRegExp("_id.idGroup",$id,$nucType);

		    # Hard-coded list of first-level Analysis, by now (Feb-2015): Curves, Stiffness, Stacking and HBs.
		    while (list($key, $value) = each($listAnalysis))
		    {
			$time_start = microtime(true);

			$fcond = buildMongoQuery($regexp,$value,$trajs);

			$mg_time_start = microtime(true);
			$results = $analData->count( $fcond ); 
			$mg_time_end = microtime(true);
			$mg_execution_time = ($mg_time_end - $mg_time_start);
			$mg_execution_time_min = ($mg_execution_time)/60;

#print "<pre>";
#print "Block: $block, Id ($i): ".$mongoId."<br/>";
#print json_encode($fcond);
#print "</pre>";


			#$curs = $analData->find( $fcond ); 
			#$curs->timeout(-1);
			#$results = $curs->count();

                        $code = $value.",".$newblock.",".$nucType;
			if($results > 0){
			?>
        		<article>
	                    <a id="<?=$code?>" name="block<?=$newblock?>" href="queryMongo.php" target="_blank">
				<?php
					printQueryImage($value);
				?>
       		              <!--<div id="imgDNA"><img src="images/DNA_icon_64x.png"></div>-->
	               	      <div class="titleLink"><?=$value?> Analysis <br/>(<?=$results?>)</div>
       		            </a>
       			</article>
			<?php
			}
			$time_end = microtime(true);
                        $execution_time = ($time_end - $time_start);
                        $execution_time_min = ($execution_time)/60;

#print "<pre>";
#print "Block: $block, Id ($i): ".$mongoId."<br/>";
#print json_encode($fcond);
#print "<br/><b>Mongo Execution Time:</b> $mg_execution_time s. ($mg_execution_time_min Mins)";
#print "<br/><b>Total Execution Time:</b> $execution_time s. ($execution_time_min Mins)<br/>";
#print "</pre>";


		}
            break;

	case "2":

		$title = buildTitleNav ($_SESSION['mg_links'],$_SESSION['mg_analId'],$block);

		if ( preg_match('/HBs/',$mongoId) or preg_match('/NMR/',$mongoId)){
			list($type,$anal) = explode('.', $mongoId);
			#print "$type - $anal";

                        $type = preg_replace('/-/','',$type);
                        $regexp = buildRegExp("_id.idGroup",$type,$nucType);

                        $fcond = buildMongoQuery($regexp,$anal,$trajs);

#print "<pre>";
#print "Block: $block, Id ($i): ".$mongoId."<br/>";
#print json_encode( $fcond );
#print "</pre>";
                        $results = $analData->find( $fcond , array("$anal" =>1) );

                        $plotFileName = "dat2plot_".rand().".dat";
			plot_mongo_boxplot($results,$anal,$plotFileName);

			?>

                        <hr/>
				<?=$title?>
                        <hr/>
			<div style="width: 100%">
                        <a name="plot" href="tmpPlots/<?=$plotFileName?>.png" class="preview jqueryImages"><img style="width: initial;" src="tmpPlots/<?=$plotFileName?>.png"></a>
                        <br/>
                        <a href="getFile.php?fileloc=tmpPlots/<?php echo $plotFileName ?>&type=curves"> <p class="curvesDatText">Download Raw Data</p></a>
			</div>
                       	<div name="block5" style="width: 100%">
			<?php
			if (preg_match('/NMR/',$mongoId)){
				buildResultsTableExpandableHB($results,$anal);
			}
			else{
				buildResultsTableExpandable("HB",$anal,$results);
			}
				print "</div>\n";
		}
		else{
			$time_start = microtime(true);

			list($type,$anal) = explode('.', $mongoId);
			$lenType = strlen($type);
			$type = preg_replace('/-/','',$type);
			$regexp = buildRegExp("_id.idGroup",$type,$nucType);
			
			$fcond = buildMongoQuery($regexp,$anal,$trajs);

			# While we discover how to efficiently run a distinct in 
			# a sharded mongo collection, we will do a trick:
			if( $anal == "CURVES" and ($lenType == 2) ){
				$anal_bps = "CURVES.helical_bpstep";
				$fcond_bps = buildMongoQuery($regexp,$anal_bps,$trajs);
				$results_bps = $analData->count( $fcond_bps ); 
				$results['helical_bpstep'] = $results_bps;
				$analHash['helical_bpstep'] = $results_bps;
			
				$anal_grooves = "CURVES.grooves";
				$fcond_grooves = buildMongoQuery($regexp,$anal_grooves,$trajs);
				$results_grooves = $analData->count( $fcond_grooves ); 
				$results['grooves'] = $results_grooves;
				$analHash['grooves'] = $results_grooves;
			}
			elseif( $anal == "CURVES" and ($lenType == 3) ){
				$anal_bps = "CURVES.axis_bp";
				$fcond_bps = buildMongoQuery($regexp,$anal_bps,$trajs);
				$results_bps = $analData->count( $fcond_bps ); 
				$results['axis_bp'] = $results_bps;
				$analHash['axis_bp'] = $results_bps;
			
				$anal_grooves = "CURVES.helical_bp";
				$fcond_grooves = buildMongoQuery($regexp,$anal_grooves,$trajs);
				$results_grooves = $analData->count( $fcond_grooves ); 
				$results['helical_bp'] = $results_grooves;
				$analHash['helical_bp'] = $results_grooves;
			}
			elseif( $anal == "CURVES" and ($lenType == 1) ){
				$anal_bps = "CURVES.backbone_torsions";
				$fcond_bps = buildMongoQuery($regexp,$anal_bps,$trajs);
				$results_bps = $analData->count( $fcond_bps ); 
				$results['backbone_torsions'] = $results_bps;
				$analHash['backbone_torsions'] = $results_bps;
			}
			else{
				$mg_time_start = microtime(true);
				$results = $analData->find( $fcond , array("$anal" =>1) ); 
				$mg_time_end = microtime(true);
				$mg_execution_time = ($mg_time_end - $mg_time_start);
				$mg_execution_time_min = ($mg_execution_time)/60;

				$while_time_start = microtime(true);
				$analHash = array();
                        	foreach ($results as $k) {

					$mg_id = $k['_id'];
					$mg_curves = $k[$anal];
                	        	foreach ($mg_curves as $key => $value) {
						$analHash[$key] ++;
					}
				}
				$while_time_end = microtime(true);
				$while_execution_time = ($while_time_end - $while_time_start);
				$while_execution_time_min = ($while_execution_time)/60;
			}
                if (empty($results))
                    print "<h3>No records found</h3>";
                else {
                        ?>
                        <hr/>
				<?=$title?>
                        <hr/>
                        <?php
                        foreach ($analHash as $k => $value) {
                                $code = $k.",".$newblock.",".$nucType;
                                #echo "$k<br/>";
				if($mapProperties[$k])
					$k_txt = $mapProperties[$k];
				else
					$k_txt = ucfirst($k);
                                ?>
                                <article>
                                    <a id="<?=$code?>" name="block<?=$newblock?>" href="queryMongo.php" target="_blank">
				<?php
					printQueryImage($k);
				?>
                                      <!--<div id="imgDNA"><img src="images/DNA_icon_64x.png"></div>-->
                                      <div id="titleLink"><?=$k_txt?> Analysis <br/>(<?=$value?>)</div>
                                    </a>
                                </article>
                                <?php
                        }
			$time_end = microtime(true);
                        $execution_time = ($time_end - $time_start);
                        $execution_time_min = ($execution_time)/60;
#print "<pre>";
#print "Block: $block, Id ($i): ".$mongoId."<br/>";
#print json_encode( $fcond_bps );
#print "<br/>Results: $results_bps";
#print "<br/><b>Mongo Execution Time:</b> $mg_execution_time s. ($mg_execution_time_min Mins)";
#print "<br/><b>While Execution Time:</b> $while_execution_time s. ($while_execution_time_min Mins)<br/>";
#print "<br/><b>Total Execution Time:</b> $execution_time s. ($execution_time_min Mins)<br/>";
#print "</pre>";

                }
		}
            break;

	case "3":
	
	    $title = buildTitleNav ($_SESSION['mg_links'],$_SESSION['mg_analId'],$block);

	    if ( (! preg_match('/STACKING/',$mongoId)) and (! preg_match('/NMR/',$mongoId)) ){

		list($type,$anal,$subanal) = explode('.', $mongoId);
		$lenType = strlen($type);
		$type = preg_replace('/-/','',$type);
		$regexp = buildRegExp("_id.idGroup",$type,$nucType);

		$fcond = buildMongoQuery($regexp,"$anal.$subanal",$trajs);

		# While we discover how to efficiently run a distinct in 
		# a sharded mongo collection, we will do a trick:
		if( $anal == "CURVES" and ($lenType == 1) ){
			$listElems = array("alpha","beta","gamma","chi","epsil","zeta","BI_population","puckering");
			foreach ($listElems as $elem) {
				$anal_bps = "CURVES.backbone_torsions.$elem";
				$fcond_bps = buildMongoQuery($regexp,$anal_bps,$trajs);
				$results_bps = $analData->count( $fcond_bps ); 
				$results[$elem] = $results_bps;
				$analHash[$elem] = $results_bps;
			}
		}			
		else{
#print "<pre>";
#print "Block: $block, Id ($i): ".$mongoId."<br/>";
#print json_encode( $fcond );
#print "</pre>";
			$results = $analData->find( $fcond , array("$anal.$subanal" => 1)); 

			$analHash = array();
	                foreach ($results as $k) {

				$mg_id = $k['_id'];
				$mg_curves = $k[$anal][$subanal];
        	               	foreach ($mg_curves as $key => $value) {
					if (preg_match('/canonical/', $key))
						continue;
					$analHash[$key] ++;
				}
			}
		}
                if (empty($results))
                    print "<h3>No records found</h3>";
                else {
                        ?>
                        <hr/>
				<?=$title?>
                        <hr/>
                        <?php
                        foreach ($analHash as $k => $value) {
                                $code = $k.",".$newblock.",".$nucType;
				#$k = preg_replace('/_avg/','',$k);
				if($k == "PROD" or $k == "MAT" or $k == "phase" or $k == "diff")
					continue;
				if($mapProperties[$k])
					$k_txt = $mapProperties[$k];
				else
					$k_txt = ucfirst($k);
                                ?>
                                <article>
                                    <a id="<?=$code?>" name="block<?=$newblock?>" href="queryMongo.php" target="_blank">
				<?php
					printQueryImage($k);
				?>
                                      <!--<div id="imgDNA"><img src="images/DNA_icon_64x.png"></div>-->
                                      <div id="titleLink"><?=$k_txt?> Analysis <br/>(<?=$value?>)</div>
                                    </a>
                                </article>
                                <?php
                        }
                }
	    }
	    else {  // If STACKING 
		list($type,$anal,$property) = explode('.', $mongoId);
		$type = preg_replace('/-/','',$type);
		$regexp = buildRegExp("_id.idGroup",$type,$nucType);
			
		$fcond = buildMongoQuery($regexp,"$anal.$property",$trajs);

#print "<pre>";
#print "Block: $block, Id ($i): ".$mongoId."<br/>";
#print json_encode($fcond);
#print "</pre>";

logger("Case 3:");
logger(json_encode($fcond));
logger(json_encode(array("$anal.$property" => 1)));

		$results = $analData->find( $fcond , array("$anal.$property" => 1)); 

		#db.analData.distinct("_id.idGroup",{"CURVES.grooves.mind": {"$exists":1}})

		$pre_property_txt = preg_replace('/_avg/','',$property);
		if($mapProperties[$pre_property_txt])
			$property_txt = $mapProperties[$pre_property_txt];
		else
			$property_txt = ucfirst($pre_property_txt);

		$mongoId_txt = preg_replace('/_avg/','',$mongoId);

		if (empty($results)) 
		    print "<h3>No records found</h3>";
		else {
                        $plotFileName = "dat2plot_".rand().".dat";
                        $avg_sd = plot_mongo_data($results,$type,$property,$plotFileName);
                        list($avg,$sd) = preg_split('/,/',$avg_sd);
                        $mean = sprintf("%8.3f",$avg);
                        $stdev = sprintf("%8.3f",$sd);
                        ?>
                        <hr/>
                                <?=$title?>
                        <hr/>


                        <div name="block5" style="width: 100%">
                        <a name="plot" href="tmpPlots/<?=$plotFileName?>.png" class="preview jqueryImages"><img style="width: initial;" src="tmpPlots/<?=$plotFileName?>.png"></a>
			<br/>
	                <a href="getFile.php?fileloc=tmpPlots/<?php echo $plotFileName ?>&type=curves"> <p class="curvesDatText">Download Raw Data</p></a>
                        <!--<p> Average: <?=$avg?> , Standard Deviation: <?=$sd?> </p>-->

                        <table cellpadding='15' align='center' border='0' class='tableNMR'>
                        <tr><td></td><td>Mean</td><td>Stdev</td></tr><tr><td><?=$property_txt?></td><td><?=$mean?></td><td><?=$stdev?></td></tr>
                        </table>
			<?php
				buildResultsTableExpandable ($property_txt,$property,$results);
				print "</div>\n";
		}

	    }
            break;

	case "4":
	
		$title = buildTitleNav ($_SESSION['mg_links'],$_SESSION['mg_analId'],$block);

		list($type,$anal,$subanal,$property) = explode('.', $mongoId);
		$type = preg_replace('/-/','',$type);
		$regexp = buildRegExp("_id.idGroup",$type,$nucType);
			
		$fcond = buildMongoQuery($regexp,"$anal.$subanal.$property",$trajs);

#print "<pre>";
#print "Block: $block, Id ($i): ".$mongoId."<br/>";
#print json_encode($fcond);
#print "</pre>";
logger("Case 4:");
logger(json_encode($fcond));
logger(json_encode(array("$anal.$subanal.$property" => 1)));

		$results = $analData->find( $fcond , array("$anal.$subanal.$property" => 1)); 

		#db.analData.distinct("_id.idGroup",{"CURVES.grooves.mind": {"$exists":1}})

		$pre_property_txt = preg_replace('/_avg/','',$property);
		if($mapProperties[$pre_property_txt])
			$property_txt = $mapProperties[$pre_property_txt];
		else
			$property_txt = ucfirst($pre_property_txt);

		$mongoId_txt = preg_replace('/_avg/','',$mongoId);

		if (empty($results)) 
		    print "<h3>No records found</h3>";
		else {
                        $plotFileName = "dat2plot_".rand().".dat";
			#$plotFileName = "dat2plot.dat";
			if(preg_match('/Puckering/', $property_txt)){
				#list($north,$east,$south,$west) = statsPuckering($results);
				list($north,$east,$south,$west) = plot_mongo_puckering($results,$plotFileName);
			}
			else {
				$avg_sd = plot_mongo_data($results,$type,$property,$plotFileName);
				list($avg,$sd) = preg_split('/,/',$avg_sd);
                	        $mean = sprintf("%8.3f",$avg);
                        	$stdev = sprintf("%8.3f",$sd);
			}

			if(preg_match('/BI/', $property_txt)){
				$BI = $mean;
				$BII = 100-$mean;
				plot_mongo_BI_BII($BI,$BII,$plotFileName);
			}

			?>
			<hr/>
				<?=$title?>
			<hr/>


			<div name="block5" style="width: 100%">
			<a name="plot" href="tmpPlots/<?=$plotFileName?>.png" class="preview jqueryImages"><img style="width: initial;" src="tmpPlots/<?=$plotFileName?>.png"></a>
                        <br/>
                        <a href="getFile.php?fileloc=tmpPlots/<?php echo $plotFileName ?>&type=curves"> <p class="curvesDatText">Download Raw Data</p></a>
			<!--<p> Average: <?=$avg?> , Standard Deviation: <?=$sd?> </p>-->

			<table cellpadding='15' align='center' border='0' class='tableNMR'>
			<?php 
				if(preg_match('/BI/', $property_txt)){
			?>
			<tr><td></td><td>% BI (mean)</td><td>% BII (mean)</td></tr>
			<tr><td><?=$property_txt?></td><td><?=$mean?></td><td><?=100-$mean?></td></tr>
			<?php	
				}
				elseif(preg_match('/AlphaGamma/', $property_txt)){
			?>
			<tr><td></td><td>% Alpha (mean)</td><td>% Gamma (mean)</td></tr>
			<tr><td><?=$property_txt?></td><td><?=$mean?></td><td><?=100-$mean?></td></tr>
			<?php	
				}
				elseif(preg_match('/Puckering/', $property_txt)){

			?>
			<tr><td></td><td>% North (mean) </td><td>% East (mean)</td><td>% South (mean)</td><td>% West (mean)</td></tr>
			<tr><td><?=$property_txt?></td><td><?=$north?></td><td><?=$east?></td><td><?=$south?></td><td><?=$west?></td></tr>
			<?php	
				}
				else {
			?>
			<tr><td></td><td>Mean</td><td>Stdev</td></tr><tr><td><?=$property_txt?></td><td><?=$mean?></td><td><?=$stdev?></td></tr>
			<?php 
			}
			?>
			</table>
	
			<?php
			if(preg_match('/_avg/', $property)){
				buildResultsTableExpandable ($property_txt,$property,$results);
				print "</div>\n";
			}
		}
	break;

	default:
	    print "<pre>";
	    print "Sorry, analysis type not found...";
	    print "</pre>";
}

function search($array, $key)
{
    $results = array();

    if (is_array($array)) {
        if (isset($array[$key])) {
            $results[] = $array;
        }

        foreach ($array as $subarray) {
            $results = array_merge($results, search($subarray, $key));
        }
    }

    return $results;
}

function plot_mongo_data($results,$type,$property,$plotFileName)
{

	# Tmp dir to build plots
	$tmpDir = "tmpPlots";

	# Cleaning tmp dir (rm files > 2 days old)
	$files = glob($tmpDir."*");
	$now   = time();

	foreach ($files as $file)
	  if (is_file($file))
	      if ($now - filemtime($file) >= 60*60*24*2) // 2 days
        	unlink($file);

	# Removing zeros from Grooves Analysis (needs to be fixed)
	$grooves = 0;
	if ( preg_match('/maj/',$property) or preg_match('/min/',$property) ){
		$grooves = 1;
	}

	# Building plot
	$dat2plot=fopen("tmpPlots/$plotFileName", "w");
	foreach ($results as $k) {
		$s = search($k, $property);
		$value = $s[0][$property];
		if(is_array($value)){
			#$value = implode(' ',$value);
			$value = $value[0];
		}
		if(preg_match('/,/', $value)){
			$v = preg_split ("/,/",$value);
			$value = $v[0];
		}
		#foreach ($k as $key => $value2) 
		#	print "$property $key -> $value<br/>";
		#echo "$value";

		if($grooves and !$value) continue;

		fwrite($dat2plot, "$value\n");
	}
	fclose($dat2plot);
	
	# Usage: perl plotHist_R.pl <file.dat> <title> <X_title> <Y_title>
	system("$GLOBALS[scriptsDir]/plotHist_R.pl tmpPlots/$plotFileName $type $property \"Property Histogram\" \"$property\" \"Frequency\" >& tmpPlots/$plotFileName.error");

	$avgTxt = shell_exec("grep NAFlex_avg tmpPlots/$plotFileName.error | grep -v cat");
	$sdTxt = shell_exec("grep NAFlex_sd tmpPlots/$plotFileName.error | grep -v cat");

	#echo "-$avgTxt- -$sdTxt-";

	$avg = preg_replace('/NAFlex_avg/','',$avgTxt);
	$sd = preg_replace('/NAFlex_sd/','',$sdTxt);
	
	return "$avg,$sd";
}

function plot_mongo_boxplot($results,$anal,$plotFileName)
{

	# Tmp dir to build plots
	$tmpDir = "tmpPlots";

	# Building plot
	$analHash = array();
	foreach ($results as $k) {
		$mg_id = $k['_id'];
		$mg_curves = $k[$anal];
		foreach ($mg_curves as $key => $value) {
			if (!is_array($analHash[$key])) 
				$analHash[$key] = array();
			array_push($analHash[$key],$value[0]);
		}
	}
	
	foreach  ($analHash as $type => $values) {
		$datFile=fopen("tmpPlots/$plotFileName.$type.HB.dat", "w");
		foreach  ($values as $v) {
			fwrite($datFile,"$v\n");
		}
		fclose($datFile);
	}

	# Usage: perl plotBoxPlotsR.pl 
	chdir("$tmpDir");
	if( preg_match('/HB/',$anal)) {
		$title = "'HB distances'";
		$xaxis = "";
		$yaxis = "'Distance (Angstroms)'";
	}
	if (preg_match('/NMR_JC/',$anal)) {
		$title = "'NMR J-Coupling'";
		$xaxis = "";
		$yaxis = "'J-Coupling (Hz)'";
	}
	if (preg_match('/NMR_NOE/',$anal)) {
		$title = "'NMR NOEs'";
		$xaxis = "";
		$yaxis = "'NOE Distance (Angstroms)'";
	}

	system("../$GLOBALS[scriptsDir]/plotBoxPlotsR.pl $plotFileName $plotFileName $title $yaxis $xaxis>& $plotFileName.boxplot.error");
}

function buildMongoQuery ($regexp,$toSearch,$trajs)
{
	$cond = Array();
	$cond[] = $regexp;
	$cond[] = array("$toSearch" => array ('$exists' => 1));
	#$cond[] = array("_id.nSnap" => 0);
	if(!empty($trajs)){
                $in = array('_id.idSim' => array('$in' => $trajs));
       		$cond[] = $in;
	}
	$fcond = array('$and' => $cond);
	return $fcond;
}

function buildTitleNav ($arrayLinks,$arrayNames,$level){

	$title = "<h4>Global Analyses: ";

	for ($i=0;$i<$level;$i++) {
		$link = $arrayLinks[$i];
		$name = $arrayNames[$i];
		$title.="[<a id='".$link."' name='block1' href=''>".$name."</a>]";
	}

	$title.="</h4>";

	return $title;
}

function buildResultsTable ($property_txt,$property,$results){

	print "<table class='searchGroups'>\n";
	print "<thead>\n";
	print "<tr>\n";
	print "<th>TrajId</th>\n";
	print "<th>NucType</th>\n";
	print "<th>NucId</th>\n";
	print "<th>$property_txt</th>\n";
	print "</tr>\n";
	print "</thead>\n";
	print "<tbody>\n";

	foreach ($results as $k) {

		$s = search($k, $property);
		$value_raw = $s[0][$property];
		if(is_array($value_raw)){
			$value = "";
			foreach ($value_raw as $vv) { 
                        	$vv = sprintf("%8.3f",$vv);
				$value.="$vv,";
			}
			$value = rtrim ($value, ','); # Removing last comma
		}
		else{
			$value = sprintf("%8.3f",$value_raw);
		}
		$s = search($k, 'idSim');
		$idTraj = $s[0]['idSim'];
		$s = search($k, 'idGroup');
		$idGroup = $s[0]['idGroup'];
		if(strlen($idGroup) == 2){
			$idGroup = implode("-",str_split($idGroup));
		}
		$s = search($k, 'nGroup');
		$nGroup = $s[0]['nGroup'];
		$s = search($k, 'nSnap');
		$nSnap = $s[0]['nSnap'];

		print "<tr>\n";
		print "<td>$idTraj</td>\n";
		print "<td>$idGroup</td>\n";
		print "<td>$nGroup</td>\n";
		print "<td>$value</td>\n";
		print "</tr>\n";
	}

	print "</tbody>\n";
	print "</table>\n";
}

function buildResultsTableExpandableHB ($results,$anal){

	$resArray = Array();
        foreach ($results as $k) {
		$cont = 0;
                $mg_id = $k['_id'];

		#foreach ($mg_id as $a => $b){
		#	echo "MG_id: $a $b<br/>";
		#}
		#echo "<br/><br/>";
		$idTraj = $mg_id['idSim'];
		$idGroup = $mg_id['idGroup'];
		$nGroup = $mg_id['nGroup'];
                $mg_curves = $k[$anal];
                foreach ($mg_curves as $key => $value) {
                        if (!is_array($analHash[$key]))
                                $analHash[$key] = array();
			#echo "$idTraj $key $value[0]<br/>";

			$value = sprintf("%8.3f, (%8.3f)",$value[0],$value[1]);
			$resArray[$idTraj][$cont]['nucType'] = $idGroup;
			$resArray[$idTraj][$cont]['nucId'] = $nGroup;
			$resArray[$idTraj][$cont]['key'] = $key;
			$resArray[$idTraj][$cont]['value'] = $value;
			$cont++;
                }
        }

	print "<table class='searchGroups' id='expandable'>\n";
	print "<thead>\n";
	print "<tr>\n";
	print "<th>Trajectory Id</th>\n";
	print "<th></th>\n";  # Arrow column
	print "</tr>\n";
	print "</thead>\n";
	print "<tbody>\n";
	
	foreach (array_keys ($resArray) as $k) {

		$descMG = $GLOBALS['simData']->findOne( array('_id' => "$k" ), array('description' => 1) );
		$desc = $descMG[description];

		print "<tr>\n";
		print "<td><div style='padding:0px;' class='simptip-position-top' data-tooltip='".$desc."'>$k</div></td>\n";
		print "<td><div class='arrow simptip-position-top' data-tooltip='".$desc."'></div></td>\n";
		print "</tr>\n";

		#print "<tr>\n";
		#print "<td>$k</td>\n";
		#print "<td><div class='arrow'></div></td>\n";
		#print "</tr>\n";
		print "<tr>\n";
		print "<td colspan='2'>\n";

		print "<table class='searchGroups'>\n";
		print "<thead>\n";
		print "<tr>\n";
		print "<th>TrajId</th>\n";
		print "<th>NucType</th>\n";
		print "<th>NucId</th>\n";
		print "<th>Atom Pair</th>\n";
		print "<th>$anal (avg,stdev)</th>\n";
		print "</tr>\n";
		print "</thead>\n";
		print "<tbody>\n";
		foreach (array_keys ($resArray[$k]) as $k2) {
			print "<tr>\n";
			print "<td class='simptip-position-top' data-tooltip='".$desc."'>$k</td>\n";
			foreach (array_keys ($resArray[$k][$k2]) as $k3) {
				$v = $resArray[$k][$k2][$k3];
				print "<td>$v</td>\n";
			}
			print "</tr>\n";
		}
		print "</tbody>\n";
		print "</table>\n";

		print "</td>\n";
		print "</tr>\n";
	}

	print "</tbody>\n";
	print "</table>\n";
}	

function buildResultsTableExpandable ($property_txt,$property,$results){

	$resArray = Array();
	foreach ($results as $k) {

		$s = search($k, $property);
		$value_raw = $s[0][$property];
		if(is_array($value_raw)){
			$value = "";
			foreach ($value_raw as $vv) { 
                        	$vv = sprintf("%8.3f",$vv);
				$value.="$vv,";
			}
			$value = rtrim ($value, ','); # Removing last comma
		}
		else{
			$value = sprintf("%8.3f",$value_raw);
		}
		$s = search($k, 'idSim');
		$idTraj = $s[0]['idSim'];

		if($idTrajAnt != $idTraj) {
			$cont = 0;
		}

		$s = search($k, 'idGroup');
		$idGroup = $s[0]['idGroup'];
		if(strlen($idGroup) == 2){
			$idGroup = implode("-",str_split($idGroup));
		}
		$s = search($k, 'nGroup');
		$nGroup = $s[0]['nGroup'];
		$s = search($k, 'nSnap');
		$nSnap = $s[0]['nSnap'];
		
		$resArray[$idTraj][$cont]['nucType'] = $idGroup;
		$resArray[$idTraj][$cont]['nucId'] = $nGroup;
		$resArray[$idTraj][$cont]['value'] = $value;
		if(preg_match('/BI/', $property_txt) or preg_match('/Alpha/', $property_txt))
			$resArray[$idTraj][$cont]['value'].=" / ".(100-$value);

		$idTrajAnt = $idTraj;
		$cont++;
	}

	if(preg_match('/Puckering/', $property_txt))
		$property_txt.= " (%North, %East, %South, %West)";

	print "<table class='searchGroups' id='expandable'>\n";
	print "<thead>\n";
	print "<tr>\n";
	print "<th>Trajectory Id</th>\n";
	print "<th></th>\n";  # Arrow column
	print "</tr>\n";
	print "</thead>\n";
	print "<tbody>\n";
	
	foreach (array_keys ($resArray) as $k) {

		$descMG = $GLOBALS['simData']->findOne( array('_id' => "$k" ), array('description' => 1) );
		$desc = $descMG[description];

		print "<tr>\n";
		print "<td><div style='padding:0px;' class='simptip-position-top' data-tooltip='".$desc."'>$k</div></td>\n";
		print "<td><div class='arrow simptip-position-top' data-tooltip='".$desc."'></div></td>\n";
		print "</tr>\n";
		print "<tr>\n";
		print "<td colspan='2'>\n";

		print "<table class='searchGroups'>\n";
		print "<thead>\n";
		print "<tr>\n";
		print "<th>TrajId</th>\n";
		print "<th>NucType</th>\n";
		print "<th>NucId</th>\n";
		print "<th>$property_txt</th>\n";
		print "</tr>\n";
		print "</thead>\n";
		print "<tbody>\n";
		foreach (array_keys ($resArray[$k]) as $k2) {
			print "<tr>\n";
			print "<td class='simptip-position-top' data-tooltip='".$desc."'>$k</td>\n";
			foreach (array_keys ($resArray[$k][$k2]) as $k3) {
				$v = $resArray[$k][$k2][$k3];
				print "<td>$v</td>\n";
			}
			print "</tr>\n";
		}
		print "</tbody>\n";
		print "</table>\n";

		print "</td>\n";
		print "</tr>\n";
	}

	print "</tbody>\n";
	print "</table>\n";
}	

function buildResultsTableExpandable2 ($property_txt,$property,$results){

	$resArray = Array();
	foreach ($results as $k) {


		$s = search($k, $property);
		$value_raw = $s[0][$property];
		if(is_array($value_raw)){
			$value = "";
			foreach ($value_raw as $vv) { 
                        	$vv = sprintf("%8.3f",$vv);
				$value.="$vv,";
			}
			$value = rtrim ($value, ','); # Removing last comma
		}
		else{
			$value = sprintf("%8.3f",$value_raw);
		}
		$s = search($k, 'idSim');
		$idTraj = $s[0]['idSim'];

		if($idTrajAnt != $idTraj) {
			$cont = 0;
		}

		$s = search($k, 'idGroup');
		$idGroup = $s[0]['idGroup'];
		if(strlen($idGroup) == 2){
			$idGroup = implode("-",str_split($idGroup));
		}
		$s = search($k, 'nGroup');
		$nGroup = $s[0]['nGroup'];
		$s = search($k, 'nSnap');
		$nSnap = $s[0]['nSnap'];
		
		$resArray[$idTraj][$cont]['nucType'] = $idGroup;
		$resArray[$idTraj][$cont]['nucId'] = $nGroup;
		$resArray[$idTraj][$cont]['value'] = $value;

		$idTrajAnt = $idTraj;
		$cont++;
	}

	print "<table class='searchGroups' id='expandable'>\n";
	print "<thead>\n";
	print "<tr>\n";
	print "<th>TrajId</th>\n";
	print "<th>NucType</th>\n";
	print "<th>NucId</th>\n";
	print "<th>$property_txt</th>\n";
	print "<th></th>\n";  # Arrow column
	print "</tr>\n";
	print "</thead>\n";
	print "<tbody>\n";
	
	foreach (array_keys ($resArray) as $k) {
		print "<tr>\n";
		print "<td>$k</td>\n";
		print "<td></td>\n";
		print "<td></td>\n";
		print "<td></td>\n";
		print "<td><div class='arrow'></div></td>\n";
		print "</tr>\n";
		print "<tr>\n";
		print "<td colspan='5'>\n";
		foreach (array_keys ($resArray[$k]) as $k2) {
			print "<tr>\n";
			print "<td>$k</td>\n";
			foreach (array_keys ($resArray[$k][$k2]) as $k3) {
				$v = $resArray[$k][$k2][$k3];
				print "<td>$v</td>\n";
			}
			print "</tr>\n";
		}
		print "</td>\n";
		print "</tr>\n";
	}

	print "</tbody>\n";
	print "</table>\n";
}	

function printQueryImage($value){

	$analysis = strtolower($value);

	#echo "Trying to visualize $analysis.png";
	if(file_exists("images/$analysis.png") and filesize("images/$analysis.png")!=0 ){
		print "<div class='imgDNA'><img src='images/".$analysis.".png'></div>\n";
	}
	else{
		#print "<div class='imgDNA'><img src='images/DNA_icon_64x.png'></div>\n";
		print "<div class='imgDNA'><img src='images/DNA_analysis.jpeg'></div>\n";
	}
}

function formatFloat (&$v,$k) {
	echo "$v";
	$v = sprintf("%8.3f",$v);
}

// Function to calculate square of value - mean
function sd_square($x, $mean) { return pow($x - $mean,2); }

// Function to calculate standard deviation (uses sd_square)    
function sd($array) {
    
// square root of sum of squares devided by N-1
return sqrt(array_sum(array_map("sd_square", $array, array_fill(0,count($array), (array_sum($array) / count($array)) ) ) ) / (count($array)-1) );
}

// Function to calculate mean of an array
function mean($array) { return array_sum($array) / count($array); }

function statsPuckering($results) {

	$property = "puckering";

	$east = array();
	$west = array();
	$north = array();
	$south = array();
        foreach ($results as $k) {
                $s = search($k, $property);
                $value_raw = $s[0][$property];
                if(is_array($value_raw)){
			array_push($north,$value_raw[0]);
			array_push($east,$value_raw[1]);
			array_push($south,$value_raw[2]);
			array_push($west,$value_raw[3]);
                }
	}

	$mean1 = mean($north); $m_north = sprintf("%8.3f",$mean1);
	$mean2 = mean($east); $m_east = sprintf("%8.3f",$mean2);
	$mean3 = mean($south); $m_south = sprintf("%8.3f",$mean3);
	$mean4 = mean($west); $m_west = sprintf("%8.3f",$mean4);

	return array($m_north,$m_east,$m_south,$m_west);	
}

function plot_mongo_puckering($results, $plotFileName) {

	$property = "puckering";

	# Tmp dir to build plots
	$tmpDir = "tmpPlots";

	$east = array();
	$west = array();
	$north = array();
	$south = array();

        foreach ($results as $k) {
                $s = search($k, $property);
                $value_raw = $s[0][$property];
		$value = '';
                if(is_array($value_raw)){
			array_push($north,$value_raw[0]);
			array_push($east,$value_raw[1]);
			array_push($south,$value_raw[2]);
			array_push($west,$value_raw[3]);
                }
	}

	$mean1 = mean($north); $m_north = sprintf("%8.3f",$mean1);
	$mean2 = mean($east); $m_east = sprintf("%8.3f",$mean2);
	$mean3 = mean($south); $m_south = sprintf("%8.3f",$mean3);
	$mean4 = mean($west); $m_west = sprintf("%8.3f",$mean4);

	# Building plot
	$dat2plot=fopen("tmpPlots/$plotFileName", "w");
	$value = "$m_north $m_east $m_south $m_west";
	fwrite($dat2plot, "$value\n");
	fclose($dat2plot);

	# Usage: perl plotBoxPlotsR.pl 
	chdir("$tmpDir");
	$title = "'Sugar Puckering'";
	$xaxis = "";
	$yaxis = "'(%)'";
	system("../$GLOBALS[scriptsDir]/plotPuckeringR.pl $plotFileName $plotFileName $title $yaxis $xaxis>& $plotFileName.boxplot.error");

	return array($m_north,$m_east,$m_south,$m_west);	
}

function plot_mongo_BI_BII($BI,$BII,$plotFileName) {

	# Tmp dir to build plots
	$tmpDir = "tmpPlots";

	# Building plot
	$dat2plot=fopen("tmpPlots/$plotFileName", "w");
	$value = "$BI $BII";
	fwrite($dat2plot, "$value\n");
	fclose($dat2plot);

	chdir("$tmpDir");
	$title = "'BI/BII Population'";
	$xaxis = "";
	$yaxis = "'(%)'";
	system("../$GLOBALS[scriptsDir]/plotBI-BII_R.pl $plotFileName $plotFileName $title $yaxis $xaxis>& $plotFileName.boxplot.error");
}

