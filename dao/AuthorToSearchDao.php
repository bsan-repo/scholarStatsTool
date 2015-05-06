<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AuthorToSearch
 *
 * @author 6opC4C3
 */

include_once('dataObject/AuthorToSearch.php');

class AuthorToSearchDao {
    
    public function addNewAuthorsUsingNames($authorNameList){
        try {
            $db = new PDO('mysql:host=127.0.0.1;port=8889;dbname=academic;charset=utf8', 'root', 'root');
            foreach($authorNameList as $authorName){
                $stmt = $db->prepare('insert into author_to_search(name) values(?)');
                $affectedRows = $stmt->execute(array($authorName));
                $stmt->closeCursor();
                $stmt = null;
                print('Inserted author to search ['.$authorName.']: '.$affectedRows."\n");
            }
        } catch(PDOException $ex) {
            echo "DB Exception (AuthorToSearchDao): ".$ex->getMessage();
        }finally{
            $db = null;
        }
    }
    
    public function findAuthorsToProcess(){
        $authors = array();
        $index = 0;
        try {
            $db = new PDO('mysql:host=127.0.0.1;port=8889;dbname=academic;charset=utf8', 'root', 'root');
            
            $stmt = $db->query('select id, name, processed from author_to_search where processed=\'false\'');
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt = null;
            
            foreach ($results as $result){
                $authorToSearch = new AuthorToSearch();
                $authorToSearch->id = $result['id'];
                $authorToSearch->name = $result['name'];
                $authorToSearch->processed = $result['processed'];
                $authors[$index++] = $authorToSearch;
            }
            
        } catch(PDOException $ex) {
            echo "DB Exception: ".$ex->getMessage();
        }finally{
            $db = null;
        }
        return $authors;
    }
    
    public function findAllAuthors(){
        $authors = null;
        try {
            $db = new PDO('mysql:host=127.0.0.1;port=8889;dbname=academic;charset=utf8', 'root', 'root');
            
            $stmt = $db->query('select id, name, processed from author_to_search');
            $stmt->execute();
            $authors = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt = null;
            
        } catch(PDOException $ex) {
            echo "DB Exception: ".$ex->getMessage();
        }finally{
            $db = null;
        }
        return $authors;
    }
    
    public function setAuthorAsProcessed($authorId){
        $authors = null;
        try {
            $db = new PDO('mysql:host=127.0.0.1;port=8889;dbname=academic;charset=utf8', 'root', 'root');
            
            $stmt = $db->prepare('UPDATE author_to_search SET processed=\'true\' WHERE id=?');
            $affectedRows = $stmt->execute(array($authorId));
            $stmt->closeCursor();
            $stmt = null;
            print('Updated author to search ['.$authorId.']: '.$affectedRows."\n");
        } catch(PDOException $ex) {
            echo "DB Exception: ".$ex->getMessage();
        }finally{
            $db = null;
        }
        return $authors;
    }
}
