<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Affiliation
 *
 * @author 6opC4C3
 */
class Affiliation {
    public $id;
    public $msaId;
    public $officialName;
    public $homepage;
    public $latitude;
    public $longitude;
    
    function __construct() {
        $this->id = null;
        $this->msaId = null;
        $this->latitude = 0.0;
        $this->longitude = 0.0;
    }
    
    function __get($name) {
        return $this->$name;
    }
    
    function __set($name, $value) {
        $this->$name = $value;
    }
}
