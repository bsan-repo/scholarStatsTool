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

    function savePapers($papers){
        $paperDao = new PaperDao();
        foreach($papers as $paper){
            $paperDao->insert($paper);
        }
    }
    
    private function setPaperIdToAuthorPaperArray($batchCount, &$paperRefs, &$papersFound){
        // Batch count is used to determine the author offset using the 
        // current batch count and the number of citations per batch 
        // (CITATIONS_PER_REQUEST).
        $countPaperRef = $batchCount*QueryMsa::CITATIONS_PER_REQUEST;
        $countPaperId = 0;
        // Iterate for the size of the batch without exceding the number
        // of elements in authorPapers
        for($count = 0; $count < QueryMsa::CITATIONS_PER_REQUEST, $countPaperRef < count($paperRefs); $count++){
            if(isset($paperRefs[$countPaperRef])){
                $paperRef = $paperRefs[$countPaperRef];
                if(isset($papersFound[$countPaperId]) && $paperRef->msaCitationId == $papersFound[$countPaperId]->msaId){
                    $paperRef->citationId = $papersFound[$countPaperId]->id;
                }else{
                    foreach($papersFound as $paperFound){
                        if($paperRef->msaCitationId == $paperFound->msaId){
                            $paperRef->citationId = $paperFound->id;
                        }
                    }
                }
                if(isset($paperRef->citationId)){
                    print("REF Paper id set to[".$paperRef->msaPaperId."]: ".$paperRef->citationId."\n");
                }else{
                    $msaIdX = $paperRef->msaPaperId;
                    $paperFoundIdx = $papersFound[$countPaperId]->id;
                    print("Problem seting id for ".$msaIdX."  paperFoundId[".$countPaperId."]: ".$paperFoundIdx."   In the array(".count($papersFound)."): ");
                    foreach($papersFound as $paperFound){
                        print($paperFound->id.', ');
                    }
                    print("\n");
                }
                
                $countPaperId++;
            }
            $countPaperRef++;
        }
    }
    
    private function searchPapersByIds($batchCount, &$paperRefs, $papersIds){
        $papersFound = array();
        $jsonResults = $this->queryMsa->searchPapers($papersIds);
        if(isset($jsonResults)){
            $papersFound = $this->jsonToObj->toPapers($jsonResults);
            $this->savePapers($papersFound);
            $this->setPaperIdToAuthorPaperArray($batchCount, $paperRefs, $papersFound);
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
        print("------------- retrievePapersForReferences --------------");
        foreach($paperRefs as $paperRef){
            $foundIdForMsaId = $paperDao->findIdByMsaId($paperRef->msaCitationId);
            if(isset($foundIdForMsaId) == false){
                $flush = false;
                $papersIds[$count] = $paperRef->msaPaperId;
                if($count >= QueryMsa::CITATIONS_PER_REQUEST){
                    // Calls method searchPapersByIds initially designed for 
                    // author papers but this time using paper references as
                    // both share the same attributes used in this method
                    
                    // The returned papers are already stored to the DB
                    // Remove duplicates
                    print("\n\n dump ids \n");
                    var_dump($papersIds);
                    $papersIds = array_unique($papersIds, SORT_NUMERIC);
                    print("\n\n dump ids without duplicates \n");
                    var_dump($papersIds);
                    print("\n\n");
                    $papersFound = $this->searchPapersByIds($batchCount, $paperRefs, $papersIds);
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
        if($flush == false){
        // The returned papers are already stored to the DB
            $papersFound = $this->searchPapersByIds($batchCount, $paperRefs, $papersIds);
            $papers = $papers + $papersFound;
        }
        // The returned papers are already stored to the DB
        return $papers;
    }
}
