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
use app\models\main\agendaModel;
use app\models\main\usuarioModel;

class agendaController extends controllerAbstract{

    public function index(){
        $head = new head();
        $head -> show("agendas","consulta");

        $elements = new elements;

        $user = usuarioModel::getLogged();

        $agenda = new consulta();

        $agenda->addButtons($elements->button("Voltar","voltar","button","btn btn-primary","location.href='".$this->url."opcoes'"));

        $agenda->addColumns("1","Id","id")->addColumns("70","Nome","nome")->addColumns("8","Codigo","codigo")->addColumns("11","Ações","acoes")
        ->show($this->url."agenda/manutencao/".functions::encrypt($user->id_empresa),$this->url."agenda/action/",agendaModel::getByEmpresa($user->id_empresa));
      
        $footer = new footer;
        $footer->show();
    }
    public function manutencao($parameters){

        $cd = "";

        $head = new head;
        $head->show("Manutenção Agenda");
        
        $form = new form($this->url."agenda/action/");

        $elements = new elements;

        if (array_key_exists(1,$parameters)){
            $cd = functions::decrypt($parameters[1]);
            $form->setHidden("cd",$parameters[1]);
        }
        
        if (array_key_exists(0,$parameters)){
            $form->setHidden("id_empresa",$parameters[0]);
        }

        $dado = agendaModel::get($cd);
        
        $form->setInputs($elements->input("nome","Nome:",$dado->nome,true));

        $elements->setOptions("funcionario","id","nome");
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
            $cd = functions::decrypt($parameters[0]);
            agendaModel::deleteAgendaUsuario($cd);
            agendaModel::deleteAgendaFuncionario($cd);
            agendaModel::delete($cd);
            mensagem::setSucesso(["Agenda deletada com sucesso"]);
            $this->go("agenda");
        }

        $id = functions::decrypt($this->getValue('cd'));
        $nome  = $this->getValue('nome');
        $id_funcionario  = $this->getValue('funcionario');
        $codigo  = $this->getValue('codigo');
        $id_empresa  = functions::decrypt($this->getValue('id_empresa'));

        if ($id_agenda = agendaModel::set($nome,$id_empresa,$codigo,$id)){ 
            agendaModel::setAgendaUsuario($user->id,$id_agenda);
            agendaModel::setAgendaFuncionario($id_funcionario,$id_agenda);
        }
       
        mensagem::setSucesso(["Agenda salva com sucesso"]);
        $this->go("agenda");
    }
}