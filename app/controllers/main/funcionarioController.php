<?php
namespace app\controllers\main;

use app\classes\head;
use app\classes\form;
use app\classes\elements;
use app\classes\functions;
use app\classes\controllerAbstract;
use app\classes\consulta;
use app\classes\footer;
use app\classes\filter;
use app\classes\mensagem;
use app\classes\modal;
use app\db\transactionManeger;
use app\models\main\usuarioModel;
use app\models\main\agendaModel;
use app\models\main\funcionarioModel;
use app\models\main\grupoFuncionarioModel;

class FuncionarioController extends controllerAbstract {

    public function index($parameters = []){
        $user = usuarioModel::getLogged();
       
        $head = new head();
        $head->show("Cadastro de Funcionários", "consulta");

        $elements = new elements();
        $cadastro = new consulta();
        $cadastro->addButtons($elements->button("Voltar", "voltar", "button", "btn btn-primary", "location.href='".$this->url."opcoes'"));

        $cadastro->addColumns("1", "Id", "id")
                ->addColumns("10", "CPF/CNPJ", "cpf_cnpj")
                ->addColumns("15", "Nome", "nome")
                ->addColumns("15", "Email", "email")
                ->addColumns("11", "Telefone", "telefone");

        $nome = $this->getValue("nome");
        $id_agenda = $this->getValue("agenda");
        $id_grupo_funcionarios = $this->getValue("grupo_funcionarios");

        $filter = new filter($this->url."funcionario/index/");
        $filter->addbutton($elements->button("Buscar", "buscar", "submit", "btn btn-primary pt-2"));
        $filter->addFilter(3, $elements->input("nome", "Nome:", $nome));

        $agendas = agendaModel::getByEmpresa($user->id_empresa);

        if ($agendas) {
            $modal = new modal($this->url."funcionario/massActionAgenda/", "massActionAgenda");

            $elements->addOption("", "Selecione/Todos");
            foreach ($agendas as $agenda) {
                $elements->addOption($agenda->id, $agenda->nome);
            }
            $agenda = $elements->select("Agenda:", "agenda", $id_agenda);

            $modal->setinputs($agenda);
            $modal->setButton($elements->button("Salvar", "submitModalConsulta", "button", "btn btn-primary w-100 pt-2 btn-block"));
            $modal->show();

            $filter->addFilter(3, $agenda);
        }

        $cadastro->addColumns("5", "Inicio Expediente", "hora_ini")
                ->addColumns("5", "Fim Expediente", "hora_fim")
                ->addColumns("5", "Inicio Almoço", "hora_almoco_ini")
                ->addColumns("5", "Fim Almoço", "hora_almoco_fim")
                ->addColumns("14", "Dias", "dia");

        $cadastro->addButtons($elements->button("Adicionar Agenda ao Funcionario", "openModel", "button", "btn btn-primary", "openModal('massActionAgenda')"));

        $grupo_funcionarios = grupoFuncionarioModel::getByEmpresa($user->id_empresa);

        if ($grupo_funcionarios) {
            $elements->addOption("", "Selecione/Todos");
            foreach ($grupo_funcionarios as $grupo_funcionario) {
                $elements->addOption($grupo_funcionario->id, $grupo_funcionario->nome);
            }

            $grupo_funcionario = $elements->select("Grupo Funcionario", "grupo_funcionario", $id_grupo_funcionarios);

            $modal = new modal($this->url."funcionario/massActionGrupoFuncionario/", "massActionGrupoFuncionario");

            $modal->setinputs($grupo_funcionario);
            $modal->setButton($elements->button("Salvar", "submitModalConsulta", "button", "btn btn-primary w-100 pt-2 btn-block"));
            $modal->show();

            $filter->addFilter(3, $grupo_funcionario);
            $cadastro->addButtons($elements->button("Adicionar Funcionario ao Grupo", "openModelGrupoFuncionario", "button", "btn btn-primary", "openModal('massActionGrupoFuncionario')"));
        }

        $filter->show();
        $dados = funcionarioModel::getListFuncionariosByEmpresa($user->id_empresa, $nome, intval($id_agenda), intval($id_grupo_funcionarios));

        $cadastro->addColumns("14", "Ações", "acoes");
        $cadastro->show($this->url."funcionario/manutencao/", $this->url."funcionario/action/", $dados, "id", true);

        $footer = new footer();
        $footer->show();
    }

