<?php
# SGE data

require "ProcessSGE.php";


function execJob ($workDir,$shFile,$tool=Array()) {
    logger("Executing script: $shFile");

    #job_req from mongo collection tools
    $cpus    = (isset($tool['cpus'])? $tool['cpus']:1);
    $mem     = (isset($tool['max_mem'])? $tool['max_mem']:0);
    $queue   = (isset($tool['queue'])? $tool['queue']:$GLOBALS['queueTask']);
    $jobname = $_SESSION['User']['id']."#".basename($shFile);
    #$jobname = (preg_match('/\//',$shFile)?join("#",array_slice(explode("/",$shFile), -2, 2, true)):$shFile);
    logger("CPUs= $cpus MEM=$mem QUEUE=$queue");
    

    $process = new ProcessSGE($workDir,$shFile,$queue,$jobname,$cpu,$mem);

    $pid = $process->getPid();

    if ($process->status()){
        logger("The process $cmd is currently running ($pid).");
        return $pid;
    }else{
	$_SESSION['errorData']['Error'][]="Job submission failed.<br/>".$process->getFullCommand."<br/>".$process->getErr;
        logger("The process is not running. ".$process->getErr);
        return 0;
    }
}


# getAllRunningJobs
function getRunningJobs(){
	$jobs=Array();
	$command = QSTAT." -u www-data | awk '$1 ~ /[0-9]+/ {print $1\"\t\"$5\"\t\"$6 $7}'";
	exec($command,$queueJobs);
	if (!isset($queueJobs[0]))
		return $jobs;
	else{
		foreach ($queueJobs as $jobLine){
			list($pid,$state,$start)=explode("\t",$jobLine);
			$cmd = QSTAT. " -j $pid | grep job_name | cut -d: -f2 | tr -d \" \"";
			exec($cmd,$jobName);
			$jobs[$pid]=Array(
                    'name'=>$jobName[0],
                    'start'=>$start,
                    'state'=>jobStateDicc($state)
           );
		}
	}
    return $jobs;
}
function getRunningJobInfo($pid){
	$job=Array();
	if (! $pid)
		return $job;
	$cmd = QSTAT. " -j $pid | awk '$0~/:/ {print $0}'";
	exec($cmd,$jobInfo);
	if(count($jobInfo) == 0 )
		return $job;
	foreach ($jobInfo as $line){
		$fields =explode(":",$line);
		$k = trim(array_shift($fields));
		$v = trim(implode(":",$fields));
		$job[$k]=$v;
	}
	$cmd = QSTAT." -u www-data | grep $pid | awk '$1 ~ /[0-9]+/ {print $1\"\t\"$5\"\t\"$6 $7}'";
	exec($cmd,$jobState);
	if (!isset($jobState[0]) ){
		$job['state']="FINISHING";
	}else{
		list($pid,$state,$start) = explode("\t",$jobState[0]);
		$job['state']= jobStateDicc($state);
	}
	$job['pid']=$pid;
    return $job;
}


function getPidFromOutfile($outfile){
	$pid=0;
	$SGE_updated = getUserJobs($_SESSION['userId']);
	foreach($SGE_updated as $data){
			$outs = $data['out'];
			if (!is_array($data['out']))
				$outs = Array($data['out']);
			if (in_array($outfile,$outs))
				return $data['_id'];
	}
	return $pid;	
}
function delJobFromOutfiles($outfiles){
	if (!is_array($outfiles)){
		$outfiles=Array($outfiles);
	}
	if (count($outfiles) ==0)
		return 1;

	$SGE_updated = getUserJobs($_SESSION['userId']);

	foreach($outfiles as $outfile){
	  $pid = getPidFromOutfile($outfile);
	  if ($pid){
	    //get dependencies of the selected job
		$pids=Array($pid);
		$jobInfo =  getRunningJobInfo($pid);
        if (isset($jobInfo['jid_successor_list'])){
			foreach (explode(",",$jobInfo['jid_successor_list']) as $pidSucc ){
				$succInfo = getRunningJobInfo($pidSucc);
				if($succInfo)
					array_push($pids,$pidSucc);
			}
		}
	    //foreach job, cancel and delete associated files
		foreach($pids as $pid){
			//delete job
			$ok = delJob($pid);
			if (!$ok){
				$_SESSION['errorData']['SGE'][]= "Cannot delete ".basename($outfile)." task. $pid unsuccessfully exited 'deljob'.";
				continue;
			}
		    //delete job associated files
			$files=Array();
			$jobType = (isset($SGE_updated[$pid]['log'])?basename($SGE_updated[$pid]['log']):"");
			if (preg_match('/^PP_/',$jobType)){
				$files[] = $SGE_updated[$pid]['log'];
			}else{
				if (!is_array($SGE_updated[$pid]['out']))
					$files[] =$SGE_updated[$pid]['out'];
				else
					$files  = $SGE_updated[$pid]['out'];
				$files[] = $SGE_updated[$pid]['log'];
			}

			foreach ($files as $fn){
				$rfn = $GLOBALS['dataDir']."/$fn";
				$ofn = $GLOBALS['filesCol']->findOne(array('_id' => $fn));
				if (!empty($ofn)){
					$ok = deleteGSFileBNS($fn);
				    if (!$ok){
						$_SESSION['errorData']['SGE'][]= "Job ".basename($outfile)." deleted. But errors occured while cleaning temporal files.";
						continue;
					}
				}
				if (is_file($rfn)){
			        unlink ($rfn);
			        if (error_get_last())
			            $_SESSION['errorData']['SGE'][]= "Cannot unlink $rfn". error_get_last()["message"];
				}
			}
			//update pending jobs
			//unset($SGE_updated[$pid]);
			//delUserJob($_SESSION['userId'],$pid);
		}
	  }else{
		$_SESSION['errorData']['SGE'][]= "Cannot find job information for '".basename($outfile)."'.  &nbsp;<a href=\"workspace/workspace.php\">[ OK ]</a>";
	  }
	}
	return 1;
}

function delJob($pid){
	$cmd = QDEL." $pid";
	exec($cmd,$r);
	$res = join(" ",$r);
	if (preg_match('/has deleted/i',$res) || preg_match('/registered the job \d+ for deletion/',$res)){
		delUserJob($_SESSION['User']['id'],$pid);
		return true;

	}else{
		$_SESSION['errorData']['SGE'][] = "Cannot delete job $pid.<br>".join(". ",$r); 
		return false;
	}
	return true;
}


function jobStateDicc($state){
	$dicc = Array (
			'r'  => "RUNNING",
			't'  => "TRANSFERING",
			'qw' => "PENDING",
			'hqw'=> "HOLD",
			'dr' => "DELETING",
			'Eqw'=> "ERROR"
	);
	if ($dicc[$state])
		return $dicc[$state];
	else
		return $state;
}
?>
