<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of App
 *
 * @author 6opC4C3
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


include_once("dao/StatsDao.php");

class App {
    public $authorsToSeachCsvFilePath = 'datafeed/processingAuthors.csv';
    
    public function run(){
        
        // REGULAR AUTHOR BATCH PROCESSING
        // Load authors names in csv file to the DB
        (new PopulateAuthorList())->populate($this->authorsToSeachCsvFilePath);
        
        // process authors added
        (new ExtractAcademicsData())->processBatch();
         
        
        (new StatsDao())->calculateStats();
    }
}
