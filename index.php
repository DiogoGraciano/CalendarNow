<?php
    require 'bootstrap.php';

    use core\Controller;    
    use core\Method;
    use core\Parameter;
    use app\classes\functions;

    $controller = new Controller;
    
    if (!isset($_SESSION))
        session_start();
    
    if (isset($_SESSION["user"]) || functions::getUri() == "/ajax")
        $controller = $controller->load();
    else 
        $controller = $controller->load("login");
        

    $method = new Method();
    $method = $method->load($controller);

    $parameters = new Parameter();
    $parameters = $parameters->load($controller);

    $controller->$method($parameters);

    $whoops = new \Whoops\Run;

?>