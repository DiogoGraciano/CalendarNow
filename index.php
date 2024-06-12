<?php
    require 'bootstrap.php';

    use core\Controller;    
    use core\Method;
    use core\Parameter;
    use app\classes\functions;

    $controller = new Controller;
    
    if (isset($_SESSION["user"]) || functions::getUri() == "/ajax" || functions::getUri() == "/usuario/manutencao/" || functions::getUri() == "/empresa/manutencao/"){
        $controller = $controller->load();
        //session_regenerate_id(true);
    }else 
        $controller = $controller->load("login");
        
    $method = new Method();
    $method = $method->load($controller);

    $parameters = new Parameter();
    $parameters = $parameters->load($controller);

    $controller->$method($parameters);

?>