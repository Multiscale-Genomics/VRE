<?php
require "../phplib/genlibraries.php";
redirectOutside();

// jobs before update
$jobs_ori = getUserJobs($_SESSION['User']['_id']);

/*<pre style="font-size:0.7em;margin:10px 25px;"><?php echo json_encode($jobs_ori, JSON_PRETTY_PRINT);?></pre>*/

// updating jobs
updatePendingFiles($_SESSION['User']['_id']);

// jobs after update
$jobs_last = getUserJobs($_SESSION['User']['_id']);

/*<pre style="font-size:0.7em;margin:10px 25px;"><?php echo json_encode($jobs_last, JSON_PRETTY_PRINT);?></pre>*/

$diff = strcmp(json_encode($jobs_ori), json_encode($jobs_last));

if ($diff){
    echo '{ "hasChanged":1 }';
}else{
    echo '{ "hasChanged": 0 }';
}
