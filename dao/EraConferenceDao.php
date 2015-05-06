<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EraConferenceDao
 *
 * @author 6opC4C3
 */
class EraConferenceDao {
    
    public function insert(&$eraConference){
        try {
            $db = new PDO('mysql:host=127.0.0.1;port=8889;dbname=academic;charset=utf8', 'root', 'root');
            
            $stmt = $db->prepare('insert into era_conference(era_id, name, acronym, rank) values(?, ?, ?, ?)');
            $affectedRows = $stmt->execute(array($eraConference->eraId, $eraConference->name, $eraConference->acronym, $eraConference->rank));
            // Get the id
            $id = $db->lastInsertId('id');
            $eraConference->id = $id;
            
            $stmt->closeCursor();
            $stmt = null;
            print('Inserted era conference ['.$id.']: '.$affectedRows."\n");
            
        } catch(PDOException $ex) {
            echo "DB Exception(EraConferenceDao): ".$ex->getMessage();
        }finally{
            $db = null;
        }
    }
}
