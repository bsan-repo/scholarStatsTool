<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PaperDao
 *
 * @author 6opC4C3
 */
class PaperDao {
    
    public function insert(&$paper){
        try {
            $db = new PDO('mysql:host=127.0.0.1;port=8889;dbname=academic;charset=utf8', 'root', 'root');
            
            $stmt = $db->prepare('insert into paper(conference_id, journal_id, year, title, msa_id, keyword, msa_conference_id, msa_journal_id) values(?, ?, ?, ?, ?, ?, ?, ?)');
            $affectedRows = $stmt->execute(array($paper->conferenceId, $paper->journalId, $paper->year, $paper->title, $paper->msaId, $paper->keyword, $paper->msaConferenceId, $paper->msaJournalId));
            // Get the id
            $id = $db->lastInsertId('id');
            $paper->id = $id;
            
            $stmt->closeCursor();
            $stmt = null;
            print('Inserted paper ['.$id.']: '.$affectedRows."\n");
            
        }catch(PDOException $ex) {
            echo "DB Exception(PaperDao): ".$ex->getMessage();
        }finally{
            $db = null;
        }
    }
    
    public function findIdByMsaId($recordMsaId){
        $id = null;
        try {
            $db = new PDO('mysql:host=127.0.0.1;port=8889;dbname=academic;charset=utf8', 'root', 'root');
            
            $stmt = $db->prepare('select id from paper where msa_id=?');
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
    
    // Returned papers contain only the id and the journal msa id
    public function findPapersWithoutJournal(){
        $papers = array();
        try {
            $db = new PDO('mysql:host=127.0.0.1;port=8889;dbname=academic;charset=utf8', 'root', 'root');
            
            $stmt = $db->query('SELECT id, msa_journal_id FROM paper WHERE journal_id is NULL AND msa_conference_id=0 AND msa_journal_id!=0');
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt = null;
            
            $index = 0;
            foreach ($results as $result){
                $papers[$index] = new Paper();
                $papers[$index]->id = $result['id'];
                $papers[$index]->msaJournalId = $result['msa_journal_id'];
                $index++;
            }
        } catch(PDOException $ex) {
            echo "DB Exception: ".$ex->getMessage();
        }finally{
            $db = null;
        }
        return $papers;
    }
    
    // Returned papers contain only the id and the conference msa id
    public function findPapersWithoutConference(){
        $papers = array();
        try {
            $db = new PDO('mysql:host=127.0.0.1;port=8889;dbname=academic;charset=utf8', 'root', 'root');
            
            $stmt = $db->query('SELECT id, msa_conference_id FROM paper WHERE conference_id is NULL AND msa_journal_id=0 AND msa_conference_id!=0');
            
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt = null;
            
            $index = 0;
            foreach ($results as $result){
                $papers[$index] = new Paper();
                $papers[$index]->id = $result['id'];
                $papers[$index]->msaConferenceId = $result['msa_conference_id'];
                $index++;
            }
        } catch(PDOException $ex) {
            echo "DB Exception: ".$ex->getMessage();
        }finally{
            $db = null;
        }
        return $papers;
    }
    
    public function fixJournalsAndConferencesForeignKey(){
        try {
            $db = new PDO('mysql:host=127.0.0.1;port=8889;dbname=academic;charset=utf8', 'root', 'root');
            
            $stmt = $db->query('UPDATE paper as p LEFT JOIN journal as j ON p.msa_journal_id = j.msa_id SET p.journal_id = j.id where p.journal_id is NULL AND p.msa_journal_id != 0');
            $affectedRows = $stmt->execute();
            
            $stmt = $db->query('UPDATE paper as p LEFT JOIN conference as cf ON p.msa_conference_id = cf.msa_id SET p.conference_id = cf.id where p.conference_id is NULL AND p.msa_conference_id != 0');
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
