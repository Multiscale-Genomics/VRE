<?php

class User {

    public $_id; //= Email
    public $Surname;
    public $Name;
    public $Inst;
    public $Country;
    public $Email;
    public $crypPassword;
    public $lastLogin;
    public $registrationDate;
    public $Type;
    public $Status;
    public $diskQuota;
    public $dataDir;
    public $DataSample;
    public $Token;
    public $AuthProvider;
    public $id;
//	public $dataDir;

    function __construct($f) {

        if (!$f['Email'])
            return 0;

        foreach (array('Surname','Name','Inst','Country','Email','Type','dataDir','diskQuota','DataSample','AuthProvider') as $k)
            $this->$k= sanitizeString($f[$k]);

        if ($f['pass1']){
            //$this->crypPassword = password_hash($f['pass1'], PASSWORD_DEFAULT);
            //$this->crypPassword = crypt($f['pass1'], '$6$'.randomSalt(8).'$');
            $salt = substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',4)),0,4);
            $this->crypPassword = '{SSHA}' . base64_encode(sha1( $f['pass1'].$salt, TRUE ). $salt);
        }elseif ($f['Token']){
            $this->Token        = $f['Token'];
        }elseif ($f['Type'] == 3){
        }else{
            return 0;
        }
    	$this->Status       = "1";
    	$this->Type         = (!isset($this->Type)?$this->Type=2:$this->Type=$this->Type);
    	$this->_id          = $this->Email;
    	$this->id           = ($this->Type!=3?uniqid($GLOBALS['AppPrefix'] . "USER"):uniqid($GLOBALS['AppPrefix'] . "ANON"));
    	$this->lastLogin    = moment();
    	$this->registrationDate = moment();
    	$this->Surname      = ucfirst($this->Surname);
        $this->Name         = ucfirst($this->Name);
    	$this->diskQuota    = (!$this->diskQuota && $this->Type!=3? $GLOBALS['DISKLIMIT']:$GLOBALS['DISKLIMIT_ANON']);
    	$this->DataSample   = ($this->DataSample?$this->DataSample:$GLOBALS['sampleData_default']);

        return $this;
    }
}

?>
