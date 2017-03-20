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
    public $id;
//	public $dataDir;

    function __construct($f) {
        foreach (array('Surname','Name','Inst','Country','Email','Type','DataDir') as $k)
		$this->$k= sanitizeString($f[$k]);

	$this->Status       = "1";
	$this->_id          = $this->Email;
	$this->id           = uniqid($GLOBALS['AppPrefix'] . "USER");
	$this->crypPassword = password_hash($f['pass1'], PASSWORD_DEFAULT);
	$this->lastLogin    = moment();
	$this->registrationDate = moment();
	$this->Surname      = ucfirst($this->Surname);
	$this->Name         = ucfirst($this->Name);
	$this->diskQuota    = $GLOBALS['DISKLIMIT']; /* TODO set up as $GLOBALS['disklimit'] or $GLOBALS['disklimitAnon']? */
        return $this;
    }
}

?>
