<?php 
namespace app\controllers\main;
use app\classes\head;
use app\classes\elements;
use app\classes\mensagem;
use app\classes\functions;
use app\classes\footer;
use app\classes\controllerAbstract;
use app\classes\form;
use app\models\main\configuracoesModel;
use app\models\main\usuarioModel;
use app\db\transactionManeger;

class configuracoesController extends controllerAbstract{

    public function index(){

        $head = new head();
        $head->show("Home","");

        $elements = new elements;

        $user = usuarioModel::getLogged();

        $form = new form($this->url."configuracoes/action");

        $form->setTresInputs($elements->input("max_agendamento_dia","Maximo de Agendamentos por Dia",
                        configuracoesModel::getConfig("max_agendamento_dia",$user->id_empresa)?:2,true,type_input:"number"),
                        $elements->input("max_agendamento_semana","Maximo de Agendamentos por Semana",
                        configuracoesModel::getConfig("max_agendamento_semana",$user->id_empresa)?:3,true,type_input:"number"),
                        $elements->input("max_agendamento_mes","Maximo de Agendamentos por Mês",
                        configuracoesModel::getConfig("max_agendamento_mes",$user->id_empresa)?:3,true,type_input:"number")
                    );

        $form->setDoisInputs(
            $elements->input("hora_ini", "Hora Inicial de Abertura", functions::removeSecondsTime(configuracoesModel::getConfig("hora_ini",$user->id_empresa)?:"08:00"), true, true, "", "time2"),
            $elements->input("hora_fim", "Hora Final de Abertura", functions::removeSecondsTime(configuracoesModel::getConfig("hora_fim",$user->id_empresa)?:"18:00"), true, true, "", "time2"),
            ["hora_ini", "hora_fim"]
        );

        $form->setDoisInputs(
            $elements->input("hora_almoco_ini", "Hora Inicial de Almoço", functions::removeSecondsTime(configuracoesModel::getConfig("hora_almoco_ini",$user->id_empresa)?:"12:00"), true, true, "", "time2"),
            $elements->input("hora_almoco_fim", "Hora Final de Almoço", functions::removeSecondsTime(configuracoesModel::getConfig("hora_almoco_fim",$user->id_empresa)?:"14:00"), true, true, "", "time2"),
            ["hora_almoco_ini", "hora_almoco_fim"]
        );

        $form->setButton($elements->button("Salvar","submit"));
        $form->setButton($elements->button("Voltar","voltar","button","btn btn-primary w-100 btn-block","location.href='".$this->url."opcoes'"));
        $form->show();
        
        $footer = new footer;
        $footer->show();
    }

    public function action($parameters){

        $user = usuarioModel::getLogged();

        try {
            transactionManeger::init();

            transactionManeger::beginTransaction();

            configuracoesModel::set("max_agendamento_dia",$user->id_empresa,$this->getValue("max_agendamento_dia"));
            configuracoesModel::set("max_agendamento_semana",$user->id_empresa,$this->getValue("max_agendamento_semana"));
            configuracoesModel::set("max_agendamento_mes",$user->id_empresa,$this->getValue("max_agendamento_mes"));
            configuracoesModel::set("hora_ini",$user->id_empresa,$this->getValue("hora_ini"));
            configuracoesModel::set("hora_fim",$user->id_empresa,$this->getValue("hora_fim"));
            configuracoesModel::set("hora_almoco_ini",$user->id_empresa,$this->getValue("hora_almoco_ini"));
            configuracoesModel::set("hora_almoco_fim",$user->id_empresa,$this->getValue("hora_almoco_fim"));

            transactionManeger::commit();
            mensagem::setSucesso("Configuracões salvas com sucesso");
        } catch (\Exception $e) {
            transactionManeger::rollBack();
            mensagem::setSucesso(false);
            mensagem::setErro("Erro ao salvar configuracões");
            mensagem::setErro($e->getMessage());
        }

        $this->go("configuracoes/".$this->getValue('cd'));
    }
}