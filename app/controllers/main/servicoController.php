<?php 
namespace app\controllers\main;
use app\classes\head;
use app\classes\form;
use app\classes\consulta;
use app\classes\controllerAbstract;
use app\classes\elements;
use app\classes\footer;
use app\classes\functions;
use app\models\main\agendaModel;
use app\models\main\servicoModel;
use app\models\main\usuarioModel;

class servicoController extends controllerAbstract{

    public function index(){
        $head = new head();
        $head -> show("serviços","consulta");

        $elements = new elements;

        $user = usuarioModel::getLogged();

        $servico = new consulta();

        $servico->addButtons($elements->button("Voltar","voltar","button","btn btn-primary","location.href='".$this->url."opcoes'")); 

        $servico->addColumns("10","Id","id")
                ->addColumns("15","Agenda Nome","nome_agenda")
                ->addColumns("15","Empresa Nome","nome_empresa")
                ->addColumns("15","Serviço Nome","servico_nome")
                ->addColumns("15","Serviço Tempo","servico_tempo")
                ->addColumns("20","Ações","acoes")
        ->show($this->url."servico/manutencao/".functions::encrypt($user->id_empresa),$this->url."servico/action/",servicoModel::getByEmpresa($user->id_empresa));
      
        $footer = new footer;
        $footer->show();
    }
    public function manutencao($parameters){

        $cd = "";

        $head = new head;
        $head->show("Manutenção Serviço");
        
        $form = new form($this->url."servico/action/");

        $elements = new elements;

        if (array_key_exists(1,$parameters)){
            $cd = functions::decrypt($parameters[1]);
            $form->setHidden("cd",$parameters[1]);
        }
        
        if (array_key_exists(0,$parameters)){
            $form->setHidden("id_empresa",$parameters[0]);
            $id_empresa = functions::decrypt($parameters[0]);
        }

        $dado = servicoModel::get($cd);
        
        $form->setInputs($elements->input("nome","Nome:",$dado->nome,true));

        $elements->setOptions("grupo_servico","id","nome");
        $id_grupo_servico = $elements->select("Grupo de Servicos:","id_grupo_servico",$dado->id_grupo_servico);

        $agendas = agendaModel::getByEmpresa($id_empresa);

        foreach ($agendas as $agenda){
            $elements->addObjectOption($agenda->id,$agenda->nome);
        }

        $form->setDoisInputs(
            $elements->select("Agenda","id_agenda",$dado->id_agenda),
            $id_grupo_servico,
            array("agenda","id_grupo_servico")
        );

        $form->setDoisInputs(
            $elements->input("tempo","Tempo de Trabalho:",functions::formatTime($dado->tempo),true,false,"","time"),
            $elements->input("valor","Valor:",$dado->valor,true,false,""),
            array("tempo","valor")
        );

        $form->setButton($elements->button("Salvar","btn_submit"));
        $form->setButton($elements->button("Voltar","voltar","button","btn btn-primary w-100 btn-block","location.href='".$this->url."agenda'"));
        $form->show();

        $footer = new footer;
        $footer->show();
    }
    public function action($parameters){

        $user = usuarioModel::getLogged();

        if ($parameters){
            $cd = functions::decrypt($parameters[0]);
            $agendas = agendaModel::getByUserServico($cd,$user->id);
            if ($agendas){
                $agenda = $agendas[0];
            }
            servicoModel::deleteAgendaServico($agenda->id,$cd);
            servicoModel::delete($cd);
            $this->go("servico");
        }

        $id = functions::decrypt($this->getValue('cd'));
        $nome  = $this->getValue('nome');
        $id_grupo_servico  = $this->getValue('id_grupo_servico');
        $id_agenda  = $this->getValue('id_agenda');
        $tempo  = $this->getValue('tempo');
        $valor  = $this->getValue('valor');

        if ($id_servico = servicoModel::set($nome,$valor,$tempo,$id_grupo_servico,$id) && !$id){ 
            servicoModel::setAgendaServico($id_servico,$id_agenda);
        }

        $this->go("servico");
    }
}