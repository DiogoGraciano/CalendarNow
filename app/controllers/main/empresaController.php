<?php

namespace app\controllers\main;

use app\view\layout\head;
use app\view\layout\form;
use app\view\layout\elements;
use app\controllers\abstract\controller;
use app\view\layout\consulta;
use app\view\layout\footer;
use app\helpers\functions;
use app\helpers\mensagem;
use app\db\tables\estado;
use app\db\transactionManeger;
use app\models\main\usuarioModel;
use app\models\main\enderecoModel;
use app\models\main\cidadeModel;
use app\models\main\empresaModel;
use app\models\main\configuracoesModel;
use core\session;

class empresaController extends controller {

    public function index($parameters = []){

        session::set("empresaController",false);
    
        $head = new head();
        $head->show("cadastro", "consulta");

        $elements = new elements();

        $cadastro = new consulta();
        $cadastro->addButtons($elements->button("Adicionar","manutencao","button","btn btn-primary","location.href='".$this->url."empresa/manutencao'"));
        $cadastro->addButtons($elements->button("Voltar", "voltar", "button", "btn btn-primary", "location.href='".$this->url."opcoes'"));

        $cadastro->addColumns("1", "Id", "id")
                ->addColumns("10", "CPF/CNPJ", "cpf_cnpj")
                ->addColumns("15", "Nome", "nome")
                ->addColumns("15", "Email", "email")
                ->addColumns("11", "Telefone", "telefone")
                ->addColumns("14", "Ações", "acoes");

        $dados = empresaModel::getAll();

        $cadastro->show($this->url."empresa/manutencao/opcoes/", $this->url."empresa/action/", $dados, "id", true);

        $footer = new footer();
        $footer->show();
    }

    public function manutencao($parameters = []){

        $id = null;
        $location = null;

        if ($parameters && array_key_exists(0, $parameters)){
            $location = $parameters[0];
        }
        if ($parameters && array_key_exists(1, $parameters)){
            $id = intval(functions::decrypt($parameters[1])); 
        }
      
        $form = new form($this->url."empresa/action/".$location?:"login");

        $head = new head();
        $head->show("Cadastro", "");

        $dado = isset(session::get("empresaController")->usuario)?session::get("empresaController")->usuario:usuarioModel::get($id);
        $form->setHidden("cd", $dado->id);

        $dadoEndereco = isset(session::get("empresaController")->endereco)?session::get("empresaController")->endereco:enderecoModel::get($dado->id, "id_usuario");
        $form->setHidden("id_endereco", $dadoEndereco->id);

        $dadoEmpresa = isset(session::get("empresaController")->empresa)?session::get("empresaController")->empresa:empresaModel::get($dado->id_empresa);
        $form->setHidden("id_empresa", $dadoEmpresa->id);

        $elements = new elements();

        $form->setDoisInputs(
            $elements->input("nome", "Nome", $dado->nome, true),
            $elements->input("cpf_cnpj", "CPF/CNPJ:", $dado->cpf_cnpj?functions::formatCnpjCpf($dado->cpf_cnpj):"", true),
            array("nome", "cpf_cnpj")
        );

        $form->setTresInputs(
            $elements->input("email", "Email", $dado->email, true, false, "", "email"),
            $elements->input("senha", "Senha", "", $dado->senha?false:true, false, "", "password"),
            $elements->input("telefone", "Telefone", functions::formatPhone($dado->telefone), true),
            array("email", "senha", "telefone")
        );

        $form->setTresInputs(
            $elements->input("nome_empresa", "Nome da Empresa", $dadoEmpresa->nome, true),
            $elements->input("fantasia", "Nome Fantasia", $dadoEmpresa->fantasia, true),
            $elements->input("razao", "Razao Social:", $dadoEmpresa->razao, true),
            array("nome_empresa", "fantasia", "razao")
        );

        $elements->setOptions(new estado(), "id", "nome");
        $estado = $elements->select("Estado", "id_estado", $dadoEndereco->id_estado ?: 24, true);

        $form->setDoisInputs(
            $elements->input("cep", "CEP", $dadoEndereco->cep, true),
            $estado,
            array("cep", "id_estado")
        );

        $cidades = cidadeModel::getByEstado($dadoEndereco->id_estado ?: 24);

        foreach ($cidades as $cidade){
            $elements->addOption($cidade->id, $cidade->nome);
        }

        $form->setDoisInputs(
            $elements->select("Cidade", "id_cidade", $dadoEndereco->id_cidade ?: 4487, true),
            $elements->input("bairro", "Bairro", $dadoEndereco->bairro, true),
            array("bairro", "id_cidade")
        );

        $form->setDoisInputs(
            $elements->input("rua", "Rua", $dadoEndereco->rua, true),
            $elements->input("numero", "Numero", $dadoEndereco->numero, true, false, "", "number", "form-control", 'min="0" max="999999"'),
            array("rua", "numero")
        );

        $form->setInputs($elements->textarea("complemento", "Complemento", $dadoEndereco->complemento, true), "complemento");

        $form->setButton($elements->button("Salvar", "submit"));
        $form->setButton($elements->button("Voltar", "voltar", "button", "btn btn-primary w-100 pt-2 btn-block", "location.href='".($this->url.$location?:"login")."'"));

        $form->show();

        $footer = new footer();
        $footer->show();
    }

