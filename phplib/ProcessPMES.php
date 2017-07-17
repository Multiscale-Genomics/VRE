<?php
class ProcessPMES{

	public $jobid       = 0;
	public $listening = false;
	public $lastCall;

	private $server;
	private $cloud;
	private $APIroot = "pmes/";


	public function __construct($cloudName="local",$data=array(),$service=false){

		//get infrastructure info
		if ($cloudName == "local"){
			$this->cloud = getCurrentCloud();
			if ($this->cloud == "0")
				$cloudName="mug-bsc";
		}elseif(!in_array($cloudName,array_keys($GLOBALS['clouds'])) ){
			$_SESSION['errorData']['Warning'][]="No MuG cloud named '$cloudName' is registered. Instead, attempting 'mug-bsc' infrastructure";
			$cloudName="mug-bsc";
		}else{
			$this->cloud = $GLOBALS['clouds'][$cloudName];
		}
		//PMES url server
		$this->server = "http://".$this->cloud['PMESserver_domain'].":".$this->cloud['PMESserver_port']."/".$this->cloud['PMESserver_address'];

		//test server access
		$test = get_headers($this->server.$this->APIroot,1);
		if (preg_match('/404/', $test[0])){
			$this->stderr = $test[0];
			return 0;
		}
		$this->listening   = true;
		$this->lastCall = $test;
		$this->lastCall['url'] = $this->server.$this->APIroot;

		//if data, post it
		if ($service === true && $data){
			$r = $this->post($data,$service);
			return $r;
		}
		return 1;
    }

    /*
	private function post($data,$service){
		$url = $this->server.$this->APIroot.$service;
		logger("PMES POST call. URL = '$url'");

		$data_string = json_encode($data);
	
		print "<br>POST DATA IS <br>";
		print "<pre>".json_encode($data, JSON_PRETTY_PRINT)."</pre>";
		
	
		if (!strlen($data_string)){
		    $_SESSION['errorData']['Error'][]="Curl: cannot POST request. Data to send is empty";
		    return 0;
		}
		logger("PMES POST call. POST_DATA = '".json_encode($data). "'");

		$c = curl_init();
		curl_setopt($c, CURLOPT_URL, $url);
		curl_setopt($c, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($c, CURLOPT_POST, 1);
		curl_setopt($c, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($c, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c, CURLOPT_HTTPHEADER, array(
		                    'Content-Type: application/json',
		                    'Content-Length: '. strlen($data_string)
		                    )
		            );
		//curl_setopt($c, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		//curl_setopt($c, CURLOPT_USERPWD, "username:password");

		$r = curl_exec ($c);
		$this->lastCall = curl_getinfo($c);

		if ($r === false){
			$errno = curl_errno($c);
			$msg = curl_strerror($errno);
		    	$err = "Error calling PMES. Uncomplete POST petition. Additional info: [$errno] $msg";
		    	$_SESSION['errorData']['Error'][]=$err;	
		        logger("ERROR:" .$err);
                return 0;
		}
		curl_close($c);

		print "<br>AFTER CURL EXEC RETURNS<br>";
		var_dump($r);

		if ($this->lastCall['http_code'] != 200){
			$err = "Error calling PMES. POST petition resulted unsuccessful. HTTP code: ".$this->lastCall['http_code'];
			$_SESSION['errorData']['Error'][]=$err;
			logger("ERROR:" .$err);
			logger("ERROR: calling PMES. POST_INFO = '".json_encode($this->lastCall). "'");
			logger("ERROR: calling PMES. POST_RESPONSE = '".strip_tags($r). "'");
			return 0;
		}
        return $r;
    }*/

    private function post($data,$service){

		$url = $this->server.$this->APIroot.$service;
		logger("PMES POST call. URL = '$url'");

		$data_string = json_encode($data);
	
		print "<br>POST DATA IS <br>";
		print "<pre>".json_encode($data, JSON_PRETTY_PRINT)."</pre>";
		
	
		if (!strlen($data_string)){
		    $_SESSION['errorData']['Error'][]="Curl: cannot POST request. Data to send is empty";
		    return 0;
		}
		logger("PMES POST call. POST_DATA = '".json_encode($data). "'");
        
        $headers = array(
            'Content-Type: application/json',
            'Content-Length: '. strlen($data_string)
        );

        list($r,$info) = post($data_string,$url,$headers);

        if ($r == "0"){
            if ($_SESSION['errorData']['Error']){
    			$err = array_pop($_SESSION['errorData']['Error']);
                logger("ERROR:" .$err);
            }
            if ($info['http_code'] != 200){
                logger("ERROR: Unexpected http code. HTTP code: ".$info['http_code']);
                logger("ERROR: calling PMES. POST_RESPONSE = '".strip_tags($r). "'");
            }
            return 0;
        }

		print "<br>AFTER CURL EXEC RETURNS<br>";
		var_dump($r);

		return $r;

    }

	public function runPMES($data){
		$service= "createActivity";
		$r = $this->post($data,$service);

		if ($r != "0"){
			$jobids      = json_decode($r);
			$this->jobid = $jobids[0];
		}

		return $this->jobid;
	}

	public function getRunningJobInfo($jobid){
		$job=array();
		$service = "getActivityReport";
		$r = $this->post(array($jobid),$service);

		if ($r != "0"){
			$jobPMES = flattenArray(array_shift(json_decode($r,TRUE)));
			if ($jobPMES['jobStatus'] && ($jobPMES['jobStatus']=="FINISHED" || $jobPMES['jobStatus']=="ERROR") )
				return $job;
				
			$job = $jobPMES;
			$job['state']          = ($jobPMES['jobStatus']?$jobPMES['jobStatus']:"UNKNOWN");
			$job['submission_time']= "";
		}
		return $job;
	}

        public function getSystemStatus(){
                $service= "getSystemStatus";
		$url = $this->server.$this->APIroot.$service;

		$c = curl_init();
		curl_setopt($c, CURLOPT_URL, $url);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_USERAGENT, 'Codular Sample cURL Request');

		$r = curl_exec($c);
		curl_close($c);
        }


	public function delJob($jobids){
		$service = "terminateActivity";
		return $this->post($jobids,$service);
	}

	public function getErr(){
        	if ($this->stderr)
			return $this->stderr;
		else
			return NULL;
	}


	public function getJobId(){
                return $this->jobid;
        }
	public function getServer(){
                return $this->server;
        }

	public function getCurrentCloud(){
		print "<br>CurrentCloud = ".$_SERVER['HTTP_HOST']." <br>";

		if (!isset($_SERVER['HTTP_HOST']))
			return 0;
		foreach ($GLOBALS['clouds'] as $cloudName => $cloudInfo){
			if ($cloudInfo['http_host'] == $_SERVER['HTTP_HOST'])
				return $cloudName;
		}
		$_SESSION['errorData']['Warning'][]="No MuG cloud associated to current host domain '".$_SERVER['HTTP_HOST']."'";
		return 0;
	}

	public function status(){
		return 1;
	}

	public function start(){
		return 1;
	}

	public function stop(){
		return 1;
	}
}
?>
