<?php
define('FIRSTKEY','mHWEcxTwCNPSl1Ul5YuPjA6p2uswTpA3CI5jjbE/4yU=');
define('SECONDKEY','KGRne+mLxVR29uCBIAOdYLRbnr/MIfGEiP+HRJ5SQdFgatgvuq4Nm/OmA73H5sniB4XJLNMzFoS/41eEgfVRYA==');
require __DIR__.DIRECTORY_SEPARATOR."vendor".DIRECTORY_SEPARATOR."autoload.php";

$environment = 'develop';

$whoops = new \Whoops\Run;
if ($environment !== 'production') {
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
} else {
    $whoops->pushHandler(function($e){
        $e->getMessage();
    });
}
$whoops->register();
?>
