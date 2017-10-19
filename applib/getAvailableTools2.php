<?php

require "../phplib/genlibraries.php";

redirectOutside();

//http://multiscalegenomics.bsc.es/applib/getAvailableTools.php?fn[]=MuGUSER599c0cdc04d05_59e4a642b90dd8.49844403&fn[]=MuGUSER599c0cdc04d05_59e4a6428db561.89121328&fn[]=MuGUSER599c0cdc04d05_59e4a64310bf42.33452996&fn[]=MuGUSER599c0cdc04d05_59e4a725521733.07181964

/*if(!$_POST){
	//redirect($GLOBALS['URL']);
	echo "Network error, please reload the Workspace";
}*/

//$numfiles = sizeof($_REQUEST["fn"]);
//var_dump($numfiles);

$fdt = getFiles_DataTypes($_REQUEST["fn"]);
//var_dump($fdt);

//$minFiles = getMinDT_Files($fdt);
//var_dump($minFiles);


$dt = getTools_DataTypes();
//var_dump($dt);


$toolsList = getTools_ByDT($dt, $fdt);
//var_dump($toolsList);


$tools = getTools_ListByID($toolsList);

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
