<?php
require __DIR__.DIRECTORY_SEPARATOR."config.php";
require str_replace(DIRECTORY_SEPARATOR."app".DIRECTORY_SEPARATOR."db","",__DIR__.DIRECTORY_SEPARATOR."vendor".DIRECTORY_SEPARATOR."autoload.php");

use app\db\transactionManeger;

$recreate = $argv[1];

try{

transactionManeger::init();
transactionManeger::beginTransaction();



transactionManeger::commit();

}
catch(Exception $e){
        echo $e->getMessage();
        transactionManeger::rollBack();
}

?>