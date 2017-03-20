<?php

# getAllRunningJobs
function getRunningJobs($fileId){
	$jobs=Array();
	$command = QSTAT." -u www-data | awk '$1 ~ /[0-9]+/ {print $1\"\t\"$5\"\t\"$6 $7}'";
        exec($command,$queueJobs);
	if (!isset($queueJobs[0]))
		return $jobs;

        foreach ($queueJobs as $jobLine){
		list($pid,$state,$start)=explode("\t",$jobLine);
		$cmd = QSTAT. " -j $pid | grep job_name | cut -d: -f2 | tr -d \" \"";
		exec($cmd,$jobName);
		if (!isset($jobName[0]) || !strpos($jobName[0],$fileId) ){
			continue;
		}
		$jobs[$pid]=Array(
			'name'=>$jobName[0],
			'start'=>$start,
			'state'=>$state
		);
	}
        return $jobs;
}
?>
