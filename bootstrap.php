<?php
//ini_set(default_charset, "utf-8");
define('FIRSTKEY','mHWEcxTwCNPSl1Ul5YuPjA6p2uswTpA3CI5jjbE/4yU=');
define('SECONDKEY','KGRne+mLxVR29uCBIAOdYLRbnr/MIfGEiP+HRJ5SQdFgatgvuq4Nm/OmA73H5sniB4XJLNMzFoS/41eEgfVRYA==');
require __DIR__.DIRECTORY_SEPARATOR."vendor".DIRECTORY_SEPARATOR."autoload.php";

use app\classes\logger;

$environment = 'develop';

$whoops = new \Whoops\Run;
if ($environment !== 'production') {
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
} else {
    $whoops->pushHandler(function($e){
        Logger::error($e->getMessage());
    });
}
$whoops->register();
?>
