<?php

require "../phplib/genlibraries.php";

redirectOutside();

if(!$_POST){
	//redirect($GLOBALS['URL']);
	echo "Network error, please reload the Workspace";
}


$fdt = getFiles_DataTypes($_REQUEST["fn"]);

$dt = getTools_DataTypes();

$toolsList = getTools_ByDT($dt, $fdt);

$tools = getTools_ListByID($toolsList);

sort($tools);

if(!empty($tools)) {

foreach($tools as $t) { 

	echo '<li>';
	echo '<a href="javascript:runTool(\''.$t['_id'].'\');" class="'.$t['_id'].'">';
	if (is_file('../tools/'.$t['_id'].'/assets/ws/icon.php'))
		include '../tools/'.$t['_id'].'/assets/ws/icon.php';
	else
		include '../tools/tool_skeleton/assets/ws/icon.php';
	echo ' '.$t['name'];
	echo '</a>';
	echo '</li>';

}

}else{

	echo '<li>';
	echo '<a href="javascript:;" style="mouse:default;"><i class="fa fa-exclamation-triangle"></i> No tools available for this combination of files</a>';
	echo '</li>';

}


