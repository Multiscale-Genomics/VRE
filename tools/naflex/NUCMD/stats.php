<?php
require "phplib/global.inc.php";
require "phplib/aux.inc.php";
print headerMMB("BIGNASim database structure and analysis portal for nucleic acids simulation data");

# Disk used for MD trajs?
$out = exec("df -h | grep NAFlex-Data");
$gdf = preg_split("/\s+/",$out);
$diskDry = $gdf[2];
$ndiskDry = number_format($diskDry);
$diskWet = $ndiskDry * 20;

# We are interested in ALL trajs (Global Statistics)
$trajs = '';

# So, we want to look for any kind of bases/base-pairs/base-pair steps

 $bases = array("A" => A,"C" => C,"G" => G,"T" => T);
 #$bases = array("A/T" => "A","C/G" => "C");
 $basesMulti = array("R" => 1,"Y" => 1);
 #$basepairs = array("A-T" => "AT","C-G" => "CG","T-A" => "TA","G-C" => "GC");
 $basepairs = array("A-T/T-A" => "AT","C-G/G-C" => "CG");
 #$basepairsMulti = array("R-Y" => "RY", "Y-R" => "YR");
 $basepairsMulti = array("R-Y/Y-R" => "RY");
 #$pairs = array("GC" => 1,"GT" => 1,"AC" => 1,"AT" => 1,"CG" => 1,"TG" => 1,"CA" => 1,"TA" => 1,"CC" => 1,"CT" => 1,"TT" => 1,"TC" => 1,"AA" => 1,"AG" => 1,"GG" => 1,"GA" => 1);
 $pairs = array("GC" => 1,"GT" => 1,"GG" => 1,"GA" => 1,"AC" => 1,"AT" => 1,"AA" => 1,"AG" => 1,"CG" => 1,"CC" => 1,"CT" => 1,"CA" => 1,"TA" => 1,"TG" => 1,"TT" => 1,"TC" => 1);
 $pairsMulti = array("RY" => "RY", "YR" => "YR", "YY" => "YY", "RR" => "RR");

# Getting Stats from Mongo DB.

# Number of simulations
$countNumSim = array('_id' => array('$exists' => 1) );
$numSim = $simData->count($countNumSim);

# Total Time Simulated
#db.simData.aggregate([{ $group : { _id: null, totalTime: { $sum : "$time"}}}]);
$ops = array( '$group' => array( '_id' => null, 'totalTime' => array('$sum' => '$time') ) );
$totalTimeMG = $simData->aggregate($ops);
$totalTime = $totalTimeMG[result][0][totalTime];
$totalTime = preg_replace("/int\(/","",$totalTime);
$totalTime = $totalTime / 1000;
$totalTime = number_format($totalTime);

# Total Time Simulated by Molecule Type
#db.simData.aggregate([{ $group : {  _id: "$moleculeType", totalTime: { $sum : "$time"}}}]);
$ops = array( '$group' => array( '_id' => '$moleculeType', 'totalTime' => array('$sum' => '$time') ) );
$totalTimeMG = $simData->aggregate($ops);

$totalTimeMol = array();
foreach ($totalTimeMG[result] as $molType => $resMolType){
	$id = $totalTimeMG[result][$molType][_id];
	$t = $totalTimeMG[result][$molType][totalTime];
	#$t = number_format($t);
	$totalTimeMol[$id] = $t;
}
arsort($totalTimeMol);

# Last Simulation Inserted
#db.simData.find({},{_id:1}).sort({'_id':1}).limit(1)
$lastInserted = $simData->find()->sort(array('date' => 1))->limit(1);

foreach ($lastInserted as $r) {
        $last_inserted_id = $r['_id'];
        $last_inserted_date = $r['date'];
}

# Molecule Types:
$molTypes = Array();
$nucTypes = $simData->distinct("moleculeType");
foreach ($nucTypes as $i => $v) {
	$countNumDna = array('moleculeType' => $v);
	$numDna = $simData->count($countNumDna);
	$molTypes[$v] = $numDna;
}
arsort($molTypes);
$js_molTypes = json_encode($molTypes);
echo "<script> var molTypes = ". $js_molTypes . ";\n</script>\n";

