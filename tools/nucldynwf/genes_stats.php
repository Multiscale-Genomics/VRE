<?php

require "../../phplib/genlibraries.php";
redirectOutside();

//header("Access-Control-Allow-Origin: *");

$folder = $GLOBALS['dataDir'].$_SESSION['User']['id']."/".$_SESSION['User']['activeProject']."/.tmp/outputs_".$_REQUEST["tmpf"];
//$folder = $GLOBALS['dataDir'].$_SESSION['User']['id']."/.tmp/outputs_MuGUSER5a394e70646c9_5b44d52d5eecf0.42430817";
chdir($folder);

$nrFiles = glob("[!ND]*_genes_stats.csv");
$ndFiles = glob("ND*_genes_stats.csv");
$columnsNR = [];
$columnsND = [];
$genes = [];
$nrgenes = [];
$ndgenes = [];
$numberOfFiles = sizeof($nrFiles) + sizeof($ndFiles);

// WE HAVE NR FILES
if(!empty($nrFiles)) {

// open csv file (just the 1st one)
/*$fh = fopen($nrFiles[0], "r");
$c = 0;
// get columns name for NR and array of genes
while (($line = fgetcsv($fh)) !== false) {
	if($c == 0) $columnsNR = $line;
	else {
		$genes[$line[0]] = [];
	}
	//var_dump($line);
	$c ++;
}
fclose($fh);
unset($fh);*/

$gns = [];

foreach($nrFiles as $nrf) {	
	$fh = fopen($nrf, "r");
	$c = 0;
	// get columns name for NR and array of genes
	while (($line = fgetcsv($fh)) !== false) {
		if($c == 0) $columnsNR = $line;
		else {
			if(!in_array($line[0], $gns)) $genes[$line[0]] = [];
		}
		$gns[] = $line[0];
		//var_dump($line);
		$c ++;
	}
	fclose($fh);
	unset($fh);
}


// foreach all the NR files
foreach($nrFiles as $file) {

	// get file name (without suffix)
	$fileName = str_replace("_genes_stats.csv", "", $file);

	// open csv file
	$fh = fopen($file, "r");
	$c = 0;
	$cols = [];
	// foreach line of csv
	while (($line = fgetcsv($fh)) !== false) {
		// get the columns name (first line)
		if($c == 0) $cols = $line;
		else {
			// get the rest of data for every gene > file > csv column
			$cl = 0;
			foreach($line as $l) {
				if($cl > 0) $nrgenes[$line[0]][$fileName][$cols[$cl]] = $l;
				$cl ++;
			}
		}
		
		$c ++;
	}
	fclose($fh);
	unset($fh);
}

$emptynd = [];
// foreach all the ND files
foreach($ndFiles as $file) {

	// get file name (without suffix)
	$fileName = str_replace("_genes_stats.csv", "", $file);

	// open csv file
	$fh = fopen($file, "r");
	$c = 0;
	$cols = [];
	// foreach line of csv
	while (($line = fgetcsv($fh)) !== false) {
		// get the columns name (first line)
		if($c == 0) $cols = $line;
		else {
			// get the rest of data for every gene > file > csv column
			$cl = 0;
			foreach($line as $l) {
				if($cl > 0) $ndgenes[$line[0]][$fileName][$cols[$cl]] = $l;
				$cl ++;
			}
		}

		$c ++;
	}
	fclose($fh);
	unset($fh);

	$cl = 0;
	foreach($cols as $c) {
		if($cl > 0) $emptynd[$fileName][$cols[$cl]] = "NA";
		$cl ++;
	}

}

// foreach NR (has all the genes)
foreach($nrgenes as $key => $gene) {

	// if gene is in ND, add ND data (TODO: more than one ND??)
	if (array_key_exists($key, $ndgenes)) {

		$genes[$key] = $gene;
		$genes[$key] = array_merge($genes[$key], $ndgenes[$key]);

	// if gene is not in ND, add empty data
	} else {

		$genes[$key] = $gene;
		$genes[$key] = array_merge($genes[$key], $emptynd);

	}

} 

// at this point we have all the information in $genes array, time to convert it into html table
//var_dump($genes);
$level1 = [];
$level2 = [];
$filters = [];
$filterTags = ["TSS class"];

// we use a structure the first gene with all the files
foreach($genes as $g) {
	if(sizeof($g) == $numberOfFiles) $structure = $g;
}

// creating structure arrays (table header)
// creating filters
// TODO: col id and col name (toggle columns buttons)
$f = 1;
foreach($structure as $k1 => $v1) {

	$c = 0;	
	foreach($v1 as $k2 => $v2) {
		if(in_array($k2, $filterTags)) {
			$filters["$k2 for $k1"] = $f;
		}
		$level2[] = $k2;
		$c ++;
		$f ++;
	}

	$level1[$k1] = $c;
}

} elseif(!empty($ndFiles)) {
// JUST ND FILES

// foreach all the ND files
foreach($ndFiles as $file) {

	// get file name (without suffix)
	$fileName = str_replace("_genes_stats.csv", "", $file);

	// open csv file
	$fh = fopen($file, "r");
	$c = 0;
	$cols = [];
	// foreach line of csv
	while (($line = fgetcsv($fh)) !== false) {
		// get the columns name (first line)
		if($c == 0) $cols = $line;
		else {
			// get the rest of data for every gene > file > csv column
			$cl = 0;
			foreach($line as $l) {
				if($cl > 0) $ndgenes[$line[0]][$fileName][$cols[$cl]] = $l;
				$cl ++;
			}
		}

		$c ++;
	}
	fclose($fh);
	unset($fh);

}

// foreach ND (has all the genes)
foreach($ndgenes as $key => $gene) {

	$genes[$key] = $gene;

}

// at this point we have all the information in $genes array, time to convert it into html table
//var_dump($genes);

$structure = reset($genes);
$level1 = [];
$level2 = [];
$filters = [];
//$filterTags = ["TSS class"];
// creating structure arrays (table header)
// creating filters
// TODO: col id and col name (toggle columns buttons)
$f = 1;
foreach($structure as $k1 => $v1) {

	$c = 0;	
	foreach($v1 as $k2 => $v2) {
		/*if(in_array($k2, $filterTags)) {
			$filters["$k2 for $k1"] = $f;
	}*/
		$level2[] = $k2;
		$c ++;
		$f ++;
	}

	$level1[$k1] = $c;
}

} else { // END IF WE HAVE NR FILES

	echo '{
	"htmlTable": false
	}';
	die();

}

//var_dump(sizeof($level2));

$htmlTable = "<table id='nd-table' class='display' cellspacing='0' width='100%'>";
$htmlTable .= "<thead>";

$htmlTable .= "<tr>";
$htmlTable .= "<th rowspan='2'>Gene</th>";
foreach($level1 as $k => $v) { 
	$htmlTable .= "<th colspan='$v' class='header-group'>$k</th>";
} 
$htmlTable .= "</tr>";
$htmlTable .= "<tr>";
foreach($level2 as $v) {
	$htmlTable .= "<th>$v</th>";
}
$htmlTable .= "</tr>";
$htmlTable .= "</thead>";
$htmlTable .= "<tbody>";
foreach($genes as $gkey => $ginfo) {
	$htmlTable .= "<tr>";
	$htmlTable .= "<td>$gkey</td>";
	$lcount = 0;
	foreach($ginfo as $ginf) {
		foreach($ginf as $g) {
			$floatVal = floatval($g);
			if($floatVal && intval($floatVal) != $floatVal) $htmlTable .= "<td>".number_format($g, 3, '.', '')."</td>";
			else $htmlTable .= "<td>$g</td>";

			$lcount ++;
		}
	}
	if($lcount < sizeof($level2)) {
		for($i = 0; $i < (sizeof($level2) - $lcount); $i ++) $htmlTable .= "<td></td>";
	}
	$htmlTable .= "</tr>";
	/****************************/
	//$limit ++;
	//if($limit == 100) break;
	/****************************/
}
$htmlTable .= "</tbody>";
$htmlTable .= "</table>";

?>

{
	"htmlTable": "<?php echo $htmlTable; ?>",
	"filters": {<?php echo substr(json_encode($filters), 1, -1); ?>},
	"structure": {<?php echo substr(json_encode($structure), 1, -1); ?>}
}
