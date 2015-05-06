<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Author
 *
 * @author 6opC4C3
 */
class Author {
    public $id;
    public $name;
    public $detailsId;
    public $details;
    
    function __construct() {
        $this->id = null;
        $this->details = null;
    }
    
    function __get($name) {
        return $this->$name;
    }
    
    function __set($name, $value) {
        $this->$name = $value;
    }
}
