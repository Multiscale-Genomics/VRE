<?php

require '../phplib/genlibraries.php';

if($_GET['q'] != ''){

$pdbs = array();
$pdbs = $GLOBALS['pdbCol']->find(array('_id'=>array('$regex'=>new MongoRegex("/^".strtoupper($_GET['q'])."/"))),array('_id'=>1))->sort(array('_id'=>1));
//$pdbs = $GLOBALS['pdbCol']->find(array('_id'=>array('$regex'=>strtoupper($_GET['q']))),array('_id'=>1))->sort(array('_id'=>1));

$out = array();
foreach ($pdbs as $arr) {
	foreach ($arr as $k) array_push($out, $k);
}

$len = count($out);
$i = 0;
echo '[';
foreach ($out as $k){
	if($i < $len - 1) echo '"'.$k.'",';
	else echo '"'.$k.'"';
	$i ++;
}
echo ']';

}else{
	echo 'please provide an identifier';
}

?>
