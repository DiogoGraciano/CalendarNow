<?php

session_set_cookie_params([
    'httponly' => true
]);

if (!isset($_SESSION))
    session_start();

define('FIRSTKEY','mHWEcxTwCNPSl1Ul5YuPjA6p2uswTpA3CI5jjbE/4yU=');
define('SECONDKEY','KGRne+mLxVR29uCBIAOdYLRbnr/MIfGEiP+HRJ5SQdFgatgvuq4Nm/OmA73H5sniB4XJLNMzFoS/41eEgfVRYA==');

require __DIR__.DIRECTORY_SEPARATOR."vendor".DIRECTORY_SEPARATOR."autoload.php";
require __DIR__.DIRECTORY_SEPARATOR."app".DIRECTORY_SEPARATOR."db".DIRECTORY_SEPARATOR."configDb.php";

?>