    public function action($parameters = []){

        $location = null;

        if ($parameters && array_key_exists(0, $parameters)){
            $location = $parameters[0];
        }
       
        $id = intval($this->getValue('cd'));
        $_id_empresa = intval($this->getValue('id_empresa'));
        $id_endereco = intval($this->getValue('id_endereco'));
        $nome = $this->getValue('nome');
        $nome_empresa = $this->getValue('nome_empresa');
        $fantasia = $this->getValue('fantasia');
        $razao = $this->getValue('razao');
        $cpf_cnpj = $this->getValue('cpf_cnpj');
        $senha = $this->getValue('senha');
        $email = $this->getValue('email');
        $telefone = $this->getValue('telefone');
        $id_endereco = intval($this->getValue('id_endereco'));
        $cep = $this->getValue('cep');
        $id_estado = intval($this->getValue('id_estado'));
        $id_cidade = intval($this->getValue('id_cidade'));
        $bairro = $this->getValue('bairro');
        $rua = $this->getValue('rua');
        $numero = $this->getValue('numero');
        $complemento = $this->getValue('complemento');

        $usuario = new \stdClass;
        
        $usuario->usuario = new \stdClass;
        $usuario->usuario->id           = $id;
        $usuario->usuario->nome         = $nome;
        $usuario->usuario->cpf_cnpj     = $cpf_cnpj;
        $usuario->usuario->senha        = $senha;
        $usuario->usuario->email        = $email;
        $usuario->usuario->telefone     = functions::onlynumber($telefone);

        $usuario->empresa               = new \stdClass;
        $usuario->empresa->id           = $_id_empresa;
        $usuario->empresa->nome         = $nome_empresa;
        $usuario->empresa->fantasia     = $fantasia;
        $usuario->empresa->razao        = $razao;

        $usuario->endereco              = new \stdClass;
        $usuario->endereco->id          = $id_endereco;
        $usuario->endereco->cep         = $cep;
        $usuario->endereco->id_estado   = $id_estado;
        $usuario->endereco->id_cidade   = $id_cidade;
        $usuario->endereco->bairro      = $bairro;
        $usuario->endereco->rua         = $rua;
        $usuario->endereco->numero      = $numero;
        $usuario->endereco->complemento = $complemento;

        session::set("empresaController",$usuario);

        try {

            transactionManeger::init();
            transactionManeger::beginTransaction();

            $id_empresa = empresaModel::set($nome_empresa, $cpf_cnpj, $email, $telefone, $razao, $fantasia, $_id_empresa);
            if ($id_empresa && $id_usuario = usuarioModel::set($nome, $cpf_cnpj, $email, $telefone, $senha, $id, 1, $id_empresa, false)){
                $id_endereco = enderecoModel::set($cep, $id_estado, $id_cidade, $bairro, $rua, $numero, $complemento, $id_endereco, $id_usuario, $id_empresa, false);
                if ($id_endereco){

                    if(!$_id_empresa){
                        configuracoesModel::set("max_agendamento_dia",$id_empresa,2);
                        configuracoesModel::set("max_agendamento_semana",$id_empresa,3);
                        configuracoesModel::set("max_agendamento_mes",$id_empresa,3);
                        configuracoesModel::set("hora_ini",$id_empresa,"08:00");
                        configuracoesModel::set("hora_fim",$id_empresa,"18:00");
                        configuracoesModel::set("hora_almoco_ini",$id_empresa,"12:00");
                        configuracoesModel::set("hora_almoco_fim",$id_empresa,"02:00");
                    }

                    mensagem::setSucesso("Usuario empresarial salvo com sucesso");
                    session::set("empresaController",false);
                    transactionManeger::commit();
                    $this->go($location?:"login/index/".functions::encrypt($cpf_cnpj)."/".functions::encrypt($senha));
                }
            }
        } catch (\Exception $e) {
            mensagem::setErro("Erro ao Salvar Empresa Tente Novamente");
            mensagem::setSucesso(false);
            transactionManeger::rollback();
        }

        mensagem::setSucesso(false);
        transactionManeger::rollback();
        $this->go("empresa/manutencao");
    }
}

?>
