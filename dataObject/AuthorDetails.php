<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AuthorDetails
 *
 * @author 6opC4C3
 */
class AuthorDetails {
    public $id;
    public $msaId;
    // Currently affialiation_id is only used in DAO for ref the affialiation
    public $affiliation;
    public $msaAffiliationId;
    public $researchInterest;
    public $homepageUrl;
    public $version;
    
    function __construct() {
        $this->id = null;
        $this->msaId = null;
    }
    
    function __get($name) {
        return $this->$name;
    }
    
    function __set($name, $value) {
        $this->$name = $value;
    }
}
