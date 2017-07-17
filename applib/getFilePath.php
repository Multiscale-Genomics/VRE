<?php

require "../phplib/genlibraries.php";

redirectOutside();

if(! $_POST){
	redirect($GLOBALS['URL']);
}

$filePath = getAttr_fromGSFileId($_REQUEST['id'],'path');
$dataType = getAttr_fromGSFileId($_REQUEST['id'],'data_type');


//echo $filePath;

echo '{ "path":"'.$filePath.'", "data_type":"'.$dataType.'" }';


?>
