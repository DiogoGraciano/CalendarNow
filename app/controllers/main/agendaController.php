<?php 
namespace app\controllers\main;
use app\classes\head;
use app\classes\form;
use app\classes\agenda;
use app\classes\controllerAbstract;
use app\classes\footer;
use app\models\main\agendaModel;

class agendaController extends controllerAbstract{

    public function index(){
        $head = new head();
        $head -> show("Conexão","agenda");

        $agenda = new agenda();
        $agenda->show("Agenda",$this->url."agenda/manutencao/",agendaModel::getEvents(date("Y-m-d H:i:s",strtotime("-1 Year")),date("Y-m-d H:i:s",strtotime("+1 Year"))));
      
        $footer = new footer;
        $footer->show();
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

        $dado = agendaModel::get($cd);

        $form = new form("Manutenção Agenda",$this->url."agenda/action/");

        $status = array($form->getObjectOption("Em Andamento","Em Andamento"),
                        $form->getObjectOption("Completo","Completo"),);

        $form->setHidden("cd",$cd);

        $form->setDoisInputs($form->input("cor","Cor:",$dado->cor,false,false,"","color","form-control form-control-color"),
                            $form->input("titulo","Titulo:",$dado->titulo,true));
        $form->setDoisInputs($form->select("Cliente","cd_cliente",$form->getOptions("tb_cliente","cd_cliente","nm_cliente"),$dado->cd_cliente,true),
                            $form->select("Funcionario","cd_funcionario",$form->getOptions("tb_ramal","cd_ramal","nm_funcionario"),$dado->cd_funcionario,true),
                            $form->select("Status","status ",$status,$dado->status,true)
        );
        $form->setDoisInputs($form->input("dt_inicio","Data Inicial:",$dado->dt_inicio?:$dt_ini,true,false,"","datetime-local","form-control form-control-date"),
                            $form->input("dt_fim","Data Final:",$dado->dt_fim?:$dt_fim,true,false,"","datetime-local","form-control form-control-date"));
        $form->setInputs($form->textarea("obs","Observações:",$dado->obs,false,false,"","3","12"));

        $form->setButton($form->button("Salvar","btn_submit"));
        $form->setButton($form->button("Voltar","btn_submit","button","btn btn-dark pt-2 btn-block","location.href='".$this->url."agenda'"));
        $form->show();

        $footer = new footer;
        $footer->show();
    }
    public function action($parameters){

        if ($parameters){
            agendaModel::delete($parameters[0]);
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

        agendaModel::set($cd_cliente,$cd_funcionario,$titulo,$dt_inicio,$dt_fim,$cor,$obs,$cd_agenda);

        $this->go("agenda/manutencao/".$cd_agenda);
    }

    public function export(){
        $this->go("tabela/exportar/tb_agenda");
    }

}