<?php
    require 'bootstrap.php';

    use app\db\migrations\migrate;

    migrate::execute(!empty($recrete));

    die;

    use core\controller;    
    use core\method;
    use core\parameter;
    use core\session;
    use app\helpers\functions;

    session::start();

    $controller = new Controller;

    $urlPermitidas = ["/ajax","/usuario/manutencao","/usuario/action/","/empresa/manutencao","/empresa/action/"];
    
    if (session::get("user") || in_array(functions::getUriPath(),$urlPermitidas)){
        $controller = $controller->load();
    }else 
        $controller = $controller->load("login");
        
    $method = new Method();
    $method = $method->load($controller);

    $parameters = new Parameter();
    $parameters = $parameters->load($controller);

    $controller->$method($parameters);

?>