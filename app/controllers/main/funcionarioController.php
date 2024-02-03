<?php 
namespace app\controllers\main;
use app\classes\controllerAbstract;
use app\classes\functions;
use app\controllers\main\cadastroController;

class funcionarioController extends controllerAbstract{


    public function index(){
    
        $funcionarioController = new cadastroController;

        $funcionarioController->index([functions::encrypt(2)]);
        
    }

}