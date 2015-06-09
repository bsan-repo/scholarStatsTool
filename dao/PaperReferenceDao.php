<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PaperReferenceDao
 *
 * @author 6opC4C3
 */
class PaperReferenceDao {
    
    public function insert(&$paperref){
        try {
            $db = new PDO('mysql:host=127.0.0.1;port=8889;dbname=academic;charset=utf8', 'root', 'root');
            
            $stmt = $db->prepare('insert into paper_ref(paper_id, citation_id, msa_paper_id, msa_citation_id, msa_seq_ref) values(?, ?, ?, ?, ?)');
            $affectedRows = $stmt->execute(array($paperref->paperId, $paperref->citationId, $paperref->msaPaperId, $paperref->msaCitationId, $paperref->msaSeqId));
            // Get the id
            $id = $db->lastInsertId('id');
            $paperref->id = $id;
            
            $stmt->closeCursor();
            $stmt = null;
            print('Inserted paper ref ['.$id.']: '.$affectedRows."\n");
            
        } catch(PDOException $ex) {
            echo "DB Exception(PaperReferenceDao): ".$ex->getMessage();
        }finally{
            $db = null;
        }
    }
    
    public function fixPaperCitationForeignKey(){
        // performance check
        print("INIT PaperReferenceDao fixPaperCitationForeignKey");
        try {
            $db = new PDO('mysql:host=127.0.0.1;port=8889;dbname=academic;charset=utf8', 'root', 'root');
            
            $stmt = $db->query('UPDATE paper_ref as pf LEFT JOIN paper as p ON pf.msa_citation_id = p.msa_id SET pf.citation_id = p.id where pf.citation_id is NULL AND pf.msa_citation_id != 0');
            $affectedRows = $stmt->execute();
 
            $stmt->closeCursor();
            $stmt = null;
            print('Updated paper ref - citation refs: '.$affectedRows."\n");
            
        }catch(PDOException $ex) {
            echo "DB Exception(PaperReferenceDao): ".$ex->getMessage();
        }finally{
            $db = null;
        }
        // performance check
        print("END PaperReferenceDao fixPaperCitationForeignKey");
    }
}
