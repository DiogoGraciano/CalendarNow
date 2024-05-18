<?php
require str_replace("\app\db","",__DIR__.DIRECTORY_SEPARATOR."vendor".DIRECTORY_SEPARATOR."autoload.php");

use app\db\tableDb;
use app\db\columnDb;

$configTb = new tableDb("config");
$configTb->addColumn((new columnDb("id","INT"))->isPrimary());
$configTb->addColumn((new columnDb("id_empresa","INT"))->isForeingKey(new tableDb("empresa"),"id"));
$configTb->addColumn((new columnDb("identificador","VARCHAR",30))->isUnique());
$configTb->addColumn((new columnDb("configuracao","BLOB"))->isNotNull());
$configTb->execute();

?>