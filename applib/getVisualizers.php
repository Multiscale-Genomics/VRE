<?php

require "../phplib/genlibraries.php";

redirectOutside();

if(! $_POST){
	redirect($GLOBALS['URL']);
}

// TODO: get list of files and match with all the visualizers (casi ná :)

$visualizers = getVisualizers_List();

sort($visualizers);

foreach($visualizers as $v) { 
	
	echo '<li>';
	echo '<a href="javascript:runVisualizer(\''.$v['_id'].'\', \''.$_SESSION['User']['id'].'\');" class="'.$v['_id'].'">';
	include '../visualizers/'.$v['_id'].'/assets/ws/icon.php';
	echo ' View in '.$v['name'];
	echo '</a>';
	echo '</li>';

}

?>