# Base Analysis A/T,C/G
$baseTypes = Array();
$baseTypes_stats = Array();
foreach ($bases as $base=>$v) {
	$stats = getMongoStats($v,$trajs);
	$baseTypes[$base] = $stats;
	$baseTypes_stats[$base] = $stats;
}
# Base Analysis R,Y
foreach ($basesMulti as $base=>$v) {
	$stats = getMongoStats($base,$trajs);
	$baseTypes[$base] = $stats;
}
arsort($baseTypes);
$js_baseTypes = json_encode($baseTypes_stats);
echo "<script> var baseTypes = ". $js_baseTypes . ";\n</script>\n";

# Base-Pair Analysis A-T/T-A,C-G/G-C
$bpTypes = Array();
$bpTypes_stats = Array();
foreach ($basepairs as $pair => $v){
	$stats = getMongoStats($v,$trajs);
	$bpTypes[$pair] = $stats;
	$bpTypes_stats[$pair] = $stats;
}
# Base-Pair Analysis R-Y/Y-R
foreach ($basepairsMulti as $pair => $v){
	$stats = getMongoStats($v,$trajs);
	$bpTypes[$pair] = $stats;
}
arsort($bpTypes);
$js_bpTypes = json_encode($bpTypes_stats);
echo "<script> var bpTypes = ". $js_bpTypes . ";\n</script>\n";
        	
# Base-Pair Step Analysis GC,GT,GG,GA,AC,AT,AA,AG,CG,CC,CT,CA,TA,TG,TT,TC
$bpsTypes = Array();
$bpsTypes_stats = Array();
foreach ($pairs as $pair => $v){
	$stats = getMongoStats($pair,$trajs,"step");
	$bpsTypes[$pair] = $stats;
	$bpsTypes_stats[$pair] = $stats;
}
# Base-Pair Step Analysis RY,YR,YY,RR
foreach ($pairsMulti as $pair => $v){
	$stats = getMongoStats($pair,$trajs,"step");
	$bpsTypes[$pair] = $stats;
}
arsort($bpsTypes);
$js_bpsTypes = json_encode($bpsTypes_stats);
echo "<script> var bpsTypes = ". $js_bpsTypes . ";\n</script>\n";

# ONTOLOGY STATS

$ontoNATypes = Array();
$ontoStructureTypes = Array();
$ontoSystemTypes = Array();
$ontoTrajectoryTypes = Array();
$ontoHelicalTypes = Array();
$ontoFFTypes = Array();
$ontoLenTypes = Array();
$ontoChargeTypes = Array();
$ontoDistinct = $simData->distinct("ontology");
foreach ($ontoDistinct as $i => $v) {
	$countNum = array('ontology' => $v);
	$num = $simData->count($countNum);
	$codeOnto = $ontoHashRev2[$v];
	#echo "$codeOnto: ontoHashRev2[$v] ($ontoHashRev2[$v])<br/>";

	if (preg_match("/^1/",$v) and $v < 999){
		$ontoNATypes[$codeOnto] = $num;
	}
	if (preg_match("/^2/",$v) and $v < 999){
		$ontoStructureTypes[$codeOnto] = $num;
	}
	if (preg_match("/^3/",$v) and $v < 999){
		$ontoSystemTypes[$codeOnto] = $num;
	}
	if (preg_match("/^4/",$v) and $v < 999){
		$ontoTrajectoryTypes[$codeOnto] = $num;
	}
	if (preg_match("/^5/",$v) and $v < 999){
		$ontoHelicalTypes[$codeOnto] = $num;
	}
	if (preg_match("/^801/",$v) and $v < 80200){
		$ontoFFTypes[$codeOnto] = $num;
	}
	if (preg_match("/^802/",$v) and $v < 80300){
		$ontoLenTypes[$codeOnto] = $num;
	}
	if (preg_match("/^805/",$v)){
		$ontoChargeTypes[$codeOnto] = $num;
	}
}
arsort($ontoNATypes);
$js_ontoNATypes = json_encode($ontoNATypes);
echo "<script> var ontoNATypes = ". $js_ontoNATypes . ";\n</script>\n";

