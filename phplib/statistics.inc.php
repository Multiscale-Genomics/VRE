<?php
function get_statistics_tool($toolId,$user="all"){
	$statistics = array("Jobs launched" => 0,
			    "Submision Errors" => 0,
			    "Register Errors" => 0
			   );
	if ($user=="all"){
		$logFile =  $GLOBALS['logFile'];
		$cmd = "grep 'TOOL:$toolId' $logFile";
		exec($cmd, $matchs);
		if (count($matchs))
			$statistics['Jobs launched']=count($matchs);
		$cmd = "grep 'TOOL:$toolId*PID:0' $logFile";
		exec($cmd, $matchs);
		if (count($matchs))
			$statistics['Submision Errors']=count($matchs);
		//if (preg_match('/PID:(.*)$/',$matchs,$m) ){
		
	}
	return $statistics;
}
?>
