<?php 
namespace app\controllers\main;
use app\layout\head;
use app\layout\form;
use app\layout\agenda;
use app\layout\consulta;
use app\controllers\abstract\controller;
use app\db\transactionManeger;
use app\layout\elements;
use app\layout\filter;
use app\layout\footer;
use app\layout\tabela;
use app\layout\tabelaMobile;
use app\helpers\functions;
use app\helpers\mensagem;
use app\models\main\agendamentoItemModel;
use app\models\main\agendamentoModel;
use app\models\main\agendaModel;
use app\models\main\funcionarioModel;
use app\models\main\servicoModel;
use app\models\main\usuarioModel;
use app\models\main\clienteModel;
use app\models\main\statusModel;
use core\session;

class agendamentoController extends controller{
  
    public function index($parameters)
    {
        session::set("agendamentoController",false);

        $head = new head();
        $head->show("Agenda","agenda");

        $id_agenda = "";
        $id_funcionario = $this->getValue("id_funcionario");

        if (array_key_exists(0,$parameters))
            $id_agenda = functions::decrypt($parameters[0]);
        else
            $this->go("home");

        $elements = new elements;

        $filter = new filter($this->url."agendamento/index/".$parameters[0]);
        $filter->addbutton($elements->button("Buscar","buscar","submit","btn btn-primary pt-2"));

        $funcionarios = funcionarioModel::getByAgenda($id_agenda);

        $i = 1;
        $firstFuncionario = "";
        foreach ($funcionarios as $funcionario){
            if ($i == 1){
                $firstFuncionario = $funcionario->id;
                $i++;
            }
            $elements->addOption($funcionario->id,$funcionario->nome);
        }

        $Dadofuncionario = funcionarioModel::get(!$id_funcionario?$firstFuncionario:$id_funcionario);

        $filter->addFilter(6,$elements->select("Funcionario","funcionario",$Dadofuncionario->id));

        $filter->show();

        $agenda = new agenda();
        $agenda->addButton($elements->button("Voltar","voltar","button","btn btn-primary w-100 btn-block","location.href='".$this->url."home'"));
        $agenda->show(
            $this->url."agendamento/manutencao/".$parameters[0]."/".functions::encrypt(!$id_funcionario?$firstFuncionario:$id_funcionario)."/",
            agendamentoModel::getEventsbyFuncionario(date("Y-m-d H:i:s",strtotime("-1 Year")),date("Y-m-d H:i:s",strtotime("+1 Year")),$id_agenda,$Dadofuncionario->id),
            $Dadofuncionario->dias?:"seg,ter,qua,qui,sex",
            $Dadofuncionario->hora_ini?:"08:00",
            $Dadofuncionario->hora_fim?:"18:00",
            $Dadofuncionario->hora_almoco_ini?:"12:00",
            $Dadofuncionario->hora_almoco_fim?:"13:30"
        );
      
        $footer = new footer;
        $footer->show();
    }

