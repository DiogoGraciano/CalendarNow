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
use app\classes\filter;
use app\db\transactionManeger;
use app\db\estado;
use app\models\main\usuarioModel;
use app\models\main\funcionarioModel;
use app\models\main\enderecoModel;
use app\models\main\cidadeModel;
use stdClass;

class usuarioController extends controllerAbstract {

    public function index()
    {
        $this->setSessionVar("usuarioController",false);

        $id_funcionario = $this->getValue("funcionario");
        $nome = $this->getValue("nome");

        $head = new head();
        $head->show("Cadastro de Usuários");

        $elements = new elements();

        $user = usuarioModel::getLogged();

        $filter = new filter($this->url."servico/");
        $filter->addbutton($elements->button("Buscar","buscar","submit","btn btn-primary pt-2"));

        $filter->addFilter(4,$elements->input("nome","Nome:",$nome));

        $funcionarios = funcionarioModel::getByEmpresa($user->id_empresa);

        if ($funcionarios){
            $elements->addOption("","Selecione/Todos");
            foreach ($funcionarios as $funcionario){
                $elements->addOption($funcionario->id,$funcionario->nome);
            }

            $funcionarios = $elements->select("Funcionario","funcionario",$id_funcionario);

            $filter->addFilter(4,$funcionarios);
        }

        $cadastro = new consulta(true);
        $cadastro->addButtons($elements->button("Bloquear Usuario","usuarioblock","submit","btn btn-primary","location.href='".$this->url."usuario/bloquear'")); 

        $cadastro->addButtons($elements->button("Adicionar Usuário", "adicionar", "button", "btn btn-primary", "location.href='".$this->url."usuario/manutencao'"));
        $cadastro->addColumns("1", "Id", "id")
                 ->addColumns("10", "CPF", "cpf")
                 ->addColumns("15", "Nome", "nome")
                 ->addColumns("15", "Email", "email")
                 ->addColumns("11", "Telefone", "telefone")
                 ->addColumns("14", "Ações", "acoes");

        $dados = usuarioModel::getByEmpresa($user->id_empresa);

        $cadastro->show($this->url."usuario/manutencao/opcoes/", $this->url."usuario/action/opcoes/", $dados, "id");

        $footer = new footer();
        $footer->show();
    }

    public function bloquear($parameters = []){
        try{

            transactionManeger::init();

            transactionManeger::beginTransaction();

            $qtd_list = $this->getValue("qtd_list");

            $user = usuarioModel::getLogged();

            $mensagem = "Usuarios bloqueados com sucesso: ";
            $mensagem_erro = "Usuarios não bloqueados: ";

            if ($qtd_list){
                for ($i = 1; $i <= $qtd_list; $i++) {
                    if($id_usuario = $this->getValue("id_check_".$i)){
                        if(usuarioModel::setBloqueio($id_usuario,$user->id_empresa))
                            $mensagem .= $id_usuario." - ";
                        else
                            $mensagem_erro .= $id_usuario." - ";
                    }
                }
            }
            else{
                mensagem::setErro("Não foi possivel encontrar o numero total de usuarios");
            }

        }catch(\Exception $e){
            mensagem::setSucesso(false);
            mensagem::setErro("Erro inesperado ocorreu, tente novamente");
            transactionManeger::rollback();
        }

        $this->go("usuario");
    }

    public function manutencao($parameters = []){

        $id = null;
        $location = null;

        if ($parameters && array_key_exists(0, $parameters)){
            $location = $parameters[0];
            if (array_key_exists(1, $parameters)){
                $id = intval(functions::decrypt($parameters[1])); 
            }
        }
    
        $form = new form($this->url."usuario/action/".$location?:"");

        $head = new head();
        $head->show("Cadastro de Usuário");

        $dado = isset($this->getSessionVar("usuarioController")->usuario)?$this->getSessionVar("usuarioController")->usuario:usuarioModel::get($id);
        $dadoEndereco = isset($this->getSessionVar("usuarioController")->endereco)?$this->getSessionVar("usuarioController")->endereco:enderecoModel::get($dado->id, "id_usuario");

        $elements = new elements();

        $form->setHidden("cd",$dado->id);
        $form->setHidden("id_endereco",$dadoEndereco->id);

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
        $form->setButton($elements->button("Voltar", "voltar", "button", "btn btn-primary w-100 pt-2 btn-block", "location.href='".($this->url.$location?:"login")."'"));

        $form->show();

        $footer = new footer();
        $footer->show();
    }

    public function action($parameters = []){
        $location = "";

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

        $usuario = new stdClass;
        
        $usuario->usuario = new stdClass;
        $usuario->usuario->id           = $id;
        $usuario->usuario->nome         = $nome;
        $usuario->usuario->cpf_cnpj     = $cpf_cnpj;
        $usuario->usuario->senha        = $senha;
        $usuario->usuario->email        = $email;
        $usuario->usuario->telefone     = functions::onlynumber($telefone);

        $usuario->endereco              = new stdClass;
        $usuario->endereco->id          = $id_endereco;
        $usuario->endereco->cep         = $cep;
        $usuario->endereco->id_estado   = $id_estado;
        $usuario->endereco->id_cidade   = $id_cidade;
        $usuario->endereco->bairro      = $bairro;
        $usuario->endereco->rua         = $rua;
        $usuario->endereco->numero      = $numero;
        $usuario->endereco->complemento = $complemento;

        $this->setSessionVar("usuarioController",$usuario);

        if ($parameters && array_key_exists(0, $parameters)){
            $location = $parameters[0];
        }
        if (array_key_exists(1, $parameters)){
            $id = intval(functions::decrypt($parameters[1])); 
        }

        transactionManeger::init();
        transactionManeger::beginTransaction();

        try {
            $id_usuario = usuarioModel::set($nome, $cpf_cnpj, $email, $telefone, $senha, $id, 3);
            if ($id_usuario){
                $id_endereco = enderecoModel::set($cep, $id_estado, $id_cidade, $bairro, $rua, $numero, $complemento, $id_endereco, $id_usuario, null, false);
                if ($id_endereco){
                    mensagem::setSucesso("Usuário salvo com sucesso");
                    $this->setSessionVar("usuarioController",false);
                    transactionManeger::commit();
                    $this->go($location?:"login/index/".functions::encrypt($cpf_cnpj)."/".functions::encrypt($senha));
                }
            }
        } catch (\Exception $e) {
            mensagem::setErro("Erro ao salvar usuário");
            transactionManeger::rollback();
            $this->go("usuario/manutencao/".$location);
        }

        mensagem::setSucesso(false);
        transactionManeger::rollback();
        $this->go("usuario/manutencao/".$location);
    }
}

?>