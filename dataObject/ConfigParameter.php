<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ConfigParameter
 *
 * @author 6opC4C3
 */
class ConfigParameter {
    public $id;
    public $name;
    public $value;
    
    function __construct() {
        $this->id = null;
    }
    
    function __get($name) {
        return $this->$name;
    }
    
    function __set($name, $value) {
        $this->$name = $value;
    }
}
