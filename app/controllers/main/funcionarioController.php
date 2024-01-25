<?php 
namespace app\controllers\main;
use app\classes\head;
use app\classes\login;
use app\classes\form;
use app\classes\elements;
use app\classes\controllerAbstract;
use app\classes\footer;
use app\classes\functions;
use app\models\main\loginModel;
use app\models\main\usuarioModel;
use app\controllers\main\cadastroController;

class funcionarioController extends controllerAbstract{


    public function index(){
    
        $funcionarioController = new cadastroController;

        $funcionarioController->index([functions::encrypt(2)]);
        
    }

}