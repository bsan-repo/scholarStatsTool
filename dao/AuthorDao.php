<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AuthorDao
 *
 * @author 6opC4C3
 */
class AuthorDao {
    
    public function insert(&$author){
        try {
            $db = new PDO('mysql:host=127.0.0.1;port=8889;dbname=academic;charset=utf8', 'root', 'root');
            
            $stmt = $db->prepare('insert into author(name, details_id) values(?, ?)');
            $affectedRows = $stmt->execute(array($author->name, $author->detailsId));
            // Get the id
            $id = $db->lastInsertId('id');
            $author->id = $id;
            
            $stmt->closeCursor();
            $stmt = null;
            print('Inserted author ['.$id.']: '.$affectedRows."\n");
            
        } catch(PDOException $ex) {
            echo "DB Exception(AuthorDao): ".$ex->getMessage();
        }finally{
            $db = null;
        }
    }
}
