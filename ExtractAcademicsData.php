<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExtractAcademicsData
 *
 * @author 6opC4C3
 */

include_once 'PopulateAuthorlist.php';
include_once 'dataObject/Author.php';
include_once 'dataObject/AuthorDetails.php';
include_once 'QueryMsa.php';
include_once 'JsonToObject.php';

class ExtractAcademicsData {
    const AUTHOR_BATCH_NUMBER = 50;
    private $queryMsa;
    private $jsonToObj;
    
    public function __construct(){
        $this->queryMsa = new QueryMsa();
        $this->jsonToObj = new JsonToObject();
    }
    
    // using the name of an author search for it in ms academics and
    // return the extracted data as an array of Author objects with 
    // the results for the name
    private function searchAuthorByName($name){
        $authors = array();
        $jsonResults = $this->queryMsa->searchAuthorByName($name);
        if(isset($jsonResults)){
            $authors = $this->jsonToObj->toAuthors($jsonResults);
        }
        return $authors;
    }
    
    // Saves both author an his details using the details object contained 
    // in author.
    private function saveAuthorsToDB($authorResults){
        $authorDao = new AuthorDao();
        $authorDetailsDao = new AuthorDetailsDao();
        foreach($authorResults as $authorResult){
            $authorDetailsDao->insert($authorResult->details);
            $authorResult->detailsId = $authorResult->details->id;
            $authorDao->insert($authorResult);
        }
    }
    
    private function searchAuthorPapers($authorMsaId, $authorId){
        $authorPapers = array();
        $jsonResults = $this->queryMsa->searchAuthorPapers($authorMsaId);
        if(isset($jsonResults)){
            $authorPapers = $this->jsonToObj->toAuthorPapers($jsonResults);
        }
        // Set the author id in the DB for all the found author papers
        foreach($authorPapers as $authorPaper){
            $authorPaper->authorId = $authorId;
        }
        return $authorPapers;
    }
    
    private function setPaperIdToAuthorPaperArray($batchCount, &$authorPapers, &$papers){
        // Batch count is used to determine the author offset using the 
        // current batch count and the number of citations per batch 
        // (CITATIONS_PER_REQUEST).
        $countAuthorPaper = $batchCount*QueryMsa::CITATIONS_PER_REQUEST;
        $countPaper = 0;
        // Iterate for the size of the batch without exceding the number
        // of elements in authorPapers
        for($count = 0; $count < QueryMsa::CITATIONS_PER_REQUEST, $countAuthorPaper < count($authorPapers); $count++){
            if(isset($authorPapers[$countAuthorPaper])){
                $authorPaper = $authorPapers[$countAuthorPaper];
                if(isset($papers[$countPaper]) && $authorPaper->msaPaperId == $papers[$countPaper]->msaId){
                    $authorPaper->paperId = $papers[$countPaper]->id;
                }else{
                    foreach($papers as $paper){
                        if($authorPaper->msaPaperId == $paper->msaId){
                            $authorPaper->paperId = $paper->id;
                        }
                    }
                }
                $countPaper++;
                $countAuthorPaper++;
            }
        }
    }
    
    private function searchPapersByIds($batchCount, &$authorPapers, $papersIds){
        $papersFound = array();
        $jsonResults = $this->queryMsa->searchPapers($papersIds);
        if(isset($jsonResults)){
            $papersFound = $this->jsonToObj->toPapers($jsonResults);
            $this->savePapers($papersFound);
            $this->setPaperIdToAuthorPaperArray($batchCount, $authorPapers, $papersFound);
        }
        return $papersFound;
    }
    
    private function searchPapers($authorPapers){
        $papers = array();
        $papersIds = array();
        $count = 0;
        $flush = false;
        $batchCount = 0;
        foreach($authorPapers as $authorPaper){
            $flush = false;
            $papersIds[$count] = $authorPaper->msaPaperId;
            if($count >= QueryMsa::CITATIONS_PER_REQUEST){
                $papersFound = $this->searchPapersByIds($batchCount, $authorPapers, $papersIds);
                $papers = $papers + $papersFound;
                $count = 0;
                unset($papersIds);
                $papersIds = array();
                $flush = true;
                $batchCount++;
            }else{
                $count++;
            }
        }
        if($flush == false){
            $papersFound = $this->searchPapersByIds($batchCount, $authorPapers, $papersIds);
            $papers = $papers + $papersFound;
        }
        return $papers;
    }
    
