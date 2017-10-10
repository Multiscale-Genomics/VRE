<?php
# SGE data

require "ProcessSGE.php";
require "ProcessPMES.php";

function execJob ($workDir,$shFile,$queue,$cpus=1,$mem=0) {
    logger("Start job submission via SGE");

    $queue   = (isset($queue)? $queue:$GLOBALS['queueTask']);
    $jobname = $_SESSION['User']['id']."#".basename($shFile);

    // Start SGE process
    $process = new ProcessSGE($shFile,$workDir,$queue,$jobname,$cpus,$mem);

    $pid = $process->getPid();

    if (!$process->status()){
        $_SESSION['errorData']['Error'][]="Job submission failed.<br/>".$process->getFullCommand."<br/>".$process->getErr();
        logger("ERROR: SGE job is not running. MORE_INFO = '".$process->getErr(). "'");
        return 0;
    }
    
    logger("The process $cmd is currently running PID = $pid");
    return $pid;
}

function execJobPMES ($cloudName,$data){
    logger("Start job submission via PMES");

    // Start PMES process
    $process = new ProcessPMES($cloudName);

    if (!$process->listening){
	$_SESSION['errorData']['Error'][]="Job submission failed.<br/>PMES call to '".$process->getServer()."' returned: ".$process->getErr();
    	logger("ERROR: PMES job is not running. Server not listening. TEST_RESPONSE = '".json_encode($process->lastCall)."'");
	return 0;
    }

    $process->runPMES($data);
    $jobid =  $process->getJobId();

    print "<br>POST DONE. <br>JOBID = $jobid .<br>PMES PROCESS IS:<br>";
    var_dump($process);

    if ($jobid == "0"){
	$_SESSION['errorData']['Error'][]="Job submission failed.<br/>".$process->lastCall."<br/>".$process->getErr();
        logger("ERROR: PMES job is not running. MORE_INFO = '".$process->getErr(). "'");
        return 0;
    }

    logger("The process is currently running JOB_ID = $jobid");
    return $jobid;
}


# getAllRunningJobs
/*
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
*/

function getRunningJobInfo($pid,$launcherType=NULL){

	$job=Array();
	if (! $pid)
        return $job;

    // guess launcher
	if(!$launcherType){
		if (is_numeric($pid))
			$launcherType = "SGE";
		else
			$launcherType = "PMES";
    }

    // create new jobProcess
	if ($launcherType == "SGE"){
		$process = new ProcessSGE();
		$job = $process->getRunningJobInfo($pid);

	}elseif($launcherType == "PMES"){
		$process = new ProcessPMES();
		$job = $process->getRunningJobInfo($pid);
	}else{
		$_SESSION['errorData']['Error'][]="Cannot monitor job '$pid' of type '$launcher'. Launcher not implemented.";
		return $job;
    }
    // return job info
	return $job;
}

function updateLogFromJobInfo($logFile,$pid,$launcherType=NULL){

    // guess launcher
	if(!$launcherType){
		if (is_numeric($pid))
			$launcherType = "SGE";
		else
			$launcherType = "PMES";
    }
    // if PMES, update log content
    if ($launcherType == "PMES"){
		$process = new ProcessPMES();
        $job = $process->getActivityInfo($pid);

        if ($job['jobOutputMessage'] || $job['jobErrorMessage'] ){
            $F = fopen($logFile, "w");
            if ($job['jobOutputMessage']){
                fwrite($F, "##### STDOUT ###############################\n");
                fwrite($F, $job['jobOutputMessage']);
            }
            if ($job['jobErrorMessage']){
                fwrite($F, "##### STDERR ###############################\n");
                fwrite($F, $job['jobErrorMessage']);
            }
            fclose($F);
        }
    }
    return true;
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

// cancel job given its output file
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
				$_SESSION['errorData']['Error'][]= "Cannot delete ".basename($outfile)." task. Unsuccessfully exit of 'deljob' for job $pid.";
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

function delJob($pid,$launcherType=NULL){

    $job=Array();
    if (! $pid)
        return false;

    // guess launcher
    if(!$launcherType){
        if (is_numeric($pid))
            $launcherType = "SGE";
        else
            $launcherType = "PMES";
    }

    // cancel job
    $r = false;
    if ($launcherType == "SGE"){
        $process = new ProcessSGE();
        list($r,$msg) = $process->stop($pid);
        if (!$r)
           $_SESSION['errorData']['Error'][]="Cannot delete $launcherType job [id = $pid].<br/>$msg";

    }elseif($launcherType == "PMES"){
        $process = new ProcessPMES();
        $r= $process->stop($pid);
        if (!$r)
           $_SESSION['errorData']['Error'][]="Cannot delete $launcherType job [id = $pid].<br/>$msg";
    }else{
        $_SESSION['errorData']['Error'][]="Cannot delete job of type '$launcherType' [id = $pid]. Launcher not implemented.";
        return false;
    }

    // delete job from user
    if ($r){
        delUserJob($_SESSION['User']['id'],$pid);
    }else{
    	$_SESSION['errorData']['Internal Error'][] = "Error while cancelling $launcherType job [id = $pid].<br>Job deleted from the system, but not from user metadata"; 
    	return false;
    }
    return true;
}

/*
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
*/
?>
