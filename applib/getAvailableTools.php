<?php

require "../phplib/genlibraries.php";

redirectOutside();

if(!$_POST){
	//redirect($GLOBALS['URL']);
	echo "Network error, please reload the Workspace";
}

// TODO: get list of files and match with all the tools (casi ná :)

$tools = getTools_List();

sort($tools);

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


?>