    private function saveAuthorPapers($authorPapers){
        $authorPaperDao = new AuthorPaperDao();
        foreach($authorPapers as $authorPaper){
            $authorPaperDao->insert($authorPaper);
        }
    }

    function savePapers($papers){
        $paperDao = new PaperDao();
        foreach($papers as $paper){
            $paperDao->insert($paper);
        }
    }

    function savePaperReferences($paperRefs){
        $paperRefDao = new PaperReferenceDao();
        foreach($paperRefs as $paperRef){
            $paperRefDao->insert($paperRef);
        }
    }

    // TODO Verify the structure of the data in azure for the src and dst 
    // corresponds with the paper and citation structure in this app
    private function searchPaperReferences($paperMsaId, $paperId){
        $paperRefs = array();
        $jsonResults = $this->queryMsa->searchPaperReferences($paperMsaId);
        if(isset($jsonResults)){
            $paperRefs = $this->jsonToObj->toPaperReferences($jsonResults);
        }
        // Set the author id in the DB for all the found author papers
        foreach($paperRefs as $paperRef){
            $paperRef->paperId = $paperId;
        }
        return $paperRefs;
    }
    
    // Retreive the papers for the references (citation) and stores them to the DB
    // The id of the paper is added to the reference -> citation
    public function retrievePapersForReferences(&$paperRefs){
        $paperDao = new PaperDao();
        $queryMsa = new QueryMsa();
        $paperIds = array();
        $papers = array();
        
        foreach($paperRefs as $paperRef){
            if($paperDao->doPaperExist($paperRef->$msaCitationId) == false){
                
                
                
                
                
        $papersIds = array();
        $count = 0;
        $flush = false;
        $batchCount = 0;
        foreach($authorPapers as $authorPaper){
            $flush = false;
            $papersIds[$count] = $authorPaper->msaPaperId;
            if($count >= QueryMsa::CITATIONS_PER_REQUEST){
                $papersFound = $this->searchPapersByIds($batchCount, $authorPapers, $papersIds);
                $papers = $papers + $papersFound;
                $count = 0;
                unset($papersIds);
                $papersIds = array();
                $flush = true;
                $batchCount++;
            }else{
                $count++;
            }
        }
        if($flush == false){
            $papersFound = $this->searchPapersByIds($batchCount, $authorPapers, $papersIds);
            $papers = $papers + $papersFound;
        }
                
                
                
                
                
                
                $queryMsa->searchPapers($papersIds);
            }
        }
    }
    
    // Processes a batch of authors:
    // 1. Looks if there are any authors to process in the database.
    // 2. Uses the varible $numberToProcess to determine who many authors
    //    to process using the number returned from academics iteratively
    //    till completing the number determined.
    //
    // It will query academics for:
    //  Author -> 
    //      Author_Papers (Author.id) ->
    //          Papers(Author_Papers.id) -> 
    //              Paper_Ref(Papers.id)
    //              Journal(Paper.journal_id)
    //              Conference(Paper.conference_id)
    //
    public function processBatch(){
        $authorToSearchDao = new AuthorToSearchDao();
        $authorsToProcess = $authorToSearchDao->findAuthorsToProcess();
        
        // Iterate over authors to process results
        foreach($authorsToProcess as $authorToProcess){
            $authorResults = $this->searchAuthorByName($authorToProcess->name);
            $this->saveAuthorsToDB($authorResults);
            
            // Iterate over Authors results for the given name
            foreach($authorResults as $authorResult){
                $authorMsaId = $authorResult->details->msaId;
                // Get the author papers
                $authorPapers = $this->searchAuthorPapers($authorMsaId, $authorResult->id);
                
                // Get the papers data
                $papers = $this->searchPapers($authorPapers);
                
                // Save author papers and papers to the DB
                $this->saveAuthorPapers($authorPapers);
                
                // Iterate over Paper references for each one
                foreach($papers as $paper){
                    $paperRefs = $this->searchPaperReferences($paper->msaId, $paper->id);
                    
                    $this->savePaperReferences($paperRefs);
                }
            }
            $authorToSearchDao->setAuthorAsProcessed($authorToProcess->id);
        }
        // TODO Retrieve using the msa ids the papers in the citations and the journals and conferences
        // checking they are not already in the DB
    }
}