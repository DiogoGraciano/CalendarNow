<?php 
namespace app\controllers\main;
use app\classes\head;
use app\classes\form;
use app\classes\consulta;
use app\classes\controllerAbstract;
use app\classes\elements;
use app\classes\footer;
use app\classes\functions;
use app\classes\mensagem;
use app\classes\filter;
use app\models\main\agendaModel;
use app\models\main\usuarioModel;
use app\models\main\funcionarioModel;

class agendaController extends controllerAbstract{

    public function index(){

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

        $agenda->addButtons($elements->button("Voltar","voltar","button","btn btn-primary","location.href='".$this->url."opcoes'"));

        $agenda->addColumns("1","Id","id")->addColumns("70","Nome","nome")->addColumns("8","Codigo","codigo")->addColumns("11","Ações","acoes")
        ->show($this->url."agenda/manutencao/",$this->url."agenda/action/",agendaModel::getByEmpresa($user->id_empresa,$nome,$codigo));
      
        $footer = new footer;
        $footer->show();
    }
    public function manutencao($parameters){

        $id = "";

        $head = new head;
        $head->show("Manutenção Agenda");
        
        $form = new form($this->url."agenda/action/");

        $elements = new elements;

        if (array_key_exists(0,$parameters)){
            $id = functions::decrypt($parameters[0]);
            $form->setHidden("cd",$parameters[0]);
        }
        
        $dado = agendaModel::get($id);
        
        $form->setInputs($elements->input("nome","Nome:",$dado->nome,true));

        $user = usuarioModel::getLogged();

        $funcionarios = funcionarioModel::getByEmpresa($user->id_empresa);

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
    public function action($parameters){

        $user = usuarioModel::getLogged();

        if ($parameters){
            $id = functions::decrypt($parameters[0]);
            agendaModel::deleteAgendaUsuario($id);
            agendaModel::deleteAgendaFuncionario($id);
            agendaModel::delete($id);
            mensagem::setSucesso("Agenda deletada com sucesso");
            $this->go("agenda");
        }

        $id = functions::decrypt($this->getValue('cd'));
        $nome  = $this->getValue('nome');
        $id_funcionario  = $this->getValue('funcionario');
        $codigo  = $this->getValue('codigo');
        $id_empresa = $user->id_empresa;

        if ($id_agenda = agendaModel::set($nome,$id_empresa,$codigo,$id)){ 
            agendaModel::setAgendaUsuario($user->id,$id_agenda);
            if($id_funcionario)
                agendaModel::setAgendaFuncionario($id_funcionario,$id_agenda);
        }
       
        mensagem::setSucesso("Agenda salva com sucesso");
        $this->go("agenda");
    }
}