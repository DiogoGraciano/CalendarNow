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
use app\models\main\agendaModel;
use app\models\main\empresaModel;
use app\models\main\funcionarioModel;
use app\models\main\servicoModel;
use app\models\main\usuarioModel;
use stdClass;

class agendamentoController extends controllerAbstract{
  
    public function index($parameters){
        $head = new head();
        $head->show("Agenda","agenda");

        $id_agenda = "";
        $id_funcionario = "";

        if (array_key_exists(0,$parameters))
            $id_agenda = functions::decrypt($parameters[0]);
        else
            $this->go("home");

        if (array_key_exists(1,$parameters))
            $id_funcionario = functions::decrypt($parameters[0]);

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

        $Dadofuncionario = funcionarioModel::get($id_funcionario==""?:$firstFuncionario);

        $filter->addFilter(6,[$elements->select("Funcionario","funcionario",$Dadofuncionario->id)]);

        $filter->show();

        $agenda = new agenda();
        $agenda->addButton($elements->button("Voltar","voltar","button","btn btn-primary w-100 btn-block","location.href='".$this->url."home'"));
        $agenda->show($this->url."agendamento/manutencao/".$parameters[0]."/".functions::encrypt($id_funcionario==""?:$firstFuncionario)."/",agendamentoModel::getEvents(date("Y-m-d H:i:s",strtotime("-1 Year")),date("Y-m-d H:i:s",strtotime("+1 Year")),$id_agenda));
      
        $footer = new footer;
        $footer->show();
    }
    
    public function filter($parameters){
        // $this->grupo = $this->getValue("grupo");
        $this->go("agendamento/index/".$parameters[0]."/".functions::decrypt($this->getValue("funcionario")));
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

        $user = usuarioModel::getLogged();

        if ($user->tipo_usuario != 3){

            $elements->addOption("0","Agendado");
            $elements->addOption("1","Completo");
            $elements->addOption("99","Cancelado");
            $status = $elements->select("Status","status",$dado->status);

            $usuarios = usuarioModel::getByTipoUsuario(4);

            foreach ($usuarios as $usuario){
                $elements->addOption($usuario->id,$usuario->nome);
            }

            $cliente = $elements->select("Cliente","usuario",$dado->id_usuario);

            $agendas = [];

            if ($user->tipo_usuario < 2)
                $agendas = agendaModel::getByEmpresa($user->id_empresa);
            else 
                $agendas = agendaModel::getByUser($user->id);

            foreach ($agendas as $agenda){
                $elements->addOption($agenda->id,$agenda->nome);
            }

            $agenda = $elements->select("Agenda","agenda",$dado->id_agenda);

            $form->setDoisInputs($elements->input("cor","Cor:",$dado->cor?:"#4267b2",false,false,"","color","form-control form-control-color"),
                                $agenda);

            $form->addCustomInput("4 col-sm-12 usuario mb-2",$cliente,"usuario")
                ->addCustomInput("2 col-sm-12 d-flex align-items-end mb-2",$elements->button("Novo","novoCliente","button"),"w-100")
                ->addCustomInput(6,$status)
                ->setCustomInputs();
           
        }

        $form->addCustomInput("3 col-6 mb-2",$elements->input("dt_ini","Data Inicial:",$dado->dt_ini?:$dt_ini,true,false,"","datetime-local","form-control form-control-date"),"dt_ini");
        $form->addCustomInput("3 col-6 mb-2",$elements->input("dt_fim","Data Final:",$dado->dt_fim?:$dt_fim,true,false,"","datetime-local","form-control form-control-date"),"dt_fim");
        $form->addCustomInput("3 col-6 d-flex align-items-end mb-2",$elements->button("Anterior","anterior","button","btn btn-primary w-100 btn-block"),"anterior w-100");
        $form->addCustomInput("3 col-6 d-flex align-items-end mb-2",$elements->button("Proximo","proximo","button","btn btn-primary w-100 btn-block"),"proximo w-100");
    
        $form->setCustomInputs();

        $Dadofuncionario = funcionarioModel::get($id_funcionario);
        
        $form->setInputs($elements->label("Serviços"));

        $form->addCustomInput("4 col-4",$elements->label("Titulo"));
        $form->addCustomInput("2 col-2",$elements->label("Quantidade"));
        $form->addCustomInput("2 col-2",$elements->label("Tempo"));
        $form->addCustomInput("2 col-2",$elements->label("Total"));
        $form->addCustomInput("2 col-2",$elements->label("Selecionar"));

        $form->setCustomInputs();

        $servicos = servicoModel::getByFuncionario($Dadofuncionario->id);

        $i = 1;
        foreach ($servicos as $servico){
            $form->addCustomInput("4 col-4 d-flex align-items-end mb-2",$elements->label($servico->nome),"titulo");
            $form->addCustomInput("2 col-2",$elements->input("qtd_item_".$i,"",isset($dado->qtd_item)?$dado->qtd_item:1,false,false,"","number","form-control qtd_item",'min="1" data-index-servico="'.$i.'"'),"qtd_item");
            $form->addCustomInput("2 col-2",$elements->input("tempo_item_".$i,"",isset($dado->tempo_item)?$dado->tempo_item:$servico->tempo,false,true,"","text","form-control",'data-vl-base="'.$servico->tempo.'"'),"tempo_item");
            $form->addCustomInput("2 col-2",$elements->input("total_item_".$i,"",isset($dado->total_item)?functions::formatCurrency($dado->total_item):functions::formatCurrency($servico->valor),false,true,"","text","form-control",'data-vl-base="'.$servico->valor.'" data-vl-atual="'.$servico->valor.'"'),"total_item");
            $form->addCustomInput("2 col-2 d-flex align-items-center mt-3",$elements->checkbox("servico_index_".$i,"",false,isset($dado->id_servico)?true:false,false,$servico->id,"checkbox","form-check-input check_item",'data-index-check="'.$i.'"'));
            $form->setCustomInputs();
            $i++;
        }

        $form->setHidden("qtd_servico",$i);

        $form->setInputs($elements->textarea("obs","Observações:",$dado->obs,false,false,"","3","12"));

        $total = $dado->total?:0;

        $form->addCustomInput("1 col-2 d-flex align-items-end mb-2",$elements->label("Total"),"total");
        $form->addCustomInput("11 col-10",$elements->input("total","",$dado->total?functions::formatCurrency($dado->total):"R$ 0.00",false,true,"","text","form-control",'data-vl-total="'.$total.'"'));
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
            $this->go("agendamento");
            return;
        }

