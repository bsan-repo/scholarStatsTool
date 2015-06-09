<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExtractAcademicsAffiliations
 *
 * @author 6opC4C3
 */
class ExtractAcademicsAffiliations {
    private $queryMsa;
    private $jsonToObj;
    
    public function __construct(){
        $this->queryMsa = new QueryMsa();
        $this->jsonToObj = new JsonToObject();
    }
    
    private function saveAffiliations(&$affiliations){
        $affiliationDao = new AffiliationDao();
        foreach($affiliations as $affiliation){
            $affiliationDao->insert($affiliation);
        }
    }
    
    private function searchAffiliationsByIds($batchCount, $affiliationIds){
        $affiliationsFound = array();
        $jsonResults = $this->queryMsa->searchAffiliations($affiliationIds);
        if(isset($jsonResults)){
            $affiliationsFound = $this->jsonToObj->toAffiliations($jsonResults);
            $this->saveAffiliations($affiliationsFound);
        }
        return $affiliationsFound;
    }
    
    public function retrieveAffiliationsForAuthors(){
        // performance check
        print("INIT retrieveAffiliationsForAuthors");
        $affiliationDao = new AffiliationDao();
        $authorDetailsDao = new AuthorDetailsDao();
        // Papers without journal
        $authorDetails = $authorDetailsDao->findAuthorDetailsWithoutAffiliation();
        
        $recordIds = array();    
        $count = 0;
        $flush = false;
        $batchCount = 0;
        
        foreach($authorDetails as $authorDetail){
            $AffiliationCurrentId = $affiliationDao->findIdByMsaId($authorDetail->msaAffiliationId);
            $flush = false;
            if(isset($AffiliationCurrentId) == false && $authorDetail->msaAffiliationId > 0){
                $recordIds[$count] = $authorDetail->msaAffiliationId;
                if($count >= QueryMsa::MAX_RECORDS_PER_QUERY){
                    // performance check
                    print("PROCESS BATCH(".$batchCount.") retrieveAffiliationsForAuthors");
                    $recordIdsUnique = array_unique($recordIds, SORT_NUMERIC);
                    $this->searchAffiliationsByIds($batchCount, $recordIdsUnique);
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
            // performance check
            print("PROCESS BATCH(".$batchCount.") LAST retrieveAffiliationsForAuthors");
            $recordIdsUnique = array_unique($recordIds, SORT_NUMERIC);
            $this->searchAffiliationsByIds($batchCount, $recordIdsUnique);
        }
        // performance check
        print("END retrieveAffiliationsForAuthors");
    }
}
