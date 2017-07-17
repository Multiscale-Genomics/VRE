<?php

namespace PMut;

/**
 * Description of ProjectClass
 *
 * @author gelpi
 */
class ProjectClass {
    
    const SETUP = 0;
    const SUBMITTED = 1;
    const PENDING = 2;
    const FINISHED = 3;
//
    public $id;
    public $title;
    public $owner;
    public $status;
    public $started;
    public $lastUpdate;
    public $querySequenceData;

    
    function __construct() {
        $this->id = uniqid($GLOBALS['AppPrefix'].'P');
        $this->owner = $_SESSION['User']->_id;
        $this->status = SETUP;
        $this->started = moment();
        $this->lastUpdate = $this->started;
        return $this;
    }         
    
    function finished () {
        return ($this->status == FINISHED);
    }
}
