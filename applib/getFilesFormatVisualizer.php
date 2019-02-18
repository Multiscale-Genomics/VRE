<?php 

require "../phplib/genlibraries.php";
redirectOutside();

$vis = $GLOBALS['visualizersCol']->findOne(array("external" => true, "_id" => $_REQUEST["id"]), array("accepted_file_types" => true));

echo json_encode($vis);