    public function manutencao($parameters = []){
        $form = new form($this->url."funcionario/action");
        $head = new head();
        $head->show("Cadastro de Funcionário");

        $id = functions::decrypt($parameters[1] ?? '');
        $user = usuarioModel::getLogged();

        $dadoFuncionario = funcionarioModel::get($id);
        $dado = usuarioModel::get($dadoFuncionario->id_usuario);
        $form->setHidden("cd", $dado->id);
        $form->setHidden("id_funcionario", $dadoFuncionario->id);
        $form->setHidden("id_empresa", $user->id_empresa);

        $elements = new elements();
        $form->setDoisInputs(
            $elements->input("nome", "Nome", $dado->nome, true),
            $elements->input("cpf_cnpj", "CPF/CNPJ:", $dado->cpf_cnpj ? functions::formatCnpjCpf($dado->cpf_cnpj) : "", true),
            ["nome", "cpf_cnpj"]
        );

        $form->setTresInputs(
            $elements->input("email", "Email", $dado->email, true, false, "", "email"),
            $elements->input("senha", "Senha", "", true, false, "", "password"),
            $elements->input("telefone", "Telefone", functions::formatPhone($dado->telefone), true),
            ["email", "senha", "telefone"]
        );

        $grupos_funcionarios = grupoFuncionarioModel::getByEmpresa($user->id_empresa);
        foreach ($grupos_funcionarios as $grupo_funcionario) {
            $elements->addOption($grupo_funcionario->id, $grupo_funcionario->nome);
        }
        $id_grupo_funcionario = $elements->select("Grupo de Funcionarios", "id_grupo_funcionario");

        $form->setInputs($id_grupo_funcionario);

        $form->setDoisInputs(
            $elements->input("hora_ini", "Hora Inicial de Trabalho", functions::removeSecondsTime($dadoFuncionario->hora_ini ?: "08:00"), true, true, "", "time2"),
            $elements->input("hora_fim", "Hora Final de Trabalho", functions::removeSecondsTime($dadoFuncionario->hora_fim ?: "18:00"), true, true, "", "time2"),
            ["hora_ini", "hora_fim"]
        );

        $form->setDoisInputs(
            $elements->input("hora_almoco_ini", "Hora Inicial de Almoço", functions::removeSecondsTime($dadoFuncionario->hora_almoco_ini ?: "12:00"), true, true, "", "time2"),
            $elements->input("hora_almoco_fim", "Hora Final de Almoço", functions::removeSecondsTime($dadoFuncionario->hora_almoco_fim ?: "13:30"), true, true, "", "time2"),
            ["hora_almoco_ini", "hora_almoco_fim"]
        );

        $form->setInputs($elements->label("Dias de trabalho na Semana"));

        $checkDias = explode(",", $dadoFuncionario->dias ?: "");
        $diasSemana = ["dom" => "Domingo", "seg" => "Segunda", "ter" => "Terça", "qua" => "Quarta", "qui" => "Quinta", "sex" => "Sexta", "sab" => "Sábado"];

        foreach ($diasSemana as $key => $value) {
            $form->addCustomInput(2, $elements->checkbox($key, $value, false, in_array($key, $checkDias)), $key);
        }

        $form->setCustomInputs();
        $form->setButton($elements->button("Salvar", "submit"));
        $form->setButton($elements->button("Voltar", "voltar", "button", "btn btn-primary", "location.href='".$this->url."funcionario/index/".functions::encrypt($tipo_usuario)."'"));
        $form->show();
    }

