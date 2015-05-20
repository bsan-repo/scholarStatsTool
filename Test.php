<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once('dao/AuthorDao.php');
include_once('dao/AuthorDetailsDao.php');
include_once('dataobject/Paper.php');
include_once('dao/PaperDao.php');
include_once('dataobject/AuthorPaper.php');
include_once('dao/AuthorPaperDao.php');
include_once('dataobject/PaperReference.php');
include_once('dao/PaperReferenceDao.php');
include_once('dataobject/Affiliation.php');
include_once('dao/AffiliationDao.php');
include_once('dataobject/Conference.php');
include_once('dao/ConferenceDao.php');
include_once('dataobject/Journal.php');
include_once('dao/JournalDao.php');
include_once('dataobject/ConfigParameter.php');
include_once('dao/ConfigParameterDao.php');
include_once('JsonToObject.php');
include_once 'dao/AuthorToSearchDao.php';

include_once("QueryMsa.php");
include_once("ExtractAcademicsData.php");

/**
 * Description of Test
 *
 * @author 6opC4C3
 */
class Test {
    public function deleteRecord($tableName, $recordId){
        try {
            $db = new PDO('mysql:host=127.0.0.1;port=8889;dbname=academic;charset=utf8', 'root', 'root');
            
            $stmt = $db->prepare('DELETE FROM '.$tableName.' WHERE id=?');
            $affectedRows = $stmt->execute(array($recordId));
            $stmt->closeCursor();
            $stmt = null;
            print('Deleted from '.$tableName.'['.$recordId.']: '.$affectedRows."\n");
        } catch(PDOException $ex) {
            echo "DB Exception: ".$ex->getMessage();
        }finally{
            $db = null;
        }     
    }
    public function deleteAllRecords($tableName){
        try {
            $db = new PDO('mysql:host=127.0.0.1;port=8889;dbname=academic;charset=utf8', 'root', 'root');
            
            $stmt = $db->prepare('DELETE FROM '.$tableName);
            $affectedRows = $stmt->execute();
            $stmt->closeCursor();
            $stmt = null;
            print('Deleted from '.$tableName.': '.$affectedRows."\n");
        } catch(PDOException $ex) {
            echo "DB Exception: ".$ex->getMessage();
        }finally{
            $db = null;
        }     
    }
    
    function retrieveRecord($tableName, $recordId){
        try {
            $db = new PDO('mysql:host=127.0.0.1;port=8889;dbname=academic;charset=utf8', 'root', 'root');
            
            $stmt = $db->prepare('select * from '.$tableName.' where id=?');
            $stmt->execute(array($recordId));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt = null;
            
            if (isset($result)){
                print($tableName.'['.$recordId."]:\n");
                var_dump($result);
            }
            
        } catch(PDOException $ex) {
            echo "DB Exception: ".$ex->getMessage();
        }finally{
            $db = null;
        }
    }
    
