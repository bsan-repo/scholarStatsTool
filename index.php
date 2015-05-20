<?php

//
// TODO: 
// - Create dump of era records and create script to load it to the DB
//

//include_once 'Test.php';
include_once 'App.php';

function main(){
    //(new Test())->testExtractMsaData();
    (new App())->run();
}

main();

?>