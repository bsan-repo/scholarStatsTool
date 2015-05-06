<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Example
 *
 * @author 6opC4C3
 */
class Example {
    //put your code here
    private  $number;
    public $state;
    
    function __construct() {
        // init;
        $this->number = 0;
        $this->state = false;
    }
    
    function getNumber(){
        return $this->number;
    }
    
    function setNumberToRef(&$refNum){
        $refNum = $this->number;
    }
    
    function setNumber($num){
        $this->number = $num;
    }
    
    function __get($name) {
        return $this->$name;
    }
    
    function __set($name, $value) {
        $this->$name = $value;
    }
    
    private function processNum(){
        $this->number = $this->number * $this->number;
    }
}

function test(){
    print("In test function\n");
    $example = new Example();
    $example->state = false;
    $example->setNumber(4);
    $refNum = 0;
    $example->setNumberToRef($refNum);
    print("Num: ".$refNum);
    if($example instanceof Example){
        print("\nInstance of Ext    ractAcademicsData\n");
    }
    
    
    $a = array(1, 2, 3, 4, 5);
    
    foreach($a as $n){
        print("\nfe> ".$n);
    }
    print("\n");
    
    for($i = 0; $i < 5; $i++){
        print("\nfc> ".$a[$i]);
    }
    print("\n");
    
    $xA = array('one'=>'1 num', 'two'=>'2 num');
    foreach ($xA as $k=>$v){
        print("\nfe[".$k."]: ".$v);
    }
    print("\n");
}

