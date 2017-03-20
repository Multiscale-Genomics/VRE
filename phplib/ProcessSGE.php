<?php
/* An easy way to keep in track of external processes.
 * Ever wanted to execute a process in php, but you still wanted to have somewhat controll of the process ? Well.. This is a way of doing it.
 * @compability: Linux only. (Windows does not work).
 * @author: Peec
 */
class ProcessSGE{
	private $pid;
	private $command;
	private $workDir;
	private $queue="srv.q";
	private $cpu=1;
	private $mem=0; 

	public function __construct($workDir, $cl=false,$queue="srv.q",$jobname="",$cpu=1,$mem=0){
		if ($cl != false){
			$this->workDir = $workDir;
			$this->command = $cl;
			$this->queue   = $queue;
			$this->cpu     = $cpu;
			$this->mem     = $mem; 
	
			if ($jobname)
				$this->jobname = $jobname;
			else
				$this->jobname = basename($cl);

			$this->runCom();
		}
	}
	private function runCom(){
		$this->setFullCommand();
		$command = $this->fullcommand;
		logger("Command: $command");
				
		$proc = proc_open($command,[
						 1 => ['pipe','w'],
						 2 => ['pipe','w'],
						],$pipes, $this->workDir);
		$this->stdout = stream_get_contents($pipes[1]);
		fclose($pipes[1]);
		$this->stderr = stream_get_contents($pipes[2]);
		fclose($pipes[2]);
		proc_close($proc);
 
		if (preg_match('/job (\d+)/',$this->stdout,$m)){
				$this->pid=(int)$m[1];
				logger("Job returns: ".$this->stdout);
		}else{
				$this->pid=0;
				logger("Job returns: ".$this->stdout." Error: ". $this->stderr);
				$_SESSION['errorData']['Error'][] = $this->stdout." Error: ". $this->stderr;
		}
		
		//	  exec($command." 2>&1",$op);
		//	  $output = preg_split('/ /', $op[0]);
		//	  $pid = $output[2];
		//  $this->pid = (int)$pid;
	}

	public function setFullCommand(){
        $workDir = $this->workDir;
        $command = QSUB." -N '".$this->jobname."' -wd $workDir -q ".$this->queue;
        if ($this->cpu > 1)
            $command .= " -l cpu=". $this->cpu;
        $command .= " ".$this->command;
        $this->fullcommand = $command;
	}

    public function getFullCommand(){
        return $this->fullcommand;
    }

	public function setPid($pid){
		$this->pid = $pid;
	}

	public function getPid(){
		return $this->pid;
	}

    public function getErr(){
        if ($this->stderr)
            return $this->stderr;
        else
            return NULL;
    }
	public function status(){
		# No need to specify a queue, pids are unique in the same SGE system.
		$pidForm = sprintf("%7s",$this->pid);
		//$command = '/var/www/MDWeb/scripts/qstat -u www-data | grep "^'.$pidForm.'"';
		$command = QSTAT.' -u www-data | grep "^'.$pidForm.'"';
		exec($command,$op);

		if (!isset($op[0]))return false;
		else return true;
	}

	public function start(){
		if ($this->command != '')$this->runCom();
		else return true;
	}

	public function stop(){
	//	$command = '/var/www/MDWeb/scripts/qdel '.$this->pid;
		$command = QDEL.' '.$this->pid;
#		$command = 'kill '.$this->pid;
		exec($command);
		if ($this->status() == false)return true;
		else return false;
	}
}
?>
