<?php

require "../../phplib/genlibraries.php";

redirectOutside();

$user = $_REQUEST["user"];

// Clean files older than 1 day
$files = glob($GLOBALS['dataDir']."/". $user . "/.tadkit/conf_*");
$now   = time();
foreach ($files as $file)
	if (is_file($file))
      		if ($now - filemtime($file) >= 60 * 60 * 24) // 2 days
		        unlink($file);

# TADkit url
$absURL = $GLOBALS['URL']."/visualizers/tadkit/tadkit/index.html";

$user_data = "user_data/";
$uid = uniqid();
$url = "#/project/dataset";
$url = $url . "?conf=". $user_data . urlencode($user) . "/.tadkit/conf_".$uid.".json";
$file = $GLOBALS['dataDir']."/". $user . "/.tadkit/conf_".$uid.".json";
//print $file;
$conf_file = fopen($file, "w");

$query_string = "";

$arr_datasets= $_REQUEST["fn"];
if(!is_array($arr_datasets)) {
	$arr_datasets = array($arr_datasets);
}
//$arr_datasets = split(',',$datasets);
$first = true;
$refGlobal;

$conf_json = array(
    "dataset" => "",
    "tracks" => array()
);
if(sizeof($arr_datasets)>0) {
  //$arr_datasets = split(',',$datasets);
  foreach ($arr_datasets as $id) {
	$label = $id;
	$fileData = getGSFile_fromId($id);
        //$fileData = $GLOBALS['filesMetaCol']->findOne(array('_id' => $id));
        //$fileData2 = $GLOBALS['filesCol']->findOne(array('_id' => $id));

        $filepath = $fileData['path'];
	$filename = basename($filepath);
        $type = $fileData['format'];
	$data_type = $fileData['data_type'];
        $ref = $fileData['refGenome'];
        $a_project = split("/",dirname($filepath));
        $project = array_pop($a_project);

	if ($first) {
		# here we could include a list of always available tracks. Need to build a kind of repository. Future improvement
		//$trackHead = file_get_contents("JBrowse-1.11.6/data/tracks/$ref/trackList_head.json", FILE_USE_INCLUDE_PATH);
		//$trackTail = file_get_contents("JBrowse-1.11.6/data/tracks/$ref/trackList_tail.json", FILE_USE_INCLUDE_PATH);
		# Common tracks (reference sequence, genes, GC, etc.)
		//fwrite($trackf, $trackHead);
		$refGlobal=$ref;
	}

//	$filename = exec("grep -P \"^$label\\t\" metadata.txt |cut -f2");
//	$type = exec("grep -P \"^$label\\t\" metadata.txt |cut -f3");
//	$project = exec("grep -P \"^$label\\t\" metadata.txt |cut -f4");
//	$ref = exec("grep -P \"^$label\\t\" metadata.txt |cut -f5");
// print "LABEL: " . $label . " " . "FILENAME: " . $filename . " TYPE: " . $type . " PROJECT: " . $project . " REF: " . $ref . "<br/>";

	if ($refGlobal != $ref){
		$_SESSION['errorData']['tadkit'][] ="All selected tracks should have the same Reference Genome $refGlobal";
                redirect("/visualizers/error.php"); 

	}
	if ($data_type == 'chromatin_3dmodel_ensemble' || $data_type == 'tadbit_models') {
	//if(strpos($filename, '.json') !== false)  { #Until we fix data type
		$conf_json["dataset"] = $user_data.$filepath;
	} else {
		if ($type == "FALSE"){
			$_SESSION['errorData']['tadkit'][] ="$filename cannot be visualized, file has no track type";
			redirect("/visualizers/error.php"); 
		}
	

		if ($type == "BAM") {
 	               	$type = "bam";
 			$igv_type = "alignment";
		} elseif ($type == "BED"){
        	        $type = "bed";
                	$igv_type = "annotation";
		} elseif ($type == "BEDGRAPH"){
                        $type = "bedGraph";
                        $igv_type = "wig";
		} elseif ($type == "BW_cov"){
        	        $type = "bigwig";
                	$igv_type = "wig";
		} elseif ($type == "GFF_NR"){
        	        $type = "gff";
                	$igv_type = "annotation";
		} elseif ($type == "GFF_ND"){
        	        $type = "gff";
                	$igv_type = "annotation";
	        } elseif ($type == "GFF_TX"){
        	        $type = "gff";
                	$igv_type = "annotation";
	        } elseif ($type == "GFF_NFR"){
        	        $type = "gff";
                	$igv_type = "annotation";
	        } elseif ($type == "GFF_GAU"){
        	        $type = "gff";
                	$igv_type = "annotation";
	        } elseif ($type == "GFF_P"){
        	        $type = "gff";
                	$igv_type = "annotation";
	        } elseif ($type == "BW_P"){
        	        $type = "bigwig";
                	$igv_type = "wig";
	        } elseif ($type == "GFF"){
        	        $type = "gff";
                	$igv_type = "annotation";
	        } elseif ($type == "BW"){
        	        $type = "bigwig";
                	$igv_type = "wig";
		} else {
	//		print $type;
	//		print "unknown trackType<br>";
        	        $_SESSION['errorData']['tadkit'][] ="$filename cannot be visualized, file has unknown track type '$type' ";
			redirect("/visualizers/error.php"); 
		}

	
		$atrack = array(
		  "name" => $filename,
		  "type" => $igv_type,
		  "format" => $type,
		  "sourceType" => "file",
		  "url" => $user_data.$filepath,
		  "indexed" => false
		);
		array_push($conf_json["tracks"],$atrack);
	}
  }
}

if ($conf_json["dataset"] == ""){
	$_SESSION['errorData']['tadkit'][] ="No TADbit models file selected";
        redirect("/visualizers/error.php");
}
if ($conf_json != null) {
  $json = json_encode($conf_json);
  $json = str_replace("\/","/",$json);
  //echo $json;
  fwrite($conf_file, $json);
}

# If we got here, trackType's and refGenome's are OK

fclose($conf_file);

//$url = $url . $url_tracks;
//$url = $GLOBALS['absURL'] . $url;
$url = $absURL . $url;

//print $url;

header('Location: ' . $url);
exit();


?>
