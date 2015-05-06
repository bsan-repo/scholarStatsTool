<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EraConference
 *
 * @author 6opC4C3
 */
class EraConference {
    public $id;
    public $eraId;
    public $name;
    public $acronym;
    public $rank;
    
    function __construct() {
        $this->id = null;
        $this->eraId = null;
        $this->rank = null;
    }
    
    function __get($name) {
        return $this->$name;
    }
    
    function __set($name, $value) {
        $this->$name = $value;
    }
}
