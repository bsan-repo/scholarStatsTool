<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PaperReference
 *
 * @author 6opC4C3
 */
class PaperReference {
    public $id;
    public $paperId;
    public $citationId;
    public $msaPaperId;
    public $msaCitationId;
    public $msaSeqId;
    
    function __construct() {
        $this->id = null;
        $this->paperId = null;
        $this->citationId = null;
        $this->msaPaperId = null;
        $this->msaCitationId = null;
        $this->msaSeqId = null;
    }
    
    function __get($name) {
        return $this->$name;
    }
    
    function __set($name, $value) {
        $this->$name = $value;
    }
}
