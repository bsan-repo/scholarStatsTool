<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of JournalDao
 *
 * @author 6opC4C3
 */
class JournalDao {
    
    public function insert(&$journal){
        try {
            $db = new PDO('mysql:host=127.0.0.1;port=8889;dbname=academic;charset=utf8', 'root', 'root');
            
            $stmt = $db->prepare('insert into journal(msa_id, fullname, homepage, era_entry) values(?, ?, ?, ?)');
            $affectedRows = $stmt->execute(array($journal->msaId, $journal->fullname, $journal->homepage, $journal->eraEntry));
            // Get the id
            $id = $db->lastInsertId('id');
            $journal->id = $id;
            
            $stmt->closeCursor();
            $stmt = null;
            print('Inserted journal ['.$id.']: '.$affectedRows."\n");
            
        } catch(PDOException $ex) {
            echo "DB Exception(JournalDao): ".$ex->getMessage();
        }finally{
            $db = null;
        }
    }
}
