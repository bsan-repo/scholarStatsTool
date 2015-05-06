<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EraJournalDao
 *
 * @author 6opC4C3
 */
class EraJournalDao {
    
    public function insert(&$eraJournal){
        try {
            $db = new PDO('mysql:host=127.0.0.1;port=8889;dbname=academic;charset=utf8', 'root', 'root');
            
            $stmt = $db->prepare('insert into era_journal(era_id, name, acronym, rank) values(?, ?, ?, ?)');
            $affectedRows = $stmt->execute(array($eraJournal->eraId, $eraJournal->name, $eraJournal->acronym, $eraJournal->rank));
            // Get the id
            $id = $db->lastInsertId('id');
            $eraJournal->id = $id;
            
            $stmt->closeCursor();
            $stmt = null;
            print('Inserted era journal ['.$id.']: '.$affectedRows."\n");
            
        } catch(PDOException $ex) {
            echo "DB Exception(EraJournalDao): ".$ex->getMessage();
        }finally{
            $db = null;
        }
    }
}
