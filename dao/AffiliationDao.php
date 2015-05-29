<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AffiliationDao
 *
 * @author 6opC4C3
 */
class AffiliationDao {
    
    public function insert(&$affiliation){
        // QUICK FIX
        // TODO Check this part of the code if there are any problems with collecting the papers
        
        $existingRecordId = $this->findIdByMsaId($affiliation->msaId);
        
        if($existingRecordId != null){
            $affiliation->id = $existingRecordId;
            return;
        }
        
        // Default path to insert the record
        try {
            $db = new PDO('mysql:host=127.0.0.1;port=8889;dbname=academic;charset=utf8', 'root', 'root');
            
            $stmt = $db->prepare('insert into affiliation(msa_id, official_name, homepage, latitude, longitude) values(?, ?, ?, ?, ?)');
            $affectedRows = $stmt->execute(array($affiliation->msaId, $affiliation->officialName, $affiliation->homepage, $affiliation->latitude, $affiliation->longitude));
            // Get the id
            $id = $db->lastInsertId('id');
            $affiliation->id = $id;
            
            $stmt->closeCursor();
            $stmt = null;
            print('Inserted affiliation ['.$id.']: '.$affectedRows."\n");
            
        } catch(PDOException $ex) {
            echo "DB Exception(AffiliationDao): ".$ex->getMessage();
        }finally{
            $db = null;
        }
    }
    
    public function findIdByMsaId($recordMsaId){
        $id = null;
        try {
            $db = new PDO('mysql:host=127.0.0.1;port=8889;dbname=academic;charset=utf8', 'root', 'root');
            
            $stmt = $db->prepare('select id from affiliation where msa_id=?');
            $stmt->execute(array($recordMsaId));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt = null;
            
            if (isset($result)){
                $id = $result['id'];
            }
            
        } catch(PDOException $ex) {
            echo "DB Exception: ".$ex->getMessage();
        }finally{
            $db = null;
        }
        return $id;
    }
}