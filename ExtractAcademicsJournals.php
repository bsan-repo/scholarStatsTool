<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExtractAcademicsJournals
 *
 * @author 6opC4C3
 */
class ExtractAcademicsJournals {
    private $queryMsa;
    private $jsonToObj;
    
    public function __construct(){
        $this->queryMsa = new QueryMsa();
        $this->jsonToObj = new JsonToObject();
    }
    
    private function setJournalIdToPaperArray($batchCount, $papers, $journalsFound){
        $countPaper = $batchCount*QueryMsa::CITATIONS_PER_REQUEST;
        $countJournal = 0;
        // Iterate for the size of the batch without exceding the number
        // of elements in authorPapers
        for($count = 0; $count < QueryMsa::CITATIONS_PER_REQUEST, $countPaper < count($papers); $count++){
            if(isset($papers[$countPaper])){
                $paper = $papers[$countPaper];
                if(isset($journalsFound[$countJournal]) && $paper->msaJournalId == $journalsFound[$countJournal]->msaId){
                    $paper->journalId = $journalsFound[$countJournal]->id;
                }else{
                    foreach($journalsFound as $journalFound){
                        if($paper->msaJournalId == $journalFound->msaId){
                            $paper->journalId = $journalFound->id;
                        }
                    }
                }
                $countPaper++;
            }
            $countPaper++;
        }
    }
    
    private function saveJournals(&$journals){
        $journalDao = new JournalDao();
        foreach($journals as $journal){
            $journalDao->insert($journal);
        }
    }
    
    private function searchJournalsByIds($batchCount, &$papers, $journalIds){
        $papersFound = array();
        $jsonResults = $this->queryMsa->searchJournals($journalIds);
        if(isset($jsonResults)){
            $journalsFound = $this->jsonToObj->toJournals($jsonResults);
            $this->saveJournals($journalsFound);
            $this->setJournalIdToPaperArray($batchCount, $papers, $journalsFound);
        }
        return $papersFound;
    }
    
    public function retrieveJournalsForPapers(){
        $paperDao = new PaperDao();
        // Papers without journal
        $papers = $paperDao->findPapersWithoutJournal();
        
        $recordIds = array();    
        $count = 0;
        $flush = false;
        $batchCount = 0;
        
        foreach($papers as $paper){
            $flush = false;
            $recordIds[$count] = $paperRef->msaPaperId;
            if($count >= QueryMsa::CITATIONS_PER_REQUEST){
                $this->searchJournalsByIds($batchCount, $papers, $recordIds);
                $count = 0;
                unset($recordIds);
                $recordIds = array();
                $flush = true;
                $batchCount++;
            }else{
                $count++;
            }
        }
        
        if($flush == false){
            $this->searchJournalsByIds($batchCount, $papers, $recordIds);
        }
        
        // Update the papers journal ids
        foreach($papers as $paper){
            $paperDao->updateJournalIdForPaper($paper);
        }
    }
}
