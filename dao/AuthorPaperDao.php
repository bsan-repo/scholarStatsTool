<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AuthorPaperDao
 *
 * @author 6opC4C3
 */
class AuthorPaperDao {
    
    public function insert(&$authorPaper){
        try {
            $db = new PDO('mysql:host=127.0.0.1;port=8889;dbname=academic;charset=utf8', 'root', 'root');
            
            $stmt = $db->prepare('insert into author_paper(author_id, paper_id, msa_paper_id, msa_author_id, msa_seq_id) values(?, ?, ?, ?, ?)');
            $affectedRows = $stmt->execute(array($authorPaper->authorId, $authorPaper->paperId, $authorPaper->msaPaperId, $authorPaper->msaAuthorId, $authorPaper->msaSeqId));
            // Get the id
            $id = $db->lastInsertId('id');
            $authorPaper->id = $id;
            
            $stmt->closeCursor();
            $stmt = null;
            print('Inserted author paper ['.$id.']: '.$affectedRows."\n");
            
        } catch(PDOException $ex) {
            echo "DB Exception(AuthorPaperDao): ".$ex->getMessage();
        }finally{
            $db = null;
        }
    }
    
    public function fixPaperForeignKey(){
        try {
            $db = new PDO('mysql:host=127.0.0.1;port=8889;dbname=academic;charset=utf8', 'root', 'root');
            
            $stmt = $db->query('UPDATE author_paper as ap LEFT JOIN paper as p ON ap.msa_paper_id = p.msa_id SET ap.paper_id = p.id where ap.paper_id = p.id is NULL AND ap.msa_paper_id != 0');
            $affectedRows = $stmt->execute();
 
            $stmt->closeCursor();
            $stmt = null;
            print('Updated author paper - paper fk: '.$affectedRows."\n");
            
        }catch(PDOException $ex) {
            echo "DB Exception(AuthorPaperDao): ".$ex->getMessage();
        }finally{
            $db = null;
        }
    }
}
