<?php 
namespace app\controllers\main;
use app\layout\head;
use app\layout\login as layoutLogin;
use app\layout\form;
use app\layout\elements;
use app\controllers\abstract\controller;
use app\layout\footer;
use app\helpers\functions;
use app\models\main\loginModel;
use app\models\main\usuarioModel;

final class loginController extends controller{

    public function index($parameters)
    {
        $usuario= "";
        $senha = "";
        
        if ($parameters){
            $usuario = functions::decrypt($parameters[0]);
            $senha = functions::decrypt($parameters[1]);
        }
        $head = new head();
        $head->show("Login","","");

        $login = new layoutLogin;
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

        $form = new form($this->url."login/resetar");
        $elements = new elements;

        $form->setDoisInputs(
            $elements->input("cpf_cnpj","CPF/CNPJ","",true),
            $elements->input("email","Email","",true)
        );

        $form->setButton($elements->button("Resetar","submit"));
        $form->setButton($elements->button("Voltar","submit","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."login'"));
        $form->show();

        $footer = new footer;
        $footer->show();
    }

    public function resetar(){
        $cpf_cnpj = $this->getValue('cpf_cnpj');
        $email = $this->getValue('email');

        $usuario = usuarioModel::getByCpfEmail($cpf_cnpj,$email);

        $this->go("usuario/manutencao/".functions::encrypt($usuario->id));
    } 
}