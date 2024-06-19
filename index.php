<?php
    require 'bootstrap.php';

    use core\Controller;    
    use core\Method;
    use core\Parameter;
    use app\classes\functions;

    $controller = new Controller;

    $urlPermitidas = ["/ajax","/usuario/manutencao","/usuario/action","/empresa/manutencao","/empresa/action"];
    
    if (isset($_SESSION["user"]) || in_array(functions::getUri(),$urlPermitidas)){
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