<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Paper
 *
 * @author 6opC4C3
 */
class Paper {
    public $id;
    public $conferenceId;
    public $journalId;
    public $year;
    public $title;
    public $msaId;
    public $keyword;
    public $msaConferenceId;
    public $msaJournalId;
            
    function __construct() {
        $this->id = null;
        $this->conferenceId = null;
        $this->journalId = null;
        $this->msaId = null;
    }
    
    function __get($name) {
        return $this->$name;
    }
    
    function __set($name, $value) {
        $this->$name = $value;
    }
}