        $id  = $this->getValue('cd');
        $dt_ini = $this->getValue('dt_ini');
        $dt_fim = $this->getValue('dt_fim');
        $qtd_list = $this->getValue("qtd_list");
        $status = $this->getValue("status");
        $id_agenda = $this->getValue("agenda"); 
        $id_funcionario = $this->getValue("id_funcionario");
        $array_itens = []; 
        $total = 0;
        if ($qtd_list){
            for ($i = 1; $i <= $qtd_list; $i++) {
                $objeto_item = new stdClass;
                if($servico_id = $this->getValue('servico_index_'.$i) && $qtd_item = $this->getValue('qtd_item_'.$i) && $tempo_item = $this->getValue('tempo_item_'.$i) && $total_item = $this->getValue('total_item_'.$i)){
                    $objeto_item->servico_id = $servico_id;
                    $objeto_item->qtd_item = $qtd_item;
                    $objeto_item->tempo_item = $tempo_item;
                    $objeto_item->total_item = $total_item;
                    $total =+ $total_item;
                }
                if ($objeto_item)
                    $array_itens[] = $objeto_item;
            }
        }
        $cor = $this->getValue('cor');
        $obs = $this->getValue('obs');

        $usuario = "";

        $user = usuarioModel::getLogged();
        if ($user->tipo_usuario != 3){
            $usuario = $this->getValue('usuario');
            $id_usuario = "";
            if (is_int($usuario))
                $id_usuario = $usuario;
            else 
                $id_usuario = usuarioModel::set($usuario);

            $usuario = usuarioModel::get($id_usuario);
        }
        else 
            $usuario = $user;

        $id_agendamento = agendamentoModel::set($id_agenda,$usuario->id_usuario,$id_funcionario,$usuario->nome,$dt_ini,$dt_fim,$cor,$obs,$total,$id);

        $this->go("agenda/manutencao/".functions::encrypt($id));
    }

    public function export(){
        $this->go("tabela/exportar/tb_agenda");
    }

}