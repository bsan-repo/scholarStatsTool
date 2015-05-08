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
    
    // Returned authors contain only the id and the affiliation msa id
    public function findAuthorDetailsWithoutAffiliation(){
        $authorDetails = array();
        try {
            $db = new PDO('mysql:host=127.0.0.1;port=8889;dbname=academic;charset=utf8', 'root', 'root');
            
            $stmt = $db->query('SELECT id, msa_affiliation_id FROM author_details WHERE affiliation_id is NULL');
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt = null;
            
            $index = 0;
            foreach ($results as $result){
                $authorDetails[$index] = new AuthorDetails();
                $authorDetails[$index]->id = $result['id'];
                $authorDetails[$index]->msaAffiliationId = $result['msa_affiliation_id'];
                $index++;
            }
        } catch(PDOException $ex) {
            echo "DB Exception (AuthorDetailsDao): ".$ex->getMessage();
        }finally{
            $db = null;
        }
        return $authorDetails;
    }
    
    public function fixAffiliationForeignKey(){
        try {
            $db = new PDO('mysql:host=127.0.0.1;port=8889;dbname=academic;charset=utf8', 'root', 'root');
            
            $stmt = $db->query('UPDATE author_details as ad LEFT JOIN affiliation as af ON ad.msa_affiliation_id = af.msa_id SET ad.affiliation_id = af.id where ad.affiliation_id is NULL AND ad.msa_affiliation_id != 0');
            $affectedRows = $stmt->execute();
 
            $stmt->closeCursor();
            $stmt = null;
            print('Updated author details - affiliations: '.$affectedRows."\n");
            
        }catch(PDOException $ex) {
            echo "DB Exception(PaperReferenceDao): ".$ex->getMessage();
        }finally{
            $db = null;
        }
    }
}