    public function listagem($parameters){

        $head = new head();
        $head -> show("Agendamentos","consulta");

        $elements = new elements;

        $cadastro = new consulta(true);

        $user = usuarioModel::getLogged();

        $id_agenda = intval($this->getValue("agenda"));
        $id_funcionario = intval($this->getValue("funcionario"));
        $dt_ini = $this->getValue("dt_ini");
        $dt_fim = $this->getValue("dt_fim");

        if ($user->tipo_usuario != 3){
            $funcionarios = funcionarioModel::getByUsuario($user->id);
            $agendas = agendaModel::getByUsuario($user->id);
        }else{
            $funcionarios = funcionarioModel::getByEmpresa($user->id);
            $agendas = agendaModel::getByEmpresa($user->id_empresa);
        }

        $filter = new filter($this->url."agendamento/listagem");
        $filter->addbutton($elements->button("Buscar","buscar","submit","btn btn-primary pt-2"));

        if ($agendas) {
            $elements->addOption("", "Selecione/Todos");
            foreach ($agendas as $agenda) {
                $elements->addOption($agenda->id, $agenda->nome);
            }
            $agenda = $elements->select("Agenda:", "agenda", $id_agenda);

            $filter->addFilter(3, $agenda);
        }

        if ($funcionarios) {
            $elements->addOption("", "Selecione/Todos");
            foreach ($funcionarios as $funcionario){
                $elements->addOption($funcionario->id,$funcionario->nome);
            }

            $filter->addFilter(3,$elements->select("Funcionario","funcionario",$id_funcionario));
        }

        $filter->addFilter(3, $elements->input("dt_ini","Data Inicial:",$dt_ini,false,false,"","datetime-local","form-control form-control-date"));
        $filter->addFilter(3, $elements->input("dt_fim","Data Final:",$dt_fim,false,false,"","datetime-local","form-control form-control-date"));

        $filter->show();

        $cadastro->addButtons($elements->button("Voltar","voltar","button","btn btn-primary","location.href='".$this->url."opcoes'")); 
        $cadastro->addButtons($elements->button("Cancelar Agendamento","agendamentocancel","button","btn btn-primary","location.href='".$this->url."agendamento/massCancel'"));
        
        $cadastro->addColumns("1","Id","id")
                ->addColumns("10","CPF/CNPJ","cpf_cnpj")
                ->addColumns("15","Nome","nome")
                ->addColumns("15","Email","email")
                ->addColumns("10","Telefone","telefone")
                ->addColumns("10","Agenda","agenda")
                ->addColumns("10","Funcionario","agenda")
                ->addColumns("12","Data Inicial","dt_ini")
                ->addColumns("12","Data Final","dt_fim")
                ->addColumns("10","Status","status")
                ->addColumns("14","Ações","acoes");

        if ($user->tipo_usuario != 3){
            $dados = agendamentoModel::prepareList(agendamentoModel::getAgendamentosByEmpresa($user->id_empresa,$dt_ini,$dt_fim,false,$id_agenda,$id_funcionario,$this->getLimit(),$this->getOffset()));
            $count = agendamentoModel::getLastCount("getAgendamentosByEmpresa");
        }else{
            $dados =  agendamentoModel::prepareList(agendamentoModel::getAgendamentosByUsuario($user->id,$dt_ini,$dt_fim,false,$id_agenda,$id_funcionario,$this->getLimit(),$this->getOffset()));
            $count = agendamentoModel::getLastCount("getAgendamentosByUsuario");
        }

        $cadastro->show($this->url."agendamento/manutencao",$this->url."agendamento/action",$dados,"id",$this->getLimit(),$count);
      
        $footer = new footer;
        $footer->show();
    }

    public function massCancel($parameters = []){
        try{

            transactionManeger::init();

            transactionManeger::beginTransaction();

            $qtd_list = $this->getValue("qtd_list");

            $mensagem = "Agendamentos cancelados com sucesso: ";
            $mensagem_erro = "Agendamentos não cancelados: ";

            if ($qtd_list){
                for ($i = 1; $i <= $qtd_list; $i++) {
                    if($id_agendamento = $this->getValue("id_check_".$i)){
                        if(agendamentoModel::cancel($id_agendamento))
                            $mensagem .= $id_agendamento." - ";
                        else
                            $mensagem_erro .= $id_agendamento." - ";
                    }
                }
            }
            else{
                mensagem::setErro("Não foi possivel encontrar o numero total de usuarios");
            }

        }catch(\Exception $e){
            mensagem::setSucesso(false);
            mensagem::setErro("Erro inesperado ocorreu, tente novamente");
            transactionManeger::rollback();
        }

        $this->go("agendamento/listagem");
    }
    
