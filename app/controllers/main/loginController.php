<?php 
namespace app\controllers\main;
use app\classes\head;
use app\classes\login;
use app\classes\form;
use app\classes\elements;
use app\classes\controllerAbstract;
use app\classes\footer;
use app\models\main\loginModel;
use app\models\main\usuarioModel;
use app\controllers\main\cadastroController;

class loginController extends controllerAbstract{


    public function index($parameters){
        $usuario= "";
        $senha = "";
        
        if ($parameters){
            $usuario = base64_decode($parameters[0]);
            $senha = base64_decode($parameters[1]);
        }
        $head = new head();
        $head->show("Login","");

        $login = new login;
        $login->show($usuario,$senha);

        $footer = new footer;
        $footer->show();
    }

    public function action(){

        $cpf_cnpj = $this->getValue('cpf_cnpj');
        $senha = $this->getValue('senha');
        $login =loginModel::login($cpf_cnpj,$senha);
    
        if ($login){
            $this->go("home");
        }else {
            $this->go("login");
        }
    }

    public function esqueci(){
        $head = new head();
        $head->show("Cadastro","");

        $form = new form("LOGO",$this->url."login/resetar");
        $elements = new elements;

        $form->setDoisInputs(
            $elements->input("cpf_cnpj","CPF/CNPJ","",true),
            $elements->input("email","Email","",true)
        );

        $form->setButton($elements->button("Resetar","btn_submit"));
        $form->setButton($elements->button("Voltar","btn_submit","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."login'"));
        $form->show();

        $footer = new footer;
        $footer->show();
    }

    public function resetar(){
        $cpf_cnpj = $this->getValue('cpf_cnpj');
        $email = $this->getValue('email');

        $usuario = usuarioModel::getByCpfEmail($cpf_cnpj,$email);

        $this->go("cadastro/manutencao/usuario/".$usuario->id);
    }

    public function cadastro($parameters = array()){

        if(!$parameters)
            $parameters = array();

        $usuarioController = new cadastroController;

        $usuarioController->manutencao($parameters,true);
        
    }

    public function save($parameters){
       
        if(!$parameters)
            $parameters = array();
        
        $usuarioController = new cadastroController;

        $usuarioController->action($parameters,true);
       
    }
    
}