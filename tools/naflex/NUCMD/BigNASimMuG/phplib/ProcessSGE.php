<?php
/* An easy way to keep in track of external processes.
 * Ever wanted to execute a process in php, but you still wanted to have somewhat controll of the process ? Well.. This is a way of doing it.
 * @compability: Linux only. (Windows does not work).
 * @author: Peec
 */
class ProcessSGE{
    private $pid;
    private $command;
    private $queue;
    private $workDir;

    public function __construct($workDir, $cl=false){
        if ($cl != false){
            $this->command = $cl;
	    $this->queue = "www.q";
	    $this->workDir = $workDir;
            $this->runCom();
        }
    }
    private function runCom(){

	//$command = "/var/www/MDWeb/scripts/qsub $workDir/DIMS_queue.sh";
	$workDir = $this->workDir;
	$command = QSUB." $workDir/".$this->command;
        $err = exec($command ,$op);

	# Your job 408247 ("blablabla.sh") has been submitted
	$output = preg_split('/ /', $op[0]);
	$pid = $output[2];

	logger("Job $output ($pid). $err");
        $this->pid = (int)$pid;

    }

    public function setPid($pid){
        $this->pid = $pid;
    }

    public function getPid(){
        return $this->pid;
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
#        $command = 'kill '.$this->pid;
        exec($command);
        if ($this->status() == false)return true;
        else return false;
    }
}
?>
