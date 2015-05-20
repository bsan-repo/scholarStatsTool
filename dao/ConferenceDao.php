<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ConferenceDao
 *
 * @author 6opC4C3
 */
class ConferenceDao {
    
    public function insert(&$conference){
        try {
            $db = new PDO('mysql:host=127.0.0.1;port=8889;dbname=academic;charset=utf8', 'root', 'root');
            
            $stmt = $db->prepare('insert into conference(msa_id, fullname, homepage, era_entry) values(?, ?, ?, ?)');
            $affectedRows = $stmt->execute(array($conference->msaId, $conference->fullname, $conference->homepage, $conference->eraEntry));
            // Get the id
            $id = $db->lastInsertId('id');
            $conference->id = $id;
            
            $stmt->closeCursor();
            $stmt = null;
            print('Inserted conference ['.$id.']: '.$affectedRows."\n");
            
        } catch(PDOException $ex) {
            echo "DB Exception(ConferenceDao): ".$ex->getMessage();
        }finally{
            $db = null;
        }
    }
    
    public function fixEraEntryForeignKey(){
        try {
            $db = new PDO('mysql:host=127.0.0.1;port=8889;dbname=academic;charset=utf8', 'root', 'root');
            
            $stmt = $db->query('UPDATE conference AS cn LEFT JOIN era_conference ec ON cn.fullname = ec.name OR cn.fullname = ec.acronym SET cn.era_entry = ec.id where cn.era_entry IS NULL');
            $affectedRows = $stmt->execute();
 
            $stmt->closeCursor();
            $stmt = null;
            print('Updated conferences - era entries: '.$affectedRows."\n");
            
        }catch(PDOException $ex) {
            echo "DB Exception(ConferenceDao): ".$ex->getMessage();
        }finally{
            $db = null;
        }
    }
}
