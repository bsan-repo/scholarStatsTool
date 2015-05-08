<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExtractAcademicsConferences
 *
 * @author 6opC4C3
 */
class ExtractAcademicsConferences {
    private $queryMsa;
    private $jsonToObj;
    
    public function __construct(){
        $this->queryMsa = new QueryMsa();
        $this->jsonToObj = new JsonToObject();
    }
    
    private function saveConferences(&$conferences){
        $conferenceDao = new ConferenceDao();
        foreach($conferences as $conference){
            $conferenceDao->insert($conference);
        }
    }
    
    private function searchConferencesByIds($batchCount, $conferenceIds){
        $conferencesFound = array();
        $jsonResults = $this->queryMsa->searchConferences($conferenceIds);
        if(isset($jsonResults)){
            $conferencesFound = $this->jsonToObj->toConferences($jsonResults);
            $this->saveConferences($conferencesFound);
        }
        return $conferencesFound;
    }
    
    public function retrieveConferencesForPapers(){
        $paperDao = new PaperDao();
        // Papers without journal
        $papers = $paperDao->findPapersWithoutConference();
        
        $recordIds = array();    
        $count = 0;
        $flush = false;
        $batchCount = 0;
        
        foreach($papers as $paper){
            $flush = false;
            if($paper->msaConferenceId > 0){
                $recordIds[$count] = $paper->msaConferenceId;
                if($count >= QueryMsa::MAX_RECORDS_PER_QUERY){
                    $recordIdsUnique = array_unique($recordIds, SORT_NUMERIC);
                    $this->searchConferencesByIds($batchCount, $recordIdsUnique);
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
            $this->searchConferencesByIds($batchCount, $recordIdsUnique);
        }
    }
}