    public function manutencao($parameters){

        $head = new head;
        $head->show("Manutenção Agenda");

        $id = "";
        $dt_fim = "";
        $dt_ini = "";
        $id_funcionario = "";
        $id_agenda = "";

        $form = new form($this->url."agendamento/action/");
    
        if (array_key_exists(3,$parameters)){
            $dt_fim = functions::dateTimeBd(substr(base64_decode(str_replace("@","/",$parameters[3])),0,34));
            $dt_ini = functions::dateTimeBd(substr(base64_decode(str_replace("@","/",$parameters[2])),0,34));
        }
        elseif (!array_key_exists(3,$parameters) && array_key_exists(2,$parameters)){
           $id = functions::decrypt($parameters[2]);
           $form->setHidden("cd",$parameters[2]);
        }
        if (array_key_exists(1,$parameters) && array_key_exists(0,$parameters)){
            $form->setHidden("id_funcionario",$parameters[1]);
            $id_funcionario = functions::decrypt($parameters[1]);
            $id_agenda = functions::decrypt($parameters[0]);
            $form->setHidden("id_agenda",$id_agenda);
        }else{
            $this->go("home");
        }

        $elements = new elements;

        $dado = isset(session::get("agendamentoController")->agendamento)?session::get("agendamentoController")->agendamento:agendamentoModel::get($id);

        $user = usuarioModel::getLogged();

        if($user->tipo_usuario == 3)
            $statuses = statusModel::getUsuarioStatus();

        if ($user->tipo_usuario != 3){

            $statuses = statusModel::getAll();

            $usuarios = usuarioModel::getByTipoUsuarioAgenda(3,$id_agenda);

            $elements->addOption("","Selecionar/Vazio");
            foreach ($usuarios as $usuario){
                $elements->addOption($usuario->id,$usuario->nome);
            }

            $usuario = $elements->select("Usuario","usuario",$dado->id_usuario);

            $clientes = clienteModel::getByFuncionario($id_funcionario);

            $elements->addOption("","Selecionar/Vazio");
            foreach ($clientes as $cliente){
                $elements->addOption($cliente->id,$cliente->nome);
            }

            $cliente = $elements->select("Cliente","cliente",$dado->id_cliente);

            $agendas = [];

            if ($user->tipo_usuario < 2)
                $agendas = agendaModel::getByEmpresa($user->id_empresa);
            else 
                $agendas = agendaModel::getByUsuario($user->id);

            foreach ($agendas as $agenda){
                $elements->addOption($agenda->id,$agenda->nome);
            }

            $agenda = $elements->select("Agenda","agenda",$dado->id_agenda?:$id_agenda);

            $form->addCustomInput("1 col-sm-12 mb-2",$elements->input("cor","Cor:",$dado->cor?:"#4267b2",false,false,"","color","form-control form-control-color"))
                ->addCustomInput("9 col-sm-12 cliente mb-2",$cliente,"cliente")
                ->addCustomInput("2 col-sm-12 d-flex align-items-end mb-2",$elements->button("Novo","novoCliente","button"),"w-100")
                ->addCustomInput(6,$usuario)
                ->addCustomInput(6,$agenda)
                ->setCustomInputs();
        }

        foreach ($statuses as $status){
            $elements->addOption($status->id,$status->nome);
        }
        $status = $elements->select("Status","status",$dado->id_status);


        $form->addCustomInput(12,$status)->setCustomInputs()
        ->addCustomInput("6",$elements->input("dt_ini","Data Inicial:",$dado->dt_ini?:$dt_ini,true,true,"","datetime-local","form-control form-control-date"),"dt_ini")
        ->addCustomInput("6",$elements->input("dt_fim","Data Final:",$dado->dt_fim?:$dt_fim,true,true,"","datetime-local","form-control form-control-date"),"dt_fim")
        ->setCustomInputs();

        $Dadofuncionario = funcionarioModel::get($id_funcionario);
        
        $form->setInputs($elements->label("Serviços"));

        $i = 0;
        $servicos = servicoModel::getByFuncionario($Dadofuncionario->id);
        if ($this->isMobile()){
            $table = new tabelaMobile();
            $table->addColumns("1","Selecionar","massaction");
        }else {
            $table = new tabela();
            $table->addColumns("1","","massaction");
        }
        $table->addColumns("68","Nome","nome");
        $table->addColumns("10","Quantidade","qtd_item");
        $table->addColumns("10","Tempo","tempo_item");
        $table->addColumns("12","Total","total_item");

        $exists = false;
        if($servicos){
            foreach ($servicos as $servico){
                
                if($dado->id && $servico->id)
                    $agendaItem = (isset(session::get("agendamentoController")->agendaItem))?session::get("agendamentoController")->agendaItem:agendamentoItemModel::getItemByServico($dado->id,$servico->id);
                else 
                    $agendaItem = null;

                if (isset($agendaItem->id_servico) && $agendaItem->id_servico == $servico->id){
                    $form->setHidden("id_item_".$i,$agendaItem->id);
                    $servico->massaction =  $elements->checkbox("servico_index_".$i,"",false,$agendaItem->id_servico?true:false,false,$agendaItem->id_servico,"checkbox","form-check-input check_item",'data-index-check="'.$i.'"');
                    $servico->qtd_item = $elements->input("qtd_item_".$i,"",$agendaItem->qtd_item,false,false,"","number","form-control qtd_item",'min="1" data-index-servico="'.$i.'"');
                    $servico->tempo_item =  $elements->input("tempo_item_".$i,"",$agendaItem->tempo_item,false,true,"","text","form-control",'data-vl-base="'.$servico->tempo.'"');
                    $servico->total_item =   $elements->input("total_item_".$i,"",functions::formatCurrency($agendaItem->total_item),false,true,"","text","form-control",'data-vl-base="'.$servico->valor.'" data-vl-atual="'.$agendaItem->total_item.'"');
                    $table->addRow($servico->getArrayData());
                    $exists = true;
                }
                else{
                    $servico->massaction = $elements->checkbox("servico_index_".$i,"",false,false,false,$servico->id,"checkbox","form-check-input check_item",'data-index-check="'.$i.'"');
                    $servico->qtd_item = $elements->input("qtd_item_".$i,"",1,false,false,"","number","form-control qtd_item",'min="1" data-index-servico="'.$i.'"');
                    $servico->tempo_item = $elements->input("tempo_item_".$i,"",$servico->tempo,false,true,"","text","form-control",'data-vl-base="'.$servico->tempo.'"');
                    $servico->total_item = $elements->input("total_item_".$i,"",functions::formatCurrency($servico->valor),false,true,"","text","form-control",'data-vl-base="'.$servico->valor.'" data-vl-atual="'.$servico->valor.'"');
                    $table->addRow($servico->getArrayData()); 
                    $id?$exists = false:$exists = true;
                }
                $i++;
            }
        }
        $form->setInputs($table->parse());

        $form->setHidden("qtd_servico",$i);

        $form->setInputs($elements->textarea("obs","Observações:",$dado->obs,false,false,"","3","12"));

        $total = $dado->total?:0;

        $form->addCustomInput("1 col-2 d-flex align-items-end mb-2",$elements->label("Total"),"total");
        $form->addCustomInput("11 col-10",$elements->input("total","",$dado->total?functions::formatCurrency($dado->total):"R$ 0.00",false,true,"","text","form-control",'data-vl-total="'.$total.'"'));
        $form->setCustomInputs();

        $form->setButton($elements->button("Salvar","submit"));
        $form->setButton($elements->button("Voltar","voltar","button","btn btn-primary w-100 btn-block","location.href='".$this->url."agendamento/index/".$parameters[0]."/".$parameters[1]."'",!$exists?"disabled":""));
        
        $form->show();

        $footer = new footer;
        $footer->show();
    }
    public function action($parameters){

        if ($parameters){
            agendamentoItemModel::deleteByIdAgendamento($parameters[0]);
            agendamentoModel::delete($parameters[0]);
            $this->go("agendamento");
            return;
        }

        $id = functions::decrypt($this->getValue('cd'));
        $dt_ini = $this->getValue('dt_ini');
        $dt_fim = $this->getValue('dt_fim');
        $qtd_servico = intval($this->getValue("qtd_servico"));
        $status = intval($this->getValue("status"))?:1;
        $id_agenda = $this->getValue("id_agenda"); 
        $id_funcionario = functions::decrypt($this->getValue("id_funcionario"));
        $cor = $this->getValue('cor');
        $obs = $this->getValue('obs');
        $exists = false;

        $agendamento = new \stdClass;
    
        $agendamento->agendamento = new \stdClass;
        $agendamento->agendamento->id                = $id;
        $agendamento->agendamento->dt_ini            = $dt_ini;
        $agendamento->agendamento->dt_ini            = $dt_fim;
        $agendamento->agendamento->id_agenda         = $id_agenda;
        $agendamento->agendamento->id_funcionario    = $id_funcionario;
        $agendamento->agendamento->qtd_servico       = $qtd_servico;
        $agendamento->agendamento->status            = $status;
        $agendamento->agendamento->cor               = $cor;
        $agendamento->agendamento->obs               = $obs;
        
        $user = usuarioModel::getLogged();
        $id_agendamento = "";

        if ($user->tipo_usuario != 3 && $cliente = $this->getValue('cliente')){
            $id_cliente = "";
            if (intval($cliente))
                $id_cliente = $cliente;
            else 
                $id_cliente = clienteModel::set($cliente,$id_funcionario);

            $cliente = clienteModel::get($id_cliente);

            if (isset($cliente->id))
                $id_agendamento = agendamentoModel::set($id_agenda,$id_funcionario,$cliente->nome,$dt_ini,$dt_fim,0,$status,$cor,$obs,null,$cliente->id,$id);
        }
        elseif($user->tipo_usuario == 3) 
            $id_agendamento = agendamentoModel::set($id_agenda,$id_funcionario,$user->nome,$dt_ini,$dt_fim,0,$status,$cor,$obs,$user->id,null,$id);
        elseif($usuario = $this->getValue('usuario')){
            $usuario = usuarioModel::get($usuario);
            if ($usuario)
                $id_agendamento = agendamentoModel::set($id_agenda,$id_funcionario,$usuario->nome,$dt_ini,$dt_fim,0,$status,$cor,$obs,$usuario->id,null,$id);
        }
        else{
            mensagem::setErro("Selecione um usuario ou cliente");
        }

        if ($id_agendamento){
            if ($qtd_servico){

                $agendamento->agendaItems  = [];

                for ($i = 0; $i <= $qtd_servico; $i++) {
                    $id_servico = $this->getValue('servico_index_'.$i);
                    $qtd_item = $this->getValue('qtd_item_'.$i);
                    $tempo_item = $this->getValue('tempo_item_'.$i);
                    $id_agendamento_item = $this->getValue('id_item_'.$i);
                    if($id_servico && $qtd_item && $tempo_item){
                        $exists = true;
                        agendamentoItemModel::set($qtd_item,$id_agendamento,$id_servico,$id_agendamento_item);
                    }
                    elseif ($id_agendamento_item && !$id_servico){
                        agendamentoItemModel::delete($id_agendamento_item);
                    }
                    $id_servico = $qtd_item = $tempo_item = $id_agendamento_item = null;

                    $agendaItem = new \stdClass;
    
                    $agendaItem->id               = $id_servico;
                    $agendaItem->qtd_item         = $qtd_item;
                    $agendaItem->tempo            = $tempo_item;

                    $agendamento->agendaItems[]   = $agendaItem;
                }

                agendamentoModel::setTotal($id_agendamento);
            }
    
            if (!$exists){
                mensagem::setErro("Selecione ao menos um serviço");
                mensagem::setSucesso(false);
                $this->go("agendamento/manutencao/".functions::encrypt($id_agenda)."/".functions::encrypt($id_funcionario)."/".functions::encrypt($id?:$id_agendamento));
            }
        }

        session::set("agendamentoController",$agendamento);

        if(!mensagem::getErro()){
            session::set("agendamentoController",false);
            mensagem::setSucesso("Agendamento Concluido");
        }else
            mensagem::setSucesso(false);

        $this->go("agendamento/index/".functions::encrypt($id_agenda)."/".functions::encrypt($id_funcionario));
    }
}