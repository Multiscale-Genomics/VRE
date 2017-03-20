<?php
/**
 * Description of user
 *
 * @author gelpi
 */
namespace PMut;

class AnonUser {
    public $_id; //= Email
    public $Surname = "Anonymous";
    public $Email;
    public $lastLogin;
    public $Anon = True;
    
    function __construct() {
        $this->Email = uniqid($GLOBALS['AppPrefix']);
        $this->_id = $this->Email;
        $this->lastLogin = moment();
        return $this;
    }    
    function fullName () {
        return "Anonymous (".$this->Email.")";
    }
    
}

class User extends AnonUser {

    public $Surname = '';
    public $Name;
    public $Inst;
    public $Country;
    public $crypPassword;
    public $Anon = False;
    public $uniqId;
    
    function __construct($f, $newPass=False) {
        foreach (array('Surname','Name','Inst','Country','Email','uniqId') as $k)
            $this->$k=$f[$k];        
        $this->_id = $this->Email;
        if ($newPass){
        	$this->crypPassword = crypt($f['pass1'],PASSWORD_SALT);
	}else{
		$this->crypPassword = $f['crypPassword'];
	}
        $this->lastLogin = moment();
        $this->Anon = False;
        //$this->uniqId = 'TP547ed08cc9ba8';
        if(! $this->uniqId){
		$this->uniqId = uniqId('TP');
		$text = str_replace($this->_id.":".$this->crypPassword.":", $this->_id.":".$this->crypPassword.":".$this->uniqId , file_get_contents($GLOBALS['passFile']));
		file_put_contents($GLOBALS['passFile'], $text);

	}
        return $this;
    }
    
    function update (&$f) {        
        // Possible Canvi email mantenim _id per no crear nou usuari, pero continuem cercant per Email
        foreach (array('Surname','Name','Inst','Country','Email') as $k)
            $this->$k=$f[$k];        
        if ($f['pass1'])
            $this->crypPassword = crypt($f['pass1'],PASSWORD_SALT);
        $this->lastLogin = moment();
        return $this;        
    }
    
    function fullName ($NameSurname = False, $inst = False) {
//        if ($NameSurname)
//            $txt = $this->Name." ".$this->Surname;
//        else
//            $txt = $this->Surname." ".$this->Name;
//        if ($inst and $this->Inst)
//            $txt .= " (".$this->Inst.")";
	$txt= $this->_id;
        return $txt;
    }

    function home () {
	return $this->Home;
    }
}
?>
