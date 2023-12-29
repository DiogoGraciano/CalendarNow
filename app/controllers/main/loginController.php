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
use app\models\main\cidadeModel;

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

        $login = new login($usuario,$senha);
        $login->show();

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

        $this->go("login/cadastro/".$usuario->id);
    }

    public function cadastro($parameters){

        $head = new head();
        $head->show("Cadastro","");

        $cd = "";

        if ($parameters)
            $cd = $parameters[0];

        $dado = usuarioModel::get($cd);

        $form = new form("LOGO",$this->url."login/save");
        $elements = new elements;

        $form->setHidden("cd",$cd);
        $form->setHidden("cd_endereco",$dado->id_endereco);

        $form->setDoisInputs(
            $elements->input("nome","Nome",$dado->nome,true),
            $elements->input("cpf_cnpj","CPF/CNPJ:",$dado->cpf_cnpj,true)
        );
        $form->setDoisInputs(
            $elements->input("email","Email",$dado->email,true,false,"","email"),
            $elements->input("senha","Senha","",true,false,"","password")
        );
        $form->setDoisInputs(
            $elements->input("cep","Cep",$dado->endereco->cep,true,false,"","number","form-control",'min="80000000" max="89999999"'),
            $elements->select("Estado","id_estado",$elements->getOptions("estado","id","nome"),$dado->endereco->id_estado?:24,true)
        );
        $form->setDoisInputs(
            $elements->select("Cidade","id_cidade",cidadeModel::getOptionsbyEstado($dado->endereco->id_estado?:24),$dado->endereco->id_cidade?:4487,true),
            $elements->input("bairro","Bairro",$dado->endereco->bairro,true)
        );
        $form->setDoisInputs(
            $elements->input("rua","Rua",$dado->endereco->rua,true),
            $elements->input("numero","Numero",$dado->endereco->cep,true,false,"","number","form-control",'min="0" max="999999"'),
        );
        $form->setInputs(
            $elements->textarea("complemento","Complemento",$dado->endereco->complemento,true)
        );
      
        $form->setButton($elements->button("Salvar","btn_submit"));
        $form->setButton($elements->button("Voltar","btn_submit","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."login'"));
        $form->show();

        $footer = new footer;
        $footer->show();
    }

    public function save(){
       
        $cd = $this->getValue('cd');
        $nome = $this->getValue('nome');
        $cpf_cnpj = $this->getValue('cpf_cnpj');
        $senha = $this->getValue('senha');
        $email = $this->getValue('email');
        $cep = $this->getValue('cep');
        $id_estado = $this->getValue('id_estado');
        $id_cidade = $this->getValue('id_cidade');
        $rua = $this->getValue('rua');
        $numero = $this->getValue('numero');
        $complemento = $this->getValue('complemento');
        $id_endereco = $this->getValue('id_endereco');

        $retorno = usuarioModel::set($nome,$cpf_cnpj,$email,$senha,$cep,$id_estado,$id_cidade,$rua,$numero,$complemento,$id_endereco,$cd);

        if ($retorno)
            $this->go("login/index/".base64_encode($cpf_cnpj)."/".base64_encode($senha));
        else 
            $this->go("login/cadastro/");
    }
    
}