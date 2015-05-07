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
include_once 'ExtractAcademicsJournals.php';
include_once 'ExtractAcademicsConferences.php';
include_once 'ExtractAcademicsReferences.php';

class ExtractAcademicsData {
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
        $queryMorePapers = true;
        $skipOffset = 0;
        // Retrieve all the records quering by batches defined by the max number of 
        // request per query 
        while($queryMorePapers){
            $jsonResults = $this->queryMsa->searchAuthorPapers($authorMsaId, $skipOffset);
            if(isset($jsonResults)){
                $authorPapersFound = $this->jsonToObj->toAuthorPapers($jsonResults);
                $authorPapers = $authorPapersFound;
                if(count($authorPapers) < QueryMsa::MAX_RECORDS_PER_QUERY){
                    $queryMorePapers = false;
                }
            }else{
                $queryMorePapers = false;
            }
            $skipOffset += QueryMsa::MAX_RECORDS_PER_QUERY;
        }
        // Set the author id in the DB for all the found author papers
        foreach($authorPapers as $authorPaper){
            $authorPaper->authorId = $authorId;
        }
        return $authorPapers;
    }
    
    private function searchPapersByIds($batchCount, $papersIds){
        $papersFound = array();
        $jsonResults = $this->queryMsa->searchPapers($papersIds);
        if(isset($jsonResults)){
            $papersFound = $this->jsonToObj->toPapers($jsonResults);
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
            print("Paper to search msa id: ".$authorPaper->msaPaperId."\n");
        }
        foreach($authorPapers as $authorPaper){
            $flush = false;
            $papersIds[$count] = $authorPaper->msaPaperId;
            if($count >= QueryMsa::MAX_RECORDS_PER_QUERY){
                
                    print("\n\n dump ids \n");
                    var_dump($papersIds);
                    $papersIds = array_unique($papersIds, SORT_NUMERIC);
                    print("\n\n dump ids without duplicates \n");
                    var_dump($papersIds);
                    print("\n\n");
                    
                $papersFound = $this->searchPapersByIds($batchCount, $papersIds);
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
        if($flush == false && count($papersIds)> 0){
            $papersFound = $this->searchPapersByIds($batchCount, $papersIds);
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

    private function searchPaperReferences($paperMsaId, $paperId){
        $paperRefs = array();
        $queryMoreReferences = true;
        $skipOffset = 0;
        while($queryMoreReferences){
            $jsonResults = $this->queryMsa->searchPaperReferences($paperMsaId, $skipOffset);
            if(isset($jsonResults)){
                $paperRefs = $this->jsonToObj->toPaperReferences($jsonResults);
                if(count($paperRefs) < QueryMsa::MAX_RECORDS_PER_QUERY){
                    $queryMoreReferences = false;
                }
            }else{
                $queryMoreReferences = false;
            }
        }
        // Set the author id in the DB for all the found author papers
        foreach($paperRefs as $paperRef){
            $paperRef->paperId = $paperId;
        }
        return $paperRefs;
    }
    
    public function retrieveJournalsAndConferencesForPapers(){
        (new ExtractAcademicsJournals)->retrieveJournalsForPapers();
        (new ExtractAcademicsConferences)->retrieveJournalsForPapers();
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
    public function processBatch(){/*
        print("Retrieving authors to process\n");
        $authorToSearchDao = new AuthorToSearchDao();
        $authorsToProcess = $authorToSearchDao->findAuthorsToProcess();
        
        // Iterate over authors to process results
        foreach($authorsToProcess as $authorToProcess){
            $authorResults = $this->searchAuthorByName($authorToProcess->name);
            $this->saveAuthorsToDB($authorResults);
            
            print("Retrieving authors for ".$authorToProcess->name."\n");
            
            // Iterate over Authors results for the given name
            foreach($authorResults as $authorResult){
                
                print("Retrieving author papers for ".$authorResult->details->msaId."\n");
            
                $authorMsaId = $authorResult->details->msaId;
                // Get the author papers
                $authorPapers = $this->searchAuthorPapers($authorMsaId, $authorResult->id);
                
                print("Retrieving papers for ".$authorResult->details->msaId."\n");
                // Get the papers data
                $papers = $this->searchPapers($authorPapers);
                
                // Save author papers and papers to the DB
                $this->saveAuthorPapers($authorPapers);
                
                $this->savePapers($papers);
                
                // Iterate over Paper references for each one
                foreach($papers as $paper){
                    $paperRefs = $this->searchPaperReferences($paper->msaId, $paper->id);
                    
                    // Can be refactored by handling it as a case where all the ref that have 
                    // not been set with a paper id
                    $papersForRefs = (new ExtractAcademicsReferences())->retrievePapersForReferences($paperRefs);
                    $this->savePaperReferences($paperRefs);
                    
                    $this->savePapers($papersForRefs);
                }
            }
            $authorToSearchDao->setAuthorAsProcessed($authorToProcess->id);
        }*/
        $this->retrieveJournalsAndConferencesForPapers();
    }
}