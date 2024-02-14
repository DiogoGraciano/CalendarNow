<?php 
namespace app\controllers\main;
use app\classes\head;
use app\classes\form;
use app\classes\agenda;
use app\classes\controllerAbstract;
use app\classes\elements;
use app\classes\filter;
use app\classes\footer;
use app\classes\functions;
use app\models\main\agendamentoModel;
use app\models\main\empresaModel;

class agendamentoController extends controllerAbstract{

    private $funcionario;
    private $grupo;

    public function index($parameters){
        $head = new head();
        $head->show("Agenda","agenda");

        $id_agenda = "";

        if (array_key_exists(0,$parameters)){
            $id_agenda = functions::decrypt($parameters[0]);
        }

        $elements = new elements;

        $filter = new filter($this->url."agendamento/filter/".$parameters[0]);
        $filter->addbutton($elements->button("Buscar","buscar","submit","btn btn-primary pt-2"));

        $elements->setOptions("grupo_funcionario","id","nome");
        $filter->addFilter(6,[$elements->select("Grupo","grupo")]);

        $elements->setOptions("funcionario","id","nome");
        $filter->addFilter(6,[$elements->select("Funcionario","funcionario")]);

        $filter->show();

        $empresa = empresaModel::getByAgenda($id_agenda);

        $agenda = new agenda();
        $agenda->show($this->url."agendamento/manutencao/",agendamentoModel::getEvents(date("Y-m-d H:i:s",strtotime("-1 Year")),date("Y-m-d H:i:s",strtotime("+1 Year")),$id_agenda));
      
        $footer = new footer;
        $footer->show();
    }
    
    public function filter($parameters){
        $this->funcionario = $this->getValue("funcionario");
        $this->grupo = $this->getValue("grupo");

        $this->go("agendamento/index/".$parameters[0]);
    }
    public function manutencao($parameters){

        $cd = "";
        $dt_fim = "";
        $dt_ini = "";

        if (array_key_exists(1,$parameters)){
            $dt_fim = date("Y-m-d H:i:s",strtotime(substr(base64_decode($parameters[1]),0,34)));
            $dt_ini = date("Y-m-d H:i:s",strtotime(substr(base64_decode($parameters[0]),0,34)));
        }
        elseif (array_key_exists(0,$parameters) && !array_key_exists(1,$parameters))
            $cd = $parameters[0];
        
        $head = new head;
        $head->show("Manutenção Agenda");

        $elements = new elements;

        $dado = agendamentoModel::get($cd);

        $form = new form("Manutenção Agenda",$this->url."agenda/action/");

        $elements->addObjectOption("Em Andamento","Em Andamento");
        $elements->addObjectOption("Completo","Completo");
        $status = $elements->select("Status","status ",$dado->status,true);

        $elements->setOptions("usuario","id","nome");
        $cliente = $elements->select("Cliente","usuario",$dado->id_usuario,true);

        $elements->setOptions("agenda","id","nome");
        $funcionario = $elements->select("Agenda","cd_funcionario",$dado->id_agenda,true);

        $form->setHidden("cd",$cd);

        $form->setDoisInputs($elements->input("cor","Cor:",$dado->cor,false,false,"","color","form-control form-control-color"),
                            $elements->input("titulo","Titulo:",$dado->titulo,true));
        $form->setTresInputs($cliente,
                            $funcionario,
                            $status
        );
        $form->setDoisInputs($elements->input("dt_inicio","Data Inicial:",$dado->dt_inicio?:$dt_ini,true,false,"","datetime-local","form-control form-control-date"),
                            $elements->input("dt_fim","Data Final:",$dado->dt_fim?:$dt_fim,true,false,"","datetime-local","form-control form-control-date"));
        $form->setInputs($elements->textarea("obs","Observações:",$dado->obs,false,false,"","3","12"));

        $form->setButton($elements->button("Salvar","submit"));
        $form->setButton($elements->button("Voltar","submit"));
        $form->show();

        $footer = new footer;
        $footer->show();
    }
    public function action($parameters){

        if ($parameters){
            agendamentoModel::delete($parameters[0]);
            $this->go("agenda");
            return;
        }

        $cd_agenda  = $this->getValue('cd');
        $cd_cliente = $this->getValue('cd_cliente');
        $cd_funcionario  = $this->getValue('cd_funcionario');
        $titulo = $this->getValue('titulo');
        $dt_inicio = $this->getValue('dt_inicio');
        $dt_fim = $this->getValue('dt_fim');
        $cor = $this->getValue('cor');
        $obs = $this->getValue('obs');

        agendamentoModel::set($cd_cliente,$cd_funcionario,$titulo,$dt_inicio,$dt_fim,$cor,$obs,$cd_agenda);

        $this->go("agenda/manutencao/".$cd_agenda);
    }

    public function export(){
        $this->go("tabela/exportar/tb_agenda");
    }

}