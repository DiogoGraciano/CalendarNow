<?php 
namespace app\controllers\main;
use app\layout\head;
use app\layout\form;
use app\layout\consulta;
use app\controllers\abstract\controller;
use app\layout\elements;
use app\layout\footer;
use app\layout\tabela;
use app\layout\tabelaMobile;
use app\helpers\functions;
use app\helpers\mensagem;
use app\layout\filter;
use app\db\transactionManeger;
use app\models\main\agendaModel;
use app\models\main\usuarioModel;
use app\models\main\funcionarioModel;
use core\session;

final class agendaController extends controller{

    public function index()
    {
        session::set("agendaController",false);

        $nome = $this->getValue("nome");
        $codigo = $this->getValue("codigo");

        $head = new head();
        $head -> show("agendas","consulta");

        $elements = new elements;

        $filter = new filter($this->url."agenda/index/");

        $filter->addbutton($elements->button("Buscar","buscar","submit","btn btn-primary pt-2"))
                ->addFilter(3,$elements->input("nome","Nome:",$nome))
                ->addFilter(3,$elements->input("codigo","Codigo:",$codigo));

        $filter->show();

        $user = usuarioModel::getLogged();

        $agenda = new consulta();

        $agenda->addButtons($elements->button("Adicionar","manutencao","button","btn btn-primary","location.href='".$this->url."agenda/manutencao'"));
        $agenda->addButtons($elements->button("Voltar","voltar","button","btn btn-primary","location.href='".$this->url."opcoes'"));

        $agenda->addColumns("1","Id","id")
            ->addColumns("70","Nome","nome")
            ->addColumns("8","Codigo","codigo")
            ->addColumns("11","Ações","acoes")
            ->show($this->url."agenda/manutencao",
                    $this->url."agenda/action/",
                    agendaModel::getByEmpresa($user->id_empresa,$nome,$codigo,$this->getLimit(),$this->getOffset()),
                    "id",
                    $this->getLimit(),
                    agendaModel::getLastCount("getByEmpresa"));
      
        $footer = new footer;
        $footer->show();
    }
    public function manutencao($parameters){

        $id = "";

        $head = new head;
        $head->show("Manutenção Agenda");
        
        $form = new form($this->url."agenda/action/");

        $elements = new elements;

        if ($parameters && array_key_exists(0,$parameters)){
            $id = functions::decrypt($parameters[0]);
            $form->setHidden("cd",$parameters[0]);
        }
        
        $dado = session::get("agendaController")?:agendaModel::get($id);
        
        $form->setInputs($elements->input("nome","Nome:",$dado->nome,true));

        $user = usuarioModel::getLogged();

        $funcionarios = agendaModel::getFuncionarioByAgenda($dado->id);

        if($funcionarios){

            $form->setInputs($elements->label("Funcionarios Vinculados"));

            if ($this->isMobile()){
                $table = new tabelaMobile();
            }else {
                $table = new tabela();
            }
            $table->addColumns("1","ID","id");
            $table->addColumns("90","Nome","nome");
            $table->addColumns("10","Ações","acoes");

            foreach ($funcionarios as $funcionario){
                $funcionario->acoes = $elements->button("Desvincular", "desvincular", "button", "btn btn-primary w-100 pt-2 btn-block", "location.href='".$this->url."funcionario/desvincularFuncionario/".functions::encrypt($dado->id)."/".functions::encrypt($funcionario->id)."'");
                $table->addRow($funcionario->getArrayData());
            }

            $form->setInputs($table->parse());
        }

        $funcionarios = funcionarioModel::getByEmpresa($user->id_empresa);

        $elements->addOption("","Nenhum");
        foreach ($funcionarios as $funcionario){
            $elements->addOption($funcionario->id,$funcionario->nome);
        }
        $form->setInputs($elements->select("Funcionario:","funcionario",""));

        $form->setInputs($elements->input("codigo","Codigo:",$dado->codigo,false,true));

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

        $user = usuarioModel::getLogged();

        if ($parameters){
            $id = functions::decrypt($parameters[0]);
            agendaModel::delete($id);
            $this->go("agenda");
        }

        $id = intval(functions::decrypt($this->getValue('cd')));
        $nome  = $this->getValue('nome');
        $id_funcionario  = $this->getValue('funcionario');
        $codigo  = $this->getValue('codigo');
        $id_empresa = $user->id_empresa;

        $agenda = new \stdClass;
    
        $agenda->id               = $id;
        $agenda->id_funcionario   = $id_funcionario;
        $agenda->codigo           = $codigo;
        $agenda->nome             = $nome;

        session::set("agendaController",$agenda);

        try{
            transactionManeger::init();
            transactionManeger::beginTransaction();
            if ($id_agenda = agendaModel::set($nome,$id_empresa,$codigo,$id)){ 
                agendaModel::setAgendaUsuario($user->id,$id_agenda);
                if($id_funcionario)
                    agendaModel::setAgendaFuncionario($id_funcionario,$id_agenda);
                mensagem::setSucesso("Agenda salva com sucesso");
                session::set("agendaController",false);
                transactionManeger::commit();
                $this->go("agenda");
            }
        }catch (\exception $e){
            mensagem::setSucesso(false);
            transactionManeger::rollBack();
            mensagem::setErro("Erro ao cadastrar agenda, tente novamente");
        }

        mensagem::setSucesso(false);
        $this->go("agenda/manutencao/".$this->getValue('cd'));
    }
}