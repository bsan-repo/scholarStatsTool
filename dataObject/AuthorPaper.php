<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of newPHPClass
 *
 * @author 6opC4C3
 */
class AuthorPaper {
    public $id;
    public $authorId;
    public $paperId;
    public $msaPaperId;
    public $msaAuthorId;
    public $msaSeqId;
    
    function __construct() {
        $this->id = null;
        $this->authorId = null;
        $this->paperId = null;
    }
    
    function __get($name) {
        return $this->$name;
    }
    
    function __set($name, $value) {
        $this->$name = $value;
    }
}
