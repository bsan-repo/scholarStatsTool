<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExtractAcademicsReferences
 *
 * @author 6opC4C3
 */
class ExtractAcademicsReferences {
    private $queryMsa;
    private $jsonToObj;
    
    public function __construct(){
        $this->queryMsa = new QueryMsa();
        $this->jsonToObj = new JsonToObject();
    }
    
    private function searchPapersByIds($batchCount, $papersIds){
        $papersFound = array();
        $jsonResults = $this->queryMsa->searchPapers($papersIds);
        if(isset($jsonResults)){
            $papersFound = $this->jsonToObj->toPapers($jsonResults);
        }
        return $papersFound;
    }
    
    // Retreive the papers for the references (citation) and stores them to the DB
    // The id of the paper is added to the reference -> citation
    public function retrievePapersForReferences(&$paperRefs){
        $paperDao = new PaperDao();
        $papersIds = array();    
        $count = 0;
        $flush = false;
        $batchCount = 0;
        $papers = array();
        foreach($paperRefs as $paperRef){
            $foundIdForMsaId = $paperDao->findIdByMsaId($paperRef->msaCitationId);
            if(isset($foundIdForMsaId) == false && $paperRef->msaCitationId > 0){
                $flush = false;
                $papersIds[$count] = $paperRef->msaCitationId;
                if($count >= QueryMsa::MAX_RECORDS_PER_QUERY){
                    // Calls method searchPapersByIds initially designed for 
                    // author papers but this time using paper references as
                    // both share the same attributes used in this method
                    
                    // The returned papers are already stored to the DB
                    // Remove duplicates
                    $papersIdsUnique = array_unique($papersIds, SORT_NUMERIC);
                    $papersFound = $this->searchPapersByIds($batchCount, $papersIdsUnique);
                    $papers = $papers + $papersFound;
                    $count = 0;
                    unset($papersIds);
                    $papersIds = array();
                    $flush = true;
                    $batchCount++;
                }else{
                    $count++;
                }
            }else{
                $paperRef->paperId = $foundIdForMsaId;
            }
        }
        if($flush == false && count($papersIds)> 0){
        // The returned papers are already stored to the DB
            $papersIdsUnique = array_unique($papersIds, SORT_NUMERIC);
            $papersFound = $this->searchPapersByIds($batchCount, $papersIdsUnique);
            $papers = $papers + $papersFound;
        }
        // The returned papers are already stored to the DB
        return $papers;
    }
}
