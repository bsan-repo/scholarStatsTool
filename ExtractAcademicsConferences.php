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
    
    private function setConferenceIdToPaperArray($batchCount, $papers, $conferencesFound){
        $countPaper = $batchCount*QueryMsa::CITATIONS_PER_REQUEST;
        $countConference = 0;
        // Iterate for the size of the batch without exceding the number
        // of elements in authorPapers
        for($count = 0; $count < QueryMsa::CITATIONS_PER_REQUEST, $countPaper < count($papers); $count++){
            if(isset($papers[$countPaper])){
                $paper = $papers[$countPaper];
                if(isset($conferencesFound[$countConference]) && $paper->msaConferenceId == $conferencesFound[$countConference]->msaId){
                    $paper->conferenceId = $conferencesFound[$countConference]->id;
                }else{
                    foreach($conferencesFound as $conferenceFound){
                        if($paper->msaConferenceId == $conferenceFound->msaId){
                            $paper->conferenceId = $conferenceFound->id;
                        }
                    }
                }
                $countConference++;
            }
            $countPaper++;
        }
    }
    
    private function saveConferences(&$conferences){
        $conferenceDao = new ConferenceDao();
        foreach($conferences as $conference){
            $conferenceDao->insert($conference);
        }
    }
    
    private function searchConferencesByIds($batchCount, &$papers, $conferenceIds){
        $papersFound = array();
        $jsonResults = $this->queryMsa->searchJournals($conferenceIds);
        if(isset($jsonResults)){
            $conferencesFound = $this->jsonToObj->toJournals($jsonResults);
            $this->saveConferences($conferencesFound);
            $this->setJournalIdToPaperArray($batchCount, $papers, $conferencesFound);
        }
        return $papersFound;
    }
    
    public function retrieveJournalsForPapers(){
        $paperDao = new PaperDao();
        // Papers without journal
        $papers = $paperDao->findPapersWithoutConference();
        
        $recordIds = array();    
        $count = 0;
        $flush = false;
        $batchCount = 0;
        
        foreach($papers as $paper){
            $flush = false;
            $recordIds[$count] = $paperRef->msaPaperId;
            if($count >= QueryMsa::CITATIONS_PER_REQUEST){
                $this->searchConferencesByIds($batchCount, $papers, $recordIds);
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
            $this->searchConferencesByIds($batchCount, $papers, $recordIds);
        }
        
        // Update the papers journal ids
        foreach($papers as $paper){
            $paperDao->updateJournalIdForPaper($paper);
        }
    }
}
