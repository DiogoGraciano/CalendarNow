<?php

namespace app\controllers\main;

use app\classes\head;
use app\classes\form;
use app\classes\elements;
use app\classes\functions;
use app\classes\controllerAbstract;
use app\classes\footer;
use app\classes\consulta;
use app\classes\mensagem;
use app\db\transactionManeger;
use app\db\estado;
use app\models\main\usuarioModel;
use app\models\main\enderecoModel;
use app\models\main\cidadeModel;

class usuarioController extends controllerAbstract {

    public function index(){
        $head = new head();
        $head->show("Cadastro de Usuários");

        $elements = new elements();

        $user = usuarioModel::getLogged();

        $cadastro = new consulta();
        $cadastro->addButtons($elements->button("Adicionar Usuário", "adicionar", "button", "btn btn-primary", "location.href='".$this->url."usuario/manutencao'"));
        $cadastro->addColumns("1", "Id", "id")
                 ->addColumns("10", "CPF", "cpf")
                 ->addColumns("15", "Nome", "nome")
                 ->addColumns("15", "Email", "email")
                 ->addColumns("11", "Telefone", "telefone")
                 ->addColumns("14", "Ações", "acoes");

        $dados = usuarioModel::getByEmpresa($user->id_empresa);

        $cadastro->show($this->url."usuario/manutencao/", $this->url."usuario/action/", $dados, "id", true);

        $footer = new footer();
        $footer->show();
    }

    public function manutencao($parameters = []){
        $form = new form($this->url."usuario/action");

        $head = new head();
        $head->show("Cadastro de Usuário");

        $id = null;

        if (array_key_exists(0, $parameters)){
            $id = intval(functions::decrypt($parameters[0])); 
        }

        $dado = usuarioModel::get($id);
        $dadoEndereco = enderecoModel::get($dado->id, "id_usuario");

        $elements = new elements();

        $form->setDoisInputs(
            $elements->input("nome", "Nome", $dado->nome, true),
            $elements->input("cpf_cnpj", "CPF/CNPJ", functions::formatCnpjCpf($dado->cpf_cnpj), true),
            array("nome", "cpf_cnpj")
        );

        $form->setTresInputs(
            $elements->input("email", "Email", $dado->email, true, false, "", "email"),
            $elements->input("senha", "Senha", "", true, false, "", "password"),
            $elements->input("telefone", "Telefone", functions::formatPhone($dado->telefone), true),
            array("email", "senha", "telefone")
        );

        $elements->setOptions(new estado, "id", "nome");
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
            $elements->input("numero", "Número", $dadoEndereco->numero, true, false, "", "number", "form-control", 'min="0" max="999999"'),
            array("rua", "numero")
        );

        $form->setInputs($elements->textarea("complemento", "Complemento", $dadoEndereco->complemento, true), "complemento");

        $form->setButton($elements->button("Salvar", "submit"));
        $form->setButton($elements->button("Voltar", "voltar", "button", "btn btn-primary w-100 pt-2 btn-block", "location.href='".$this->url."usuario/index'"));

        $form->show();

        $footer = new footer();
        $footer->show();
    }

    public function action(){
        $id = intval($this->getValue('cd'));
        $nome = $this->getValue('nome');
        $cpf = $this->getValue('cpf');
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
            $id_usuario = usuarioModel::set($nome, $cpf, $email, $telefone, $senha, $id, 1);
            if ($id_usuario){
                $id_endereco = enderecoModel::set($cep, $id_estado, $id_cidade, $bairro, $rua, $numero, $complemento, $id_endereco, $id_usuario, null);
                if ($id_endereco){
                    mensagem::setSucesso("Usuário salvo com sucesso");
                    transactionManeger::commit();
                    $this->go("usuario/index");
                }
            }
        } catch (\Exception $e) {
            mensagem::setErro("Erro ao salvar usuário");
            transactionManeger::rollback();
        }

        mensagem::setErro("Erro ao salvar usuário");
        transactionManeger::rollback();

        $this->go("usuario/index");
    }
}

?>