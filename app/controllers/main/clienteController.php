<?php 
namespace app\controllers\main;
use app\layout\head;
use app\layout\form;
use app\layout\consulta;
use app\controllers\abstract\controller;
use app\layout\elements;
use app\layout\footer;
use app\helpers\functions;
use app\helpers\mensagem;
use app\layout\filter;
use app\db\transactionManeger;
use app\models\main\clienteModel;
use app\models\main\usuarioModel;
use app\models\main\funcionarioModel;
use core\session;

class cliente extends controller{

    public function index()
    {
        session::set("clienteController",false);

        $user = usuarioModel::getLogged();

        $head = new head();
        $head -> show("clientes","consulta");

        $elements = new elements;

        $filter = new filter($this->url."agenda/index/");

        $filter->addbutton($elements->button("Buscar","buscar","submit","btn btn-primary pt-2"))
                ->addFilter(3,$elements->input("nome","Nome:",$this->getValue("nome")));

        if($user->tipo_usuario != 2){
            $funcionarios = funcionarioModel::getByEmpresa($user->id_empresa);

            $elements->addOption("","Todos");

            foreach ($funcionarios as $funcionario){
                $elements->addOption($funcionario->id,$funcionario->nome);
            }

            $funcionario_select = $elements->select("Funcionarios: ","funcionario",$this->getValue("funcionario"));

            $filter->addFilter(3,$funcionario_select);
        }

        $filter->show();

        $cliente = new consulta();

        $cliente->addButtons($elements->button("Voltar","voltar","button","btn btn-primary","location.href='".$this->url."opcoes'"));

        $cliente->addColumns("1","Id","id")
            ->addColumns("70","Nome","nome")
            ->addColumns("11","Ações","acoes")
            ->show($this->url."cliente/manutencao",$this->url."cliente/action/",$user->tipo_usuario == 2?clienteModel::getByUsuario($user->id):clienteModel::getByEmpresa($user->id_empresa));
      
        $footer = new footer;
        $footer->show();
    }
    public function manutencao($parameters){

        $id = "";

        $head = new head;
        $head->show("Manutenção Clientes");
        
        $form = new form($this->url."cliente/action/");

        $elements = new elements;

        if ($parameters && array_key_exists(0,$parameters)){
            $id = functions::decrypt($parameters[0]);
            $form->setHidden("cd",$parameters[0]);
        }
        
        $dado = session::get("clienteController")?:clienteModel::get($id);
        
        $form->setInputs($elements->input("nome","Nome:",$dado->nome,true));$dado = session::get("clienteController")?:clienteModel::get($id);

        $form->setButton($elements->button("Salvar","submit"));
        $form->setButton($elements->button("Voltar","voltar","button","btn btn-primary w-100 btn-block","location.href='".$this->url."agenda'"));
        $form->show();

        $footer = new footer;
        $footer->show();
    }

    public function desvincularFuncionario($parameters = []){

        $id_agenda = functions::decrypt($parameters[0] ?? '');
        $id_funcionario = functions::decrypt($parameters[1] ?? '');

        if($id_agenda && $id_funcionario){
            FuncionarioModel::detachAgendaFuncionario($id_agenda,$id_funcionario);
            $this->go("funcionario/manutencao/".$parameters[1]);
        }

        mensagem::setErro("Agenda ou Funcionario não informados");
        $this->go("funcionario");
    }

    public function action($parameters){

        if ($parameters){
            $id = functions::decrypt($parameters[0]);
            try{
                transactionManeger::init();
                transactionManeger::beginTransaction();
                clienteModel::delete($id);
                transactionManeger::commit();
            }catch (\exception $e){
                transactionManeger::rollBack();
            }
            mensagem::setSucesso("cliente deletado com sucesso");
            $this->go("cliente");
        }

        $id = intval(functions::decrypt($this->getValue('cd')));
        $nome  = $this->getValue('nome');
        $id_funcionario  = $this->getValue('funcionario');

        $cliente = new \stdClass;
    
        $cliente->id               = $id;
        $cliente->id_funcionario   = $id_funcionario;
        $cliente->nome             = $nome;

        session::set("clienteController",$cliente);

        try{
            transactionManeger::init();
            transactionManeger::beginTransaction();
            if (clienteModel::set($nome,$id_funcionario,$id)){ 
                session::set("clienteController",false);
                transactionManeger::commit();
                $this->go("cliente");
            }
        }catch (\exception $e){
            mensagem::setSucesso(false);
            transactionManeger::rollBack();
            mensagem::setErro("Erro ao cadastrar cliente, tente novamente");
        }

        mensagem::setSucesso(false);
        $this->go("cliente/manutencao/".$this->getValue('cd'));
    }
}