<?php
/**
 * Description of user
 *
 * @author gelpi
 */
#namespace Classes\PMut;

class AnonUser {

    public $_id; //= Email
    public $Name;
    public $lastLogin;
    public $id;
    
    function __construct() {
        $this->Name = "Anonymous";
	$code = uniqid($GLOBALS['AppPrefix'] . "ANONUSER");
	$this->_id = $code;
	$this->id = $code;
        $this->lastLogin = moment();
        return $this;
    }
}
?>
