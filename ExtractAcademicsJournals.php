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
    
    private function saveJournals(&$journals){
        $journalDao = new JournalDao();
        foreach($journals as $journal){
            $journalDao->insert($journal);
        }
    }
    
    private function searchJournalsByIds($batchCount, $journalIds){
        $journalsFound = array();
        $jsonResults = $this->queryMsa->searchJournals($journalIds);
        if(isset($jsonResults)){
            $journalsFound = $this->jsonToObj->toJournals($jsonResults);
            $this->saveJournals($journalsFound);
        }
        return $journalsFound;
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
            if($paper->msaJournalId > 0){
                $recordIds[$count] = $paper->msaJournalId;
                if($count >= QueryMsa::MAX_RECORDS_PER_QUERY){
                    $recordIdsUnique = array_unique($recordIds, SORT_NUMERIC);
                    $this->searchJournalsByIds($batchCount, $recordIdsUnique);
                    $count = 0;
                    unset($recordIds);
                    $recordIds = array();
                    $flush = true;
                    $batchCount++;
                }else{
                    $count++;
                }
            }
        }
        
        if($flush == false && count($recordIds)> 0){
            $recordIdsUnique = array_unique($recordIds, SORT_NUMERIC);
            $this->searchJournalsByIds($batchCount, $recordIdsUnique);
        }
        (new JournalDao())->fixEraEntryForeignKey();
    }
}