    function testDao(){
        $authorDetails = new AuthorDetails();
        $authorDetails->msaId = 1;
        $authorDetails->affiliation = "Affiliation 0";
        $authorDetails->homepageUrl = 'www.homepage 0';
        $authorDetails->msaAffiliationId = 1;
        $authorDetails->researchInterest = 'research interests';
        $authorDetails->version = 2;
        
        (new AuthorDetailsDao())->insert($authorDetails);
        
        $result = $this->retrieveRecord('author_details', $authorDetails->id);
        print($result);
        
        $author = new Author();
        $author->name = 'Aame 0';
        $author->details = 'details 0';
        $author->detailsId = $authorDetails->id;
        
        (new AuthorDao())->insert($author);
        $this->retrieveRecord('author', $author->id);
        
        $paper = new Paper();
        $paper->msaId = 1;
        $paper->conferenceId = null;
        $paper->journalId = null;
        $paper->title = 'title 1';
        $paper->keyword = 'key 1';
        $paper->msaConferenceId = 1;
        $paper->msaJournalId = null;
        $paper->year = 2000;
        
        (new PaperDao())->insert($paper);
        $this->retrieveRecord('paper', $paper->id);
        
        $authorPaper = new AuthorPaper();
        $authorPaper->authorId = $author->id;
        $authorPaper->paperId = $paper->id;
        $authorPaper->msaAuthorId = 1;
        $authorPaper->msaPaperId = 1;
        $authorPaper->msaSeqId = 1;
        
        (new AuthorPaperDao())->insert($authorPaper);
        $this->retrieveRecord('author_paper', $authorPaper->id);
        
        $paperRef = new PaperReference();
        $paperRef->paperId = $paper->id;
        $paperRef->citationId = $paper->id;
        $paperRef->msaCitationId = '1';
        $paperRef->msaPaperId = '1';
        $paperRef->msaSeqId = '1';
        
        (new PaperReferenceDao())->insert($paperRef);
        $this->retrieveRecord('paper_ref', $paperRef->id);
        
        $affiliation = new Affiliation();
        $affiliation->msaId = 1;
        $affiliation->homepage = 'www.home 1';
        $affiliation->officialName = 'official name 1';
        $affiliation->latitude = 1.0;
        $affiliation->longitude = 12.34;
        
        (new AffiliationDao())->insert($affiliation);
        $this->retrieveRecord('affiliation', $affiliation->id);
        
        $conference = new Conference();
        $conference->fullname = 'conf 1';
        $conference->homepage = 'home ';
        $conference->eraEntry = null;
        $conference->msaId = 1;
        
        (new ConferenceDao())->insert($conference);
        $this->retrieveRecord('conference', $conference->id);
        
        $journal = new Journal();
        $journal->fullname = 'journal 1';
        $journal->homepage = 'home ';
        $journal->eraEntry = null;
        $journal->msaId = 2;
        
        (new JournalDao())->insert($journal);
        $this->retrieveRecord('journal', $journal->id);
        
        $param = new ConfigParameter();
        $param->name = 'record_offset';
        $param->value = 300;
        
        $paramDao = new ConfigParameterDao();
        $paramDao->insert($param);
        $this->retrieveRecord('config_parameter', $param->id);
        // update
        $param->value = 500;
        $paramDao->update($param);
        $this->retrieveRecord('config_parameter', $param->id);
        // retrieve 
        $paramRetrieved = $paramDao->findConfigParameterByName('record_offset');
        print('Value for [record_offset]:'.$paramRetrieved->value."\n\n");
        
        $this->deleteAllRecords('config_parameter');
        $this->deleteAllRecords('conference');
        $this->deleteAllRecords('journal');
        $this->deleteAllRecords('affiliation');
        $this->deleteAllRecords('paper_ref');
        $this->deleteAllRecords('author_paper');
        $this->deleteAllRecords('paper');
        $this->deleteAllRecords('author_details');
        $this->deleteAllRecords('author');
    }
    
    public function testImportAuthorList(){
        // Import authors 
        (new PopulateAuthorList())->populate('datafeed/griffithAuthorList.csv');
        // Query authors to process
        $authorsToSearch = (new AuthorToSearchDao())->findAuthorsToProcess();
        var_dump($authorsToSearch);
        print('Lenght: '.count($authorsToSearch)."\n");
        $this->deleteAllRecords('authors_to_search');
    }
    
