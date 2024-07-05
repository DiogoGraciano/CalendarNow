<?php 
namespace app\controllers\main;
use app\classes\head;
use app\classes\menu;
use app\classes\elements;
use app\classes\functions;
use app\classes\footer;
use app\classes\controllerAbstract;
use app\classes\form;
use app\models\main\loginModel;
use app\models\main\usuarioModel;

class configuracoesController extends controllerAbstract{

    public function index(){

        $head = new head();
        $head->show("Home","");

        $elements = new elements;

        $user = usuarioModel::getLogged();

        $form = new form($this->url."configuracoes/action");

        $form->setTresInputs($elements->input("max_agendamento_dia","Numero Maximo de agendamentos por Dia",
                        configuracoesModel::getConfig("max_agendamento_dia",$user->id_empresa)?:2,true,type_input:"number"),
                        $elements->input("max_agendamento_semana","Numero Maximo de agendamentos por Semana",
                        configuracoesModel::getConfig("max_agendamento_semana",$user->id_empresa)?:3,true,type_input:"number"),
                        $elements->input("max_agendamento_mes","Numero Maximo de agendamentos por Mês",
                        configuracoesModel::getConfig("max_agendamento_mes",$user->id_empresa)?:3,true,type_input:"number")
                    );

        $form->setDoisInputs(
            $elements->input("hora_ini", "Hora Inicial de Trabalho", functions::removeSecondsTime(configuracoesModel::getConfig("hora_ini",$user->id_empresa)?:"08:00"), true, true, "", "time2"),
            $elements->input("hora_fim", "Hora Final de Trabalho", functions::removeSecondsTime(configuracoesModel::getConfig("hora_fim",$user->id_empresa)?:"18:00"), true, true, "", "time2"),
            ["hora_ini", "hora_fim"]
        );

        $form->setDoisInputs(
            $elements->input("hora_almoco_ini", "Hora Inicial de Almoço", functions::removeSecondsTime(configuracoesModel::getConfig("hora_almoco_ini",$user->id_empresa)?:"12:00"), true, true, "", "time2"),
            $elements->input("hora_almoco_fim", "Hora Final de Almoço", functions::removeSecondsTime(configuracoesModel::getConfig("hora_almoco_fim",$user->id_empresa)?:"14:00"), true, true, "", "time2"),
            ["hora_almoco_ini", "hora_almoco_fim"]
        );
        
        $footer = new footer;
        $footer->show();
    }
    public function deslogar(){
        loginModel::deslogar();
        $this->go("login");
    }
}