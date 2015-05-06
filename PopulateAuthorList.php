<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once('dataObject/AuthorToSearch.php');

/**
 * Description of PopulateAuthorList
 *
 * @author 6opC4C3
 */
class PopulateAuthorList {
    public function populate($path){
        $file = fopen($path, 'r');
        if(isset($file)){
            while($line = fgetcsv($file, 100, ',')){
                (new AuthorToSearchDao())->addNewAuthorsUsingNames($line);
            }
            fclose($file);
        }
    }
}
