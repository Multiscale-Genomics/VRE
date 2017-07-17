<?php


Class FTPClient {

   public function __construct() {}

   public $connectionId;
   private $loginOk = false;
   private $messageArray = array();

    public function connect ($server, $ftpUser, $ftpPassword, $isPassive = false){
 
	$this->connectionId = ftp_connect($server);
	$loginResult = ftp_login($this->connectionId, $ftpUser, $ftpPassword);
 
	// *** mode on/off (default off)
	ftp_pasv($this->connectionId, $isPassive);
 
	if ((!$this->connectionId) || (!$loginResult)) {
	        $this->logMessage('FTP connection has failed!');
	        $this->logMessage('Attempted to connect to ' . $server . ' for user ' . $ftpUser, true);
	        return false;
	} else {
	        $this->logMessage('Connected to ' . $server . ', for user ' . $ftpUser);
	        $this->loginOk = true;
	        return true;
	 }
   }

   public function makeDir($directory) {
    if (ftp_mkdir($this->connectionId, $directory)) {
        $this->logMessage('Directory "' . $directory . '" created successfully');
        return true;
    } else {
        $this->logMessage('Failed creating directory "' . $directory . '"');
        return false;
    }
   }

   private function logMessage($message) {
	$this->messageArray[] = $message;
   }
   public function getMessages(){
	return $this->messageArray;
   }
}














