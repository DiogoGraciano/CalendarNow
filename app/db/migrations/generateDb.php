<?php
require __DIR__.DIRECTORY_SEPARATOR."config.php";
require str_replace(DIRECTORY_SEPARATOR."app".DIRECTORY_SEPARATOR."db","",__DIR__.DIRECTORY_SEPARATOR."vendor".DIRECTORY_SEPARATOR."autoload.php");

use app\db\transactionManeger;

$recreate = $argv[1];

try{

transactionManeger::init();
transactionManeger::beginTransaction();

$tables = scandir(dirname(dirname(__DIR__)));

$tablesFk = [];
foreach ($tables as $table) {
  $class = "app/db/tables/".$table;
  if (class_exists($class) && method_exists($class::table())) {
    $tableClass = $class::table();
    if($tableClass->hasForeingKey()){
      if($tableClass->exists()){
        $tableClass->execute();
        continue;
      }
      $tablesFk[] = $tableClass;
    }
    else {
      $tableClass->execute();
    }
  }
}

$valid = function ($tablesFkDependece){
    foreach ($tablesFkDependece as $tableFkDependece){
      $classArray = $tableFkDependece->getFkTablesClass();
      
      if (!$classArray && $tableFkDependece->exists()) {
        $tableFkDependece->execute();
      }
    }
}

if ($tablesFk) {
  foreach ($tablesFk as $tableFk) {
    $tablesFkDependece = $tableFk->getFkTablesClass();
    if (!$tablesFkDependece && !$tableFk->exists()) {
      $tableFkDependece->execute();
    }
    else {
      $valid()
    }
  }
}




transactionManeger::commit();

}
catch(Exception $e){
        echo $e->getMessage();
        transactionManeger::rollBack();
}

?>