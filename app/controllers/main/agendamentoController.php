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
use app\classes\lista;
use app\models\main\agendamentoModel;
use app\models\main\empresaModel;
use app\models\main\funcionarioModel;
use app\models\main\servicoModel;

class agendamentoController extends controllerAbstract{

    private $funcionario = "";
    // private $grupo;

    public function index($parameters){
        $head = new head();
        $head->show("Agenda","agenda");

        $id_agenda = "";

        if (array_key_exists(0,$parameters))
            $id_agenda = functions::decrypt($parameters[0]);
        else
            $this->go("home");

        $elements = new elements;

        $filter = new filter($this->url."agendamento/filter/".$parameters[0]);
        $filter->addbutton($elements->button("Buscar","buscar","submit","btn btn-primary pt-2"));

        // $elements->setOptions("grupo_funcionario","id","nome");
        // $filter->addFilter(6,[$elements->select("Grupo","grupo")]);

        $funcionarios = funcionarioModel::getByAgenda($id_agenda);

        $i = 1;
        $firstFuncionario = "";
        foreach ($funcionarios as $funcionario){
            if ($i == 1){
                $firstFuncionario = $funcionario->id;
            }
            $elements->addOption($funcionario->id,$funcionario->nome);
        }

        $Dadofuncionario = funcionarioModel::get($this->funcionario==""?:$firstFuncionario);

        $filter->addFilter(6,[$elements->select("Funcionario","funcionario",$Dadofuncionario->id)]);

        $filter->show();

        $agenda = new agenda();
        $agenda->addButton($elements->button("Voltar","voltar","button","btn btn-primary w-100 btn-block","location.href='".$this->url."home'"));
        $agenda->show($this->url."agendamento/manutencao/".$parameters[0]."/".functions::encrypt($this->funcionario==""?:$firstFuncionario)."/",agendamentoModel::getEvents(date("Y-m-d H:i:s",strtotime("-1 Year")),date("Y-m-d H:i:s",strtotime("+1 Year")),$id_agenda));
      
        $footer = new footer;
        $footer->show();
    }
    
    public function filter($parameters){
        $this->funcionario = functions::decrypt($this->getValue("funcionario"));
        // $this->grupo = $this->getValue("grupo");

        $this->go("agendamento/index/".$parameters[0]);
    }
    public function manutencao($parameters){

        $head = new head;
        $head->show("Manutenção Agenda");

        $cd = "";
        $dt_fim = "";
        $dt_ini = "";
        $id_funcionario = "";
        $id_agenda = "";

        $form = new form("Manutenção Agenda",$this->url."agenda/action/");

        if (array_key_exists(3,$parameters)){
            $dt_fim = date("Y-m-d H:i:s",strtotime(substr(base64_decode(str_replace("@","/",$parameters[3])),0,34)));
            $dt_ini = date("Y-m-d H:i:s",strtotime(substr(base64_decode(str_replace("@","/",$parameters[2])),0,34)));
        }
        elseif (!array_key_exists(3,$parameters) && array_key_exists(2,$parameters)){
           $cd = functions::decrypt($parameters[2]);
           $form->setHidden("cd",$parameters[2]);
        }
        if (array_key_exists(1,$parameters)){
            $form->setHidden("id_funcionario",$parameters[1]);
            $id_funcionario = functions::decrypt($parameters[1]);
        }if (array_key_exists(0,$parameters)){
            $form->setHidden("id_agenda",$parameters[0]);
            $id_agenda = functions::decrypt($parameters[0]);
        }
        
        $elements = new elements;

        $dado = agendamentoModel::get($cd);

        $elements->addOption("0","Agendado");
        $elements->addOption("1","Completo");
        $elements->addOption("99","Cancelado");
        $status = $elements->select("Status","status ",$dado->status,true);

        $elements->setOptions("usuario","id","nome");
        $cliente = $elements->select("Cliente","usuario",$dado->id_usuario,true);

        // $elements->setOptions("agenda","id","nome");
        // $agenda = $elements->select("Agenda","agenda",$dado->id_agenda,true);

        $form->setHidden("cd",$cd);

        // $form->setDoisInputs($elements->input("cor","Cor:",$dado->cor,false,false,"","color","form-control form-control-color"),
        //                     $elements->input("titulo","Titulo:",$dado->titulo,true));

        $form->setDoisInputs($cliente,
                            $status
        );

        $form->addCustomInput("3 col-6 mb-2",$elements->input("dt_ini","Data Inicial:",$dado->dt_ini?:$dt_ini,true,false,"","datetime-local","form-control form-control-date"),"dt_ini");
        $form->addCustomInput("3 col-6 mb-2",$elements->input("dt_fim","Data Final:",$dado->dt_fim?:$dt_fim,true,false,"","datetime-local","form-control form-control-date"),"dt_fim");
        $form->addCustomInput("3 col-6 d-flex align-items-end mb-2",$elements->button("Anterior","anterior","button","btn btn-primary w-100 btn-block"),"anterior w-100");
        $form->addCustomInput("3 col-6 d-flex align-items-end mb-2",$elements->button("Proximo","proximo","button","btn btn-primary w-100 btn-block"),"proximo w-100");
    
        $form->setCustomInputs();

        $Dadofuncionario = funcionarioModel::get($id_funcionario);
        
        $form->setInputs($elements->label("Serviços"));

        $servico = servicoModel::getByServicoGrupoServico($Dadofuncionario->id_grupo_servico);

        $form->addCustomInput("4 col-4",$elements->label("Titulo"));
        $form->addCustomInput("2 col-2",$elements->label("Quantidade"));
        $form->addCustomInput("2 col-2",$elements->label("Tempo"));
        $form->addCustomInput("2 col-2",$elements->label("Total"));
        $form->addCustomInput("2 col-2",$elements->label("Selecionar"));

        $form->setCustomInputs();

        $servicos = servicoModel::getByServicoGrupoServico($Dadofuncionario->id_grupo_servico);

        foreach ($servicos as $servico){
            $form->addCustomInput("4 col-4 d-flex align-items-end mb-2",$elements->label($servico->nome),"titulo");
            $form->addCustomInput("2 col-2",$elements->input("qtd_item","",$dado->qtd_item==""?1:$dado->qtd_item),"qtd_item");
            $form->addCustomInput("2 col-2",$elements->input("tempo_item","",$dado->tempo_item==""?$servico->tempo:$dado->tempo_item,false,true),"tempo_item");
            $form->addCustomInput("2 col-2",$elements->input("total_item","",$dado->total_item==""?$servico->total:$dado->total_item,false,true),"total_item");
            $form->addCustomInput("2 col-2 d-flex align-items-center",$elements->checkbox($servico->id,"",false,$dado->id_servico==""?true:false,false));
            $form->setCustomInputs();
        }

        $form->setInputs($elements->textarea("obs","Observações:",$dado->obs,false,false,"","3","12"));

        $form->addCustomInput("1 col-2 d-flex align-items-end mb-2",$elements->label("Total"),"total");
        $form->addCustomInput("11 col-10",$elements->input("total","",$dado->total==""?0:$dado->total,false,true));
        $form->setCustomInputs();

        $form->setButton($elements->button("Salvar","submit"));
        $form->setButton($elements->button("Voltar","voltar","button","btn btn-primary w-100 btn-block","location.href='".$this->url."agendamento/index/".$parameters[0]."'"));
        
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