    public function testQueryMsaAndJsonToObj(){
        
        $queryMsa = new QueryMsa();
        $jsonToObj = new JsonToObject();
        
        /////////////////////////////////////////////////////////////
        /////////////////// TEST RETRIVING AUTHOR ///////////////////
        /////////////////////////////////////////////////////////////
        // Retrieve the data from msa
        $jsonResults = $queryMsa->searchAuthorByName('David M. Lorber');
        print("Author search msa:\n");
        var_dump($jsonResults);
        print("\n");
        
        // Convert the json data to objects
        $authors = $jsonToObj->toAuthors($jsonResults);
        $counter = 0;
        foreach($authors as $author){
            print("Author[".$counter++."]:\n");
            var_dump($author);
            print("\n");
        }
        
        /////////////////////////////////////////////////////////////
        /////////////// TEST RETRIVING AUTHOR PAPERS ////////////////
        /////////////////////////////////////////////////////////////
        // Request the author papers data
        if(isset($authors[0]) == false){
            print("No authors were retrieved - FINISHING TEST");
        }
           
        $jsonResults = $queryMsa->searchAuthorPapers($authors[0]->details->msaId);
        print("Author papers search msa:\n");
        var_dump($jsonResults);
        print("\n");
        
        // Convert the json data to objects
        $authorPapers = $jsonToObj->toAuthorPapers($jsonResults);
        $counter = 0;
        foreach($authorPapers as $authorPaper){
            print("Author paper[".$counter++."]:\n");
            var_dump($authorPaper);
            print("\n");
        }
        
        /////////////////////////////////////////////////////////////
        /////////////////// TEST RETRIVING PAPERS ///////////////////
        /////////////////////////////////////////////////////////////
        // Request the paper data
        if(isset($authorPapers[0]) == false){
            print("No author papers were retrieved - FINISHING TEST");
        }
           
        $jsonResults = $queryMsa->searchPapers(array($authorPapers[0]->msaPaperId));
        print("Paper search msa:\n");
        var_dump($jsonResults);
        print("\n");
        
        // Convert the json data to objects
        $papers = $jsonToObj->toPapers($jsonResults);
        $counter = 0;
        foreach($papers as $paper){
            print("Paper[".$counter++."]:\n");
            var_dump($paper);
            print("\n");
        }
        /////////////////////////////////////////////////////////////
        //////////////// TEST RETRIVING PAPER REFS //////////////////
        /////////////////////////////////////////////////////////////
        // Request the paper data
        if(isset($authorPapers[0]) == false){
            print("No author papers were retrieved - FINISHING TEST");
        }
           
        $jsonResults = $queryMsa->searchPaperReferences($authorPapers[0]->msaPaperId);
        print("Paper references search msa:\n");
        var_dump($jsonResults);
        print("\n");
        
        // Convert the json data to objects
        $paperRefs = $jsonToObj->toPaperReferences($jsonResults);
        $counter = 0;
        foreach($paperRefs as $paperRef){
            print("Paper REF[".$counter++."]:\n");
            var_dump($paperRef);
            print("\n");
        }
    }
    
    public function testExtractMsaData(){
        $extractAcademicsData = new ExtractAcademicsData();
        
        // Set author to search
        $this->deleteAllRecords('author_to_search');
        (new AuthorToSearchDao())->addNewAuthorsUsingNames(array('David M. Lorber'));
        
        $extractAcademicsData->processBatch();
    }
    
    public function testAuthorPapersForMoreOf100Records(){
        $author = new Author();
        $author->details = new AuthorDetails();
        $author->details->msaId  = '848503';
        (new AuthorDetailsDao())->insert($author->details);
        $author->detailsId = $author->details->id;
        $author->name = "Test author";
        (new AuthorDao())->insert($author);
        (new ExtractAcademicsData())->searchAuthorPapers($author->details->msaId, $author->id);
    }
    
    public function testPaperWithMoreOf100Refs(){
        $paper = new Paper();
        $paper->msaId = "217495";
        (new PaperDao())->insert($paper);
        (new ExtractAcademicsData())->searchPaperReferences($paper->msaId, $paper->id);
    }
}

/*
        $this->deleteRecord('affiliation', $affiliation->id);
        $this->deleteRecord('paper_ref', $paperRef->id);
        $this->deleteRecord('author_paper', $authorPaper->id);
        $this->deleteRecord('paper', $paper->id);
        $this->deleteRecord('author_details', $authorDetails->id);
        $this->deleteRecord('author', $author->id);
 */