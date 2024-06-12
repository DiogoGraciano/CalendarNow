<?php

namespace app\controllers\main;

use app\classes\head;
use app\classes\form;
use app\classes\elements;
use app\classes\controllerAbstract;
use app\classes\consulta;
use app\classes\footer;
use app\classes\functions;
use app\classes\mensagem;
use app\db\estado;
use app\db\transactionManeger;
use app\models\main\usuarioModel;
use app\models\main\enderecoModel;
use app\models\main\cidadeModel;
use app\models\main\empresaModel;


class empresaController extends controllerAbstract {

    public function index($parameters = []){
    
        $head = new head();
        $head->show("cadastro", "consulta");

        $elements = new elements();

        $cadastro = new consulta();
        $cadastro->addButtons($elements->button("Voltar", "voltar", "button", "btn btn-primary", "location.href='".$this->url."opcoes'"));

        $cadastro->addColumns("1", "Id", "id")
                ->addColumns("10", "CPF/CNPJ", "cpf_cnpj")
                ->addColumns("15", "Nome", "nome")
                ->addColumns("15", "Email", "email")
                ->addColumns("11", "Telefone", "telefone")
                ->addColumns("14", "Ações", "acoes");

        $dados = empresaModel::getAll();

        $cadastro->show($this->url."empresa/manutencao/", $this->url."empresa/action/", $dados, "id", true);

        $footer = new footer();
        $footer->show();
    }

    public function manutencao($parameters = []){
      
        $form = new form($this->url."empresa/action");

        $head = new head();
        $head->show("Cadastro", "");

        $id = null;
        $tipo_usuario = null;

        if (array_key_exists(1, $parameters)){
            $id = intval(functions::decrypt($parameters[1])); 
        }

        if ($tipo_usuario == 1){
            $dado = usuarioModel::get($id);
            $form->setHidden("cd", $id);
            $dadoEndereco = enderecoModel::get($dado->id, "id_usuario");
            $dadoEmpresa = empresaModel::get($dado->id_empresa);
            $form->setHidden("id_empresa", $dado->id_empresa);
        }

        $elements = new elements();

        $form->setDoisInputs(
            $elements->input("nome", "Nome", $dado->nome, true),
            $elements->input("cpf_cnpj", "CPF/CNPJ:", $dado->cpf_cnpj?functions::formatCnpjCpf($dado->cpf_cnpj):"", true),
            array("nome", "cpf_cnpj")
        );

        $form->setTresInputs(
            $elements->input("email", "Email", $dado->email, true, false, "", "email"),
            $elements->input("senha", "Senha", "", true, false, "", "password"),
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
        $form->setButton($elements->button("Voltar", "voltar", "button", "btn btn-primary w-100 pt-2 btn-block", "location.href='".$this->url."empresa/index/".functions::encrypt($tipo_usuario)."'"));

        $form->show();

        $footer = new footer();
        $footer->show();
    }

    public function action($parameters = []){
       
        $id = intval($this->getValue('cd'));
        $nome = $this->getValue('nome');
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

        transactionManeger::init();
        transactionManeger::beginTransaction();

        try {
            $_id_empresa = intVal($this->getValue('id_empresa'));
            $nome_empresa = $this->getValue('nome_empresa');
            $razao = $this->getValue('razao');
            $fantasia = $this->getValue('fantasia');
            $id_empresa = empresaModel::set($nome_empresa, $cpf_cnpj, $email, $telefone, $razao, $fantasia, $_id_empresa);
            if ($id_empresa && $id_usuario = usuarioModel::set($nome, $cpf_cnpj, $email, $telefone, $senha, $id, 1, $id_empresa, false)){
                $id_endereco = enderecoModel::set($cep, $id_estado, $id_cidade, $bairro, $rua, $numero, $complemento, $id_endereco, $id_usuario, $id_empresa, false);
                if ($id_endereco){
                    mensagem::setSucesso("Usuario empresarial salvo com sucesso");
                    transactionManeger::commit();
                    $this->go("opcoes");
                }
            }
        } catch (\Exception $e) {
            mensagem::setSucesso(false);
            transactionManeger::rollback();
        }

        mensagem::setSucesso(false);
        transactionManeger::rollback();
        $this->go("opcoes");
    }
}

?>
