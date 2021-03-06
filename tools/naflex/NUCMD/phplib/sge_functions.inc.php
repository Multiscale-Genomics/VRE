<?php

# execCURL
# Queueing CURL execution, returning SGE process identifier (pid).
function execCURL ($workDir,$cmd) {

   /* logger("Executing script: $cmd");
        $process = new ProcessSGE($workDir,$cmd);
        $pid = $process->getPid();

        if ($process->status()){
        logger("The process $cmd is currently running ($pid).");
    }else{
        logger("The process is not running.");
    }
    return $pid;*/
}

# stillRunning
# Pooling to find out if process $pid is still Running or it has already finished.
function stillRunning ($sessId,$pid) {

	$workDir = $GLOBALS['tmpDir']."/".$sessId;

       // logger("WaitResult: ".$pid);
        $pend = 1;

        if($pid !=0){

                $process = new ProcessSGE($workDir);
                $process->setPid($pid);

                if ($process->status()){
         //           logger("Async process currently running!! ($pid).");
                }
                else{
           //         logger("Async process already finished!!");
                    $pend = 0;
                }
        }
	return $pend;
}

# queueCURL
# Building Queue CSH file to enqueue CURL jobs.
function queueCURL ($sessId, $json_input, $url) {
	
		#$workdir = $GLOBALS['tmpDir']."/$sessId";
	#echo "queueCURL workdir: $workdir<br/>";
		#chdir($workdir);

		# Queue CSH script
		$out = "CURL.$sessId.queue.sh";
		$fout = fopen($out, "w");
	        fwrite($fout, "#!/bin/csh\n");
	        fwrite($fout, "# generated by BIGNASim metatrajectory generator\n");
	        #fwrite($fout, "#\$ -q www.q\n");
	        fwrite($fout, "#\$ -cwd\n");
        	fwrite($fout, "#\$ -N BIGNaSim_curl_call_$sessId\n");
   	        fwrite($fout, "#\$ -o CURL.$sessId.out\n");
        	fwrite($fout, "#\$ -e CURL.$sessId.err\n");

	        #fwrite($fout, "cd $workdir\n");

		# CURL Exe
	        fwrite($fout, "\n# Launching CURL...\n");
	        fwrite($fout, "\n# CURL is calling a REST WS that generates the metatrajectory.\n");
		fwrite($fout, "curl -i -H \"Content-Type: application/json\" -X GET -d '$json_input' $url\n");

		fclose($fout);

		return $out;
}


?>
