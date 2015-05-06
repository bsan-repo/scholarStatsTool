<?php

//
// TODO: 
// - Complete test for extracting the data (ExtractAcademicsData class)
// - Refactor this file, determine the order of executing including paths
//   for normal execution, to load the authors list and any other
// - Add functionality to query iterativelly all the authors papers and 
//   all the papers references (currently 1 request with expected 100 max
//   elements (req. test))
// - Create dump of era records and create script to load it to the DB
// - Add functionality to retrieve all the required papers for the references,
//   all the journals and conferences for the papers and the affiliations for 
//   the authors. The criteria is doing a query to the DB to retrieve those 
//   which do not have a connection to the other table (null on foreign key).
//   - Check TODOs are solved or not completelly necessary.
//

include_once 'Test.php';

function main(){
    //$populateAuthorList = new PopulateAuthorList();
    //$populateAuthorList->populate();
    //$populateAuthorList->showAuthorsInDb();
    /*$authors = (new ExtractAcademicsData())->searchAuthorByName('Kewen Wang');
    foreach($authors as $author){
        print('> '.$author->name."  id_msa".$author->details->msaId."\n");
    }
    */
    /*
    
    $authors = (new PopulateAuthorList())->getAuthorsInDbToProcess();
    
    foreach($authors as $author){
        print('> '.$author->name."\n");
    }
    
    $a = array();
    $a[0] = 0;
    $a[1] = 1;
    $a['two'] = '2';
    $a[3] = new Author();
    $a[3]->name = "name t";
    $a[3]->id = 1;
    foreach($a as $k=>$v){
        print("> [".$k."]=".serialize($v)."\n");
    }
     * 
     */
    (new Test())->testExtractMsaData();
    
}

main();

?>