<?php

require "../../phplib/genlibraries.php";

redirectOutside();


$user = $_SESSION['User']['id'];
$proj = $_SESSION['User']['activeProject'];

// Fiñes selected
$fileIds = $_REQUEST["fn"];


// Check file types and formats for received files

$file_features = 0;
$file_matrix = 0;

foreach ($fileIds as $id) {

	$fileData = getGSFile_fromId($id);
    $filepath  = $fileData['path'];
	$data_type = $fileData['data_type']; //  prommoter C-HiC_*
	$format    = $fileData['format']; // TSV
    $assembly  = $fileData['refGenome'];

	if (!$assembly){
        $_SESSION['errorData']['Cytoscape'][] =basename($filepath)." cannot be visualized, unknown reference genome '$assembly' ";
        redirect("/visualizers/error.php");
    }

	if ($format != "TSV"){
        $_SESSION['errorData']['Cytoscape'][] =basename($filepath). " cannot be visualized. File is not TSV format.";
        redirect("/visualizers/error.php");
    }

	if ($data_type != "pchic_matrix" && $data_type != "pchic_scores"){
        $_SESSION['errorData']['Cytoscape'][] =basename($filepath) ." cannot be visualized. File should be 'Promoter C-HiC scores' or 'Promoter C-HiC matrix'.";
        redirect("/visualizers/error.php");
    }

    if ($data_type == "pchic_matrix"){
        $file_matrix = $fileData;
    }elseif ($data_type == "pchic_scores"){
        $file_features = $fileData;
    }
}

// Check we receive a features file and a matrix file

if (!$file_features || !$file_matrix){
    $_SESSION['errorData']['Cytoscape'][] =" You need a 'Promoter C-HiC scores' file and a 'Promoter C-HiC matrix' file to use Cytoscape visualizer";
    redirect("/visualizers/error.php");
}


// ????

$url_features   = $GLOBALS['URL']."/files/". $file_features['path'];
$url_matrix     = $GLOBALS['URL']."/files/". $file_matrix['path'];
$url_cytoscape  = $GLOBALS['URL'] ."/visualizers/cytoscape/BLABLA";

print "URL features -> $url_features <br/>";
print "URL matrix -> $url_matrix <br/>";
print "URL visualizers -> $url_cytoscape <br/>";

#redirect($url);


?>


