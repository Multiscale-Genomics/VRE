<<?php
require "../phplib/genlibraries.php";
redirectOutside();

$job = getUserJobPid($_SESSION['User']['_id'],$_REQUEST["pid"]);
$mt = $job[$_REQUEST["pid"]];

if(file_exists($mt['log_file'])) {
	echo nl2br(file_get_contents($mt['log_file']));
} else {
	echo "Log file not created yet, please wait.";
}
