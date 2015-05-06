<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ConfigParameterDao
 *
 * @author 6opC4C3
 */
class ConfigParameterDao {
    
    public function insert(&$configParameter){
        try {
            $db = new PDO('mysql:host=127.0.0.1;port=8889;dbname=academic;charset=utf8', 'root', 'root');
            
            $stmt = $db->prepare('insert into config_parameter(name, value) values(?, ?)');
            $affectedRows = $stmt->execute(array($configParameter->name, $configParameter->value));
            
            // Get the id
            $id = $db->lastInsertId('id');
            $configParameter->id = $id;
            
            $stmt->closeCursor();
            $stmt = null;
            print('Inserted param ['.$id.']: '.$affectedRows."\n");
            
        } catch(PDOException $ex) {
            echo "DB Exception(ConfigParameterDao): ".$ex->getMessage();
        }finally{
            $db = null;
        }
    }
    
    public function update(&$configParameter){
        try {
            $db = new PDO('mysql:host=127.0.0.1;port=8889;dbname=academic;charset=utf8', 'root', 'root');
            
            $stmt = $db->prepare('update config_parameter set name=?, value=?');
            $affectedRows = $stmt->execute(array($configParameter->name, $configParameter->value));
            
            $stmt->closeCursor();
            $stmt = null;
            print('Updated param ['.$configParameter->name."].\n");
            
        } catch(PDOException $ex) {
            echo "DB Exception(ConfigParameterDao): ".$ex->getMessage();
        }finally{
            $db = null;
        }
    }
    
    public function save(&$configParameter){
        if(isset($configParameter->id)){
            $this->update($configParameter);
        }else{
            $this->insert($configParameter);
        }
    }
    
    public function findConfigParameterByName($name){
        $configParam = new ConfigParameter();
        try {
            $db = new PDO('mysql:host=127.0.0.1;port=8889;dbname=academic;charset=utf8', 'root', 'root');
            
            $stmt = $db->prepare('select id, name, value from config_parameter where name=?');
            $stmt->execute(array($name));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt = null;
            
            if (isset($result)){
                $configParam->id = $result['id'];
                $configParam->name = $result['name'];
                $configParam->value = $result['value'];
            }
        } catch(PDOException $ex) {
            echo "DB Exception: ".$ex->getMessage();
        }finally{
            $db = null;
        }
        return $configParam;
    }
}
