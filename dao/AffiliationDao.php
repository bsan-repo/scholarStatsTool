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
}