arsort($ontoStructureTypes);
$js_ontoStructureTypes = json_encode($ontoStructureTypes);
echo "<script> var ontoStructureTypes = ". $js_ontoStructureTypes . ";\n</script>\n";

arsort($ontoSystemTypes);
$js_ontoSystemTypes = json_encode($ontoSystemTypes);
echo "<script> var ontoSystemTypes = ". $js_ontoSystemTypes . ";\n</script>\n";

arsort($ontoTrajectoryTypes);
$js_ontoTrajectoryTypes = json_encode($ontoTrajectoryTypes);
echo "<script> var ontoTrajectoryTypes = ". $js_ontoTrajectoryTypes . ";\n</script>\n";

arsort($ontoHelicalTypes);
$js_ontoHelicalTypes = json_encode($ontoHelicalTypes);
echo "<script> var ontoHelicalTypes = ". $js_ontoHelicalTypes . ";\n</script>\n";

arsort($ontoFFTypes);
$js_ontoFFTypes = json_encode($ontoFFTypes);
echo "<script> var ontoFFTypes = ". $js_ontoFFTypes . ";\n</script>\n";

arsort($ontoLenTypes);
$js_ontoLenTypes = json_encode($ontoLenTypes);
echo "<script> var ontoLenTypes = ". $js_ontoLenTypes . ";\n</script>\n";

arsort($ontoChargeTypes);
$js_ontoChargeTypes = json_encode($ontoChargeTypes);
echo "<script> var ontoChargeTypes = ". $js_ontoChargeTypes . ";\n</script>\n";

?>
<script>
jQuery(document).ready(function(){

  menuTabs("stats");

  var molTypes_jqplot = [];
  for (var prop_name in molTypes){
	molTypes_jqplot.push([prop_name, molTypes[prop_name]]);
  }
  var baseTypes_jqplot = [];
  for (var prop_name in baseTypes){
	baseTypes_jqplot.push([prop_name, baseTypes[prop_name]]);
  }
  var bpTypes_jqplot = [];
  for (var prop_name in bpTypes){
	bpTypes_jqplot.push([prop_name, bpTypes[prop_name]]);
  }
  var bpsTypes_jqplot = [];
  for (var prop_name in bpsTypes){
	bpsTypes_jqplot.push([prop_name, bpsTypes[prop_name]]);
  }
  var ontoNATypes_jqplot = [];
  for (var prop_name in ontoNATypes){
	ontoNATypes_jqplot.push([prop_name, ontoNATypes[prop_name]]);
  }
  var ontoStructureTypes_jqplot = [];
  for (var prop_name in ontoStructureTypes){
	ontoStructureTypes_jqplot.push([prop_name, ontoStructureTypes[prop_name]]);
  }
  var ontoSystemTypes_jqplot = [];
  for (var prop_name in ontoSystemTypes){
	ontoSystemTypes_jqplot.push([prop_name, ontoSystemTypes[prop_name]]);
  }
  var ontoTrajectoryTypes_jqplot = [];
  for (var prop_name in ontoTrajectoryTypes){
	ontoTrajectoryTypes_jqplot.push([prop_name, ontoTrajectoryTypes[prop_name]]);
  }
  var ontoHelicalTypes_jqplot = [];
  for (var prop_name in ontoHelicalTypes){
	ontoHelicalTypes_jqplot.push([prop_name, ontoHelicalTypes[prop_name]]);
  }
  var ontoFFTypes_jqplot = [];
  for (var prop_name in ontoFFTypes){
	ontoFFTypes_jqplot.push([prop_name, ontoFFTypes[prop_name]]);
  }
  var ontoLenTypes_jqplot = [];
  for (var prop_name in ontoLenTypes){
	ontoLenTypes_jqplot.push([prop_name, ontoLenTypes[prop_name]]);
  }
  var ontoChargeTypes_jqplot = [];
  for (var prop_name in ontoChargeTypes){
	ontoChargeTypes_jqplot.push([prop_name, ontoChargeTypes[prop_name]]);
  }
  //alert("molTypes: " + molTypes_jqplot);

  //var data = [
  //  ['Heavy Industry', 12],['Retail', 9], ['Light Industry', 14],
  //  ['Out of home', 16],['Commuting', 7], ['Orientation', 9]
  //];
  var plot1 = jQuery.jqplot ('chartdiv1', [molTypes_jqplot],
    {
      seriesDefaults: {
        // Make this a pie chart.
        renderer: jQuery.jqplot.PieRenderer,
        rendererOptions: {
          // Put data labels on the pie slices.
          // By default, labels show the percentage of the slice.
          showDataLabels: true,
          // Add a margin to seperate the slices.
          sliceMargin: 4,
          // stroke the slices with a little thicker line.
          lineWidth: 5
        }
      },
      legend: { show:true, location: 'e' }
    }
  );
  var plot2 = jQuery.jqplot ('chartdiv2', [baseTypes_jqplot],
    {
      seriesDefaults: {
        // Make this a pie chart.
        renderer: jQuery.jqplot.PieRenderer,
        rendererOptions: {
          // Put data labels on the pie slices.
          // By default, labels show the percentage of the slice.
          showDataLabels: true,
          // Add a margin to seperate the slices.
          sliceMargin: 4,
          // stroke the slices with a little thicker line.
          lineWidth: 5
        }
      },
      legend: { show:true, location: 'e' }
    }
  );
  var plot3 = jQuery.jqplot ('chartdiv3', [bpTypes_jqplot],
    {
      seriesDefaults: {
        // Make this a pie chart.
        renderer: jQuery.jqplot.PieRenderer,
        rendererOptions: {
          // Put data labels on the pie slices.
          // By default, labels show the percentage of the slice.
          showDataLabels: true,
          // Add a margin to seperate the slices.
          sliceMargin: 4,
          // stroke the slices with a little thicker line.
          lineWidth: 5
        }
      },
      legend: { show:true, location: 'e' }
    }
  );
  var plot4 = jQuery.jqplot ('chartdiv4', [bpsTypes_jqplot],
    {
      seriesDefaults: {
        // Make this a pie chart.
        renderer: jQuery.jqplot.PieRenderer,
        rendererOptions: {
          // Put data labels on the pie slices.
          // By default, labels show the percentage of the slice.
          showDataLabels: true,
          // Add a margin to seperate the slices.
          sliceMargin: 4,
          // stroke the slices with a little thicker line.
          lineWidth: 5,
	  dataLabels: 'label'
        }
      },
      legend: { show:true, location: 'e' }
    }
  );
  var plot5 = jQuery.jqplot ('chartdiv5', [ontoNATypes_jqplot],
    {
      seriesDefaults: {
        // Make this a pie chart.
        renderer: jQuery.jqplot.PieRenderer,
        rendererOptions: {
          // Put data labels on the pie slices.
          // By default, labels show the percentage of the slice.
          showDataLabels: true,
          // Add a margin to seperate the slices.
          sliceMargin: 4,
          // stroke the slices with a little thicker line.
          lineWidth: 5,
        }
      },
      legend: { show:true, location: 'e' }
    }
  );
  var plot6 = jQuery.jqplot ('chartdiv6', [ontoStructureTypes_jqplot],
    {
      seriesDefaults: {
        // Make this a pie chart.
        renderer: jQuery.jqplot.PieRenderer,
        rendererOptions: {
          // Put data labels on the pie slices.
          // By default, labels show the percentage of the slice.
          showDataLabels: true,
          // Add a margin to seperate the slices.
          sliceMargin: 4,
          // stroke the slices with a little thicker line.
          lineWidth: 5,
        }
      },
      legend: { show:true, location: 'e' }
    }
  );

  var plot7 = jQuery.jqplot ('chartdiv7', [ontoSystemTypes_jqplot],
    {
      seriesDefaults: {
        // Make this a pie chart.
        renderer: jQuery.jqplot.PieRenderer,
        rendererOptions: {
          // Put data labels on the pie slices.
          // By default, labels show the percentage of the slice.
          showDataLabels: true,
          // Add a margin to seperate the slices.
          sliceMargin: 4,
          // stroke the slices with a little thicker line.
          lineWidth: 5,
        }
      },
      legend: { show:true, location: 'e' }
    }
  );

  var plot8 = jQuery.jqplot ('chartdiv8', [ontoTrajectoryTypes_jqplot],
    {
      seriesDefaults: {
        // Make this a pie chart.
        renderer: jQuery.jqplot.PieRenderer,
        rendererOptions: {
          // Put data labels on the pie slices.
          // By default, labels show the percentage of the slice.
          showDataLabels: true,
          // Add a margin to seperate the slices.
          sliceMargin: 4,
          // stroke the slices with a little thicker line.
          lineWidth: 5,
        }
      },
      legend: { show:true, location: 'e' }
    }
  );

  var plot9 = jQuery.jqplot ('chartdiv9', [ontoHelicalTypes_jqplot],
    {
      seriesDefaults: {
        // Make this a pie chart.
        renderer: jQuery.jqplot.PieRenderer,
        rendererOptions: {
          // Put data labels on the pie slices.
          // By default, labels show the percentage of the slice.
          showDataLabels: true,
          // Add a margin to seperate the slices.
          sliceMargin: 4,
          // stroke the slices with a little thicker line.
          lineWidth: 5,
        }
      },
      legend: { show:true, location: 'e' }
    }
  );

  var plot10 = jQuery.jqplot ('chartdiv10', [ontoFFTypes_jqplot],
    {
      seriesDefaults: {
        // Make this a pie chart.
        renderer: jQuery.jqplot.PieRenderer,
        rendererOptions: {
          // Put data labels on the pie slices.
          // By default, labels show the percentage of the slice.
          showDataLabels: true,
          // Add a margin to seperate the slices.
          sliceMargin: 4,
          // stroke the slices with a little thicker line.
          lineWidth: 5,
        }
      },
      legend: { show:true, location: 'e' }
    }
  );

  var plot11 = jQuery.jqplot ('chartdiv11', [ontoLenTypes_jqplot],
    {
      seriesDefaults: {
        // Make this a pie chart.
        renderer: jQuery.jqplot.PieRenderer,
        rendererOptions: {
          // Put data labels on the pie slices.
          // By default, labels show the percentage of the slice.
          showDataLabels: true,
          // Add a margin to seperate the slices.
          sliceMargin: 4,
          // stroke the slices with a little thicker line.
          lineWidth: 5,
        }
      },
      legend: { show:true, location: 'e' }
    }
  );

  var plot12 = jQuery.jqplot ('chartdiv12', [ontoChargeTypes_jqplot],
    {
      seriesDefaults: {
        // Make this a pie chart.
        renderer: jQuery.jqplot.PieRenderer,
        rendererOptions: {
          // Put data labels on the pie slices.
          // By default, labels show the percentage of the slice.
          showDataLabels: true,
          // Add a margin to seperate the slices.
          sliceMargin: 4,
          // stroke the slices with a little thicker line.
          lineWidth: 5,
        }
      },
      legend: { show:true, location: 'e' }
    }
  );

        // Ontology Cascade Sheet
        $(".header").click(function () {

            $header = $(this);
            //getting the next element
            $content = $header.next();
            //open up the content needed - toggle the slide- if visible, slide up, if not slidedown.
            $content.slideToggle(500)

	    $plotid =  $content.attr('id');
	    eval($plotid).replot();
        });

});