    public function action(){
        $id_grupo_funcionario = $this->getValue('id_grupo_funcionario');
        $hora_ini = $this->getValue('hora_ini');
        $hora_fim = $this->getValue('hora_fim');
        $hora_almoco_ini = $this->getValue('hora_almoco_ini');
        $hora_almoco_fim = $this->getValue('hora_almoco_fim');
        $id = intval($this->getValue('cd'));
        $nome = $this->getValue('nome');
        $cpf_cnpj = $this->getValue('cpf_cnpj');
        $senha = $this->getValue('senha');
        $email = $this->getValue('email');
        $telefone = $this->getValue('telefone');
     
        $dias = implode(",",[$this->getValue("dom"),$this->getValue("seg"),$this->getValue("ter"),$this->getValue("qua"),$this->getValue("qui"),$this->getValue("sex"),$this->getValue("sab")]);

        transactionManeger::init();

        transactionManeger::beginTransaction();

        try{
            if ($id_empresa = $this->getValue("id_empresa")){  
                $id_usuario = usuarioModel::set($nome,$cpf_cnpj,$email,$telefone,$senha,$id,2,$id_empresa);
                if ($id_usuario){
                    $id_funcionario = intval($this->getValue("id_funcionario"));
                    $id_funcionario = funcionarioModel::set($id_usuario,$nome,$cpf_cnpj,$email,$telefone,$hora_ini,$hora_fim,$hora_almoco_ini,$hora_almoco_fim,$dias,$id_funcionario);
                    if($id_funcionario){
                        if ($id_grupo_funcionario && $id_funcionario)
                            funcionarioModel::setFuncionarioGrupoFuncionario($id_grupo_funcionario,$id_funcionario);
                        
                        mensagem::setSucesso("Funcionario salvo com sucesso");    
                        transactionManeger::commit();
                        
                        $this->go("funcionario/index/");
                    }
                }
            }
        }catch(\Exception $e){
            mensagem::setSucesso(false);
            transactionManeger::rollback();
        }

        mensagem::setSucesso(false);
        transactionManeger::rollback();

        $this->go("funcionario/manutencao/"); 
    }

    public function massActionAgenda(){

        try{

            transactionManeger::init();

            transactionManeger::beginTransaction();

            $qtd_list = $this->getValue("qtd_list");
            $id_agenda = $this->getValue("agenda");

            if ($qtd_list && $id_agenda){
                for ($i = 1; $i <= $qtd_list; $i++) {
                    if($id_servico = $this->getValue("id_check_".$i)){
                        funcionarioModel::setAgendaFuncionario($id_servico,$id_agenda);
                    }
                }
                transactionManeger::commit();
            }

        }catch(\Exception $e){
            mensagem::setSucesso(false);
            mensagem::setErro("Erro inesperado ocorreu, tente novamente");
            transactionManeger::rollback();
        }

        $this->go("funcionario");
    }

    public function massActionGrupoFuncionario(){

        try{

            transactionManeger::init();

            transactionManeger::beginTransaction();

            $qtd_list = $this->getValue("qtd_list");
            $id_grupo_funcionario = $this->getValue("grupo_funcionario");

            if ($qtd_list && $id_grupo_funcionario){
                for ($i = 1; $i <= $qtd_list; $i++) {
                    if($id_funcionario = $this->getValue("id_check_".$i)){
                        funcionarioModel::setFuncionarioGrupoFuncionario($id_funcionario,$id_grupo_funcionario);
                    }
                }
            }

        }catch(\Exception $e){
            mensagem::setSucesso(false);
            mensagem::setErro("Erro inesperado ocorreu, tente novamente");
            transactionManeger::rollback();
        }

        $this->go("funcionario");
    }
}

?>
