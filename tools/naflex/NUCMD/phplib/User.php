<?php
/**
 * Description of user
 *
 * @author gelpi
 */
#namespace Classes\PMut;

class User {

    public $_id; //= Email
    public $Surname;
    public $Name;
    public $Inst;
    public $Country;
    public $Email;
    public $crypPassword;
    public $lastLogin;
    public $Uploader;
    public $id;
    
    function __construct($f) {
        foreach (array('Surname','Name','Inst','Country','Email','Uploader') as $k)
            $this->$k=$f[$k];        
        $this->_id = $this->Email;
        $this->crypPassword = crypt($f['pass1'],PASSWORD_SALT);
        $this->lastLogin = moment();
	$this->id = uniqid($GLOBALS['AppPrefix'] . "USER");
        return $this;
    }
}
?>