</script>

<h3>BIGNASim database structure and analysis portal for nucleic acids simulation data</h3>

<div class="metaImageSection">

<div class="statsTable statsContent">
    <div class="wrapper">
        <hr/>
                <p style="text-align: left; font-size:1.1em;font-weight:bold;">BIGNASim Nucleotide MD Simulations DB Global Statistics</p>
		<ul style = "text-align: left;">
			<li><a href="#General"><strong>General Statistics</strong></a></li>
			<li><a href="#BasePairs"><strong>Statistics by Bases / Base-Pairs / Base-Pair Steps</strong></a></li>
			<li><a href="#Ontology"><strong>Statistics by Ontology</strong></a></li>
		</ul>

	<a name='General'></a>
	<br/>
	<p style="font-size:1.2em;font-weight:bold;">General Statistics</p>
	<hr/>
	<div class="table" style="margin-bottom: 5px";>
		<div class="row-header bluegreen">
			<div class="cell"> Total Number of Simulations / Time Simulated</div>
			<div class="cell"> <?=$numSim?> </div>
			<div class="cell"> <?=$totalTime?> &#0181s </div>
		</div>
		<?php
		foreach ($totalTimeMol as $type=>$ntype) {
			$ntype = number_format($ntype);
			$number = $molTypes[$type];
			print "<div class='row'>";
			print "<div class='cell';> $type </div>";
			print "<div class='cell';><strong> $number </strong></div>";
			print "<div class='cell';><strong> $ntype ns </strong></div>";
			print "</div>";
		}
		?>
	</div>

	<br/>

	<div class="table" style="margin-bottom: 5px";>
		<div class="row">
			<div class="cell"> Total Amount of disk used</div>
			<div class="cell"><strong> <?= $ndiskDry?> TB (dry)</strong></div>
			<div class="cell"> <strong> ~ <?= $diskWet?> TB (raw) </strong></div>
		</div>
	</div>

	<br/>

	<div class="table" style="margin-bottom: 5px";>
		<div class="row">
			<div class="cell"> Last Simulation Inserted</div>
			<div class="cell"> <strong> <a href="getStruc.php?idCode=<?=$last_inserted_id?>"><?=$last_inserted_id?></a></strong></div>
			<div class="cell"> <strong> <?=$last_inserted_date?></strong></div>
		</div>
	</div>

	<br/>

	<div class="table header" style="margin-bottom: 5px";>
		<div class="row-header bluegreen">
			<div class="cell"> Simulations by Nucleic Acid Type </div>
			<div class="cell"> <i>(Click to expand/compress)</i></div>
		</div>
	</div>

	<div class="statsInsideVis" id="plot1">

		<div id="chartdiv1" class="statsCharts">
		</div> 

		<div class="table">
			<div class="row-header green">
				<div class="cell"> Type </div>
				<div class="cell"> Number </div>
			</div>
			<?php
			foreach ($molTypes as $type=>$ntype) {
				print "<div class='row'>";
				print "<div class='cell'> $type </div>";
				print "<div class='cell'> $ntype </div>";
				print "</div>";
			}
			?>
		</div>
	</div>

	<br/>

	<a name='BasePairs'></a>
	<br/>
	<p style="font-size:1.2em;font-weight:bold;">Statistics by Bases / Base-Pairs / Base-Pair Steps</p>
	<hr/>
	<div class="table header" style="margin-bottom: 5px";>
		<div class="row-header blue">
			<div class="cell"> Nucleotide Bases Analized </div>
			<div class="cell"> <i>(Click to expand)</i></div>
		</div>
	</div>

	<div class="statsInside" id="plot2">

		<div id="chartdiv2" class="statsCharts">
		</div> 

		<div class="table">
			<div class="row-header green">
				<div class="cell"> Type </div>
				<div class="cell"> Number </div>
			</div>
			<?php 
			foreach ($baseTypes as $type=>$ntype) {
				print "<div class='row'>";
				print "<div class='cell'> $type </div>";
				print "<div class='cell'> $ntype </div>";
				print "</div>";
			}
			?>
		</div>
	</div>

	<div class="table header"  style="margin-bottom: 5px";>
		<div class="row-header blue">
			<div class="cell"> Nucleotide Base-Pairs Analized </div>
			<div class="cell"> <i>(Click to expand)</i></div>
		</div>
	</div>

	<div class="statsInside" id="plot3">

		<div id="chartdiv3" class="statsCharts">
		</div> 

		<div class="table">
			<div class="row-header green">
				<div class="cell"> Type </div>
				<div class="cell"> Number </div>
			</div>
			<?php 
			foreach ($bpTypes as $type=>$ntype) {
				print "<div class='row'>";
				print "<div class='cell'> $type </div>";
				print "<div class='cell'> $ntype </div>";
				print "</div>";
			}
			?>
		</div>
	</div>

	<div class="table header" style="margin-bottom: 5px";>
		<div class="row-header blue">
			<div class="cell"> Nucleotide Base-Pair Steps Analized</div>
			<div class="cell"> <i>(Click to expand)</i></div>
		</div>
	</div>

	<div class="statsInside" id="plot4">

		<div id="chartdiv4" class="statsChartsBPS">
		</div> 

		<div class="table">
			<div class="row-header green">
				<div class="cell"> Type </div>
				<div class="cell"> Number </div>
			</div>
			<?php 
			foreach ($bpsTypes as $type=>$ntype) {
				print "<div class='row'>";
				print "<div class='cell'> $type </div>";
				print "<div class='cell'> $ntype </div>";
				print "</div>";
			}
			?>
		</div>
	</div>

	<br/>
	<a name='Ontology'></a>
	<br/>
	<p style="font-size:1.2em;font-weight:bold;">Statistics by Ontology</p>
	<hr/>


	<div class="table header" style="margin-bottom: 5px";>
		<div class="row-header cyan">
			<div class="cell"> Ontology: NA Types</div>
			<div class="cell"> <i>(Click to expand)</i></div>
		</div>
	</div>

	<div class="statsInside" id="plot5">

		<div id="chartdiv5" class="statsCharts">
		</div> 

		<div class="table">
			<div class="row-header green">
				<div class="cell"> Type </div>
				<div class="cell"> Number </div>
			</div>
			<?php 
			foreach ($ontoNATypes as $type=>$ntype) {
				print "<div class='row'>";
				print "<div class='cell'> $type </div>";
				print "<div class='cell'> $ntype </div>";
				print "</div>";
			}
			?>
		</div>
	</div>

	<div class="table header" style="margin-bottom: 5px";>
		<div class="row-header cyan">
			<div class="cell"> Ontology: NA Structure Types</div>
			<div class="cell"> <i>(Click to expand)</i></div>
		</div>
	</div>

	<div class="statsInside" id="plot6">

		<div id="chartdiv6" class="statsCharts">
		</div> 

		<div class="table">
			<div class="row-header green">
				<div class="cell"> Type </div>
				<div class="cell"> Number </div>
			</div>
			<?php 
			foreach ($ontoStructureTypes as $type=>$ntype) {
				print "<div class='row'>";
				print "<div class='cell'> $type </div>";
				print "<div class='cell'> $ntype </div>";
				print "</div>";
			}
			?>
		</div>
	</div>

	<div class="table header" style="margin-bottom: 5px";>
		<div class="row-header cyan">
			<div class="cell"> Ontology: System Type</div>
			<div class="cell"> <i>(Click to expand)</i></div>
		</div>
	</div>

	<div class="statsInside" id="plot7">

		<div id="chartdiv7" class="statsCharts">
		</div> 

		<div class="table">
			<div class="row-header green">
				<div class="cell"> Type </div>
				<div class="cell"> Number </div>
			</div>
			<?php 
			foreach ($ontoSystemTypes as $type=>$ntype) {
				print "<div class='row'>";
				print "<div class='cell'> $type </div>";
				print "<div class='cell'> $ntype </div>";
				print "</div>";
			}
			?>
		</div>
	</div>

	<div class="table header" style="margin-bottom: 5px";>
		<div class="row-header cyan">
			<div class="cell"> Ontology: Trajectory Type</div>
			<div class="cell"> <i>(Click to expand)</i></div>
		</div>
	</div>

	<div class="statsInside" id="plot8">

		<div id="chartdiv8" class="statsCharts">
		</div> 

		<div class="table">
			<div class="row-header green">
				<div class="cell"> Type </div>
				<div class="cell"> Number </div>
			</div>
			<?php 
			foreach ($ontoTrajectoryTypes as $type=>$ntype) {
				print "<div class='row'>";
				print "<div class='cell'> $type </div>";
				print "<div class='cell'> $ntype </div>";
				print "</div>";
			}
			?>
		</div>
	</div>

	<div class="table header" style="margin-bottom: 5px";>
		<div class="row-header cyan">
			<div class="cell"> Ontology: Helical Conformation</div>
			<div class="cell"> <i>(Click to expand)</i></div>
		</div>
	</div>

	<div class="statsInside" id="plot9">

		<div id="chartdiv9" class="statsCharts">
		</div> 

		<div class="table">
			<div class="row-header green">
				<div class="cell"> Type </div>
				<div class="cell"> Number </div>
			</div>
			<?php 
			foreach ($ontoHelicalTypes as $type=>$ntype) {
				print "<div class='row'>";
				print "<div class='cell'> $type </div>";
				print "<div class='cell'> $ntype </div>";
				print "</div>";
			}
			?>
		</div>
	</div>

	<div class="table header" style="margin-bottom: 5px";>
		<div class="row-header cyan">
			<div class="cell"> Ontology: MD Force Field</div>
			<div class="cell"> <i>(Click to expand)</i></div>
		</div>
	</div>

	<div class="statsInside" id="plot10">

		<div id="chartdiv10" class="statsCharts">
		</div> 

		<div class="table">
			<div class="row-header green">
				<div class="cell"> Type </div>
				<div class="cell"> Number </div>
			</div>
			<?php 
			foreach ($ontoFFTypes as $type=>$ntype) {
				print "<div class='row'>";
				print "<div class='cell'> $type </div>";
				print "<div class='cell'> $ntype </div>";
				print "</div>";
			}
			?>
		</div>
	</div>

	<div class="table header" style="margin-bottom: 5px";>
		<div class="row-header cyan">
			<div class="cell"> Ontology: MD Length</div>
			<div class="cell"> <i>(Click to expand)</i></div>
		</div>
	</div>

	<div class="statsInside" id="plot11">

		<div id="chartdiv11" class="statsCharts">
		</div> 

		<div class="table">
			<div class="row-header green">
				<div class="cell"> Type </div>
				<div class="cell"> Number </div>
			</div>
			<?php 
			foreach ($ontoLenTypes as $type=>$ntype) {
				print "<div class='row'>";
				print "<div class='cell'> $type </div>";
				print "<div class='cell'> $ntype </div>";
				print "</div>";
			}
			?>
		</div>
	</div>

	<div class="table header" style="margin-bottom: 5px";>
		<div class="row-header cyan">
			<div class="cell"> Ontology: MD Charge</div>
			<div class="cell"> <i>(Click to expand)</i></div>
		</div>
	</div>

	<div class="statsInside" id="plot12">

		<div id="chartdiv12" class="statsCharts">
		</div> 

		<div class="table">
			<div class="row-header green">
				<div class="cell"> Type </div>
				<div class="cell"> Number </div>
			</div>
			<?php 
			foreach ($ontoChargeTypes as $type=>$ntype) {
				print "<div class='row'>";
				print "<div class='cell'> $type </div>";
				print "<div class='cell'> $ntype </div>";
				print "</div>";
			}
			?>
		</div>
	</div>

    </div>
</div>
</div>
<?php
print footerMMB();

?>

