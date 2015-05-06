<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AuthorDetailsDao
 *
 * @author 6opC4C3
 */
class AuthorDetailsDao {
    
    public function insert(&$authorDetails){
        try {
            $db = new PDO('mysql:host=127.0.0.1;port=8889;dbname=academic;charset=utf8', 'root', 'root');
            
            $stmt = $db->prepare('insert into author_details(msa_id, affiliation, msa_affiliation_id, research_interest, homepage_url, version) values(?, ?, ?, ?, ?, ?)');
            $affectedRows = $stmt->execute(array($authorDetails->msaId, $authorDetails->affiliation, $authorDetails->msaAffiliationId, $authorDetails->researchInterest, $authorDetails->homepageUrl, $authorDetails->version));
            
            // Get the id
            $id = $db->lastInsertId('id');
            $authorDetails->id = $id;
            
            $stmt->closeCursor();
            $stmt = null;
            print('Inserted author details ['.$id.']: '.$affectedRows."\n");
            
        } catch(PDOException $ex) {
            echo "DB Exception(AuthorDetailsDao): ".$ex->getMessage();
        }finally{
            $db = null;
        }
    }
}
