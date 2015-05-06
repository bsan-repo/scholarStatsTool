<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Journal
 *
 * @author 6opC4C3
 */
class Journal {
    public $id;
    public $msaId;
    public $homepage;
    public $fullname;
    public $eraEntry;
    
    function __construct() {
        $this->id = null;
        $this->msaId = null;
        $this->eraEntry = null;
    }
    
    function __get($name) {
        return $this->$name;
    }
    
    function __set($name, $value) {
        $this->$name = $value;
    }
}
