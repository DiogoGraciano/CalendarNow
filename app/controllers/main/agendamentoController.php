<?php 
namespace app\controllers\main;
use app\classes\head;
use app\classes\form;
use app\classes\agenda;
use app\classes\controllerAbstract;
use app\classes\elements;
use app\classes\filter;
use app\classes\footer;
use app\classes\tabela;
use app\classes\tabelaMobile;
use app\classes\functions;
use app\classes\Logger;
use app\classes\mensagem;
use app\models\main\agendamentoItemModel;
use app\models\main\agendamentoModel;
use app\models\main\agendaModel;
use app\models\main\funcionarioModel;
use app\models\main\servicoModel;
use app\models\main\usuarioModel;
use app\models\main\clienteModel;
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
            $id_funcionario = functions::decrypt($parameters[1]);

        $elements = new elements;

        $filter = new filter($this->url."agendamento/filter/".$parameters[0]);
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

        $Dadofuncionario = funcionarioModel::get($id_funcionario==""?$firstFuncionario:$id_funcionario);

        $filter->addFilter(6,[$elements->select("Funcionario","funcionario",$Dadofuncionario->id)]);

        $filter->show();

        $agenda = new agenda();
        $agenda->addButton($elements->button("Voltar","voltar","button","btn btn-primary w-100 btn-block","location.href='".$this->url."home'"));
        $agenda->show(
            $this->url."agendamento/manutencao/".$parameters[0]."/".functions::encrypt($id_funcionario==""?$firstFuncionario:$id_funcionario)."/",
            agendamentoModel::getEventsbyFuncionario(date("Y-m-d H:i:s",strtotime("-1 Year")),date("Y-m-d H:i:s",strtotime("+1 Year")),$id_agenda,$Dadofuncionario->id),
            $Dadofuncionario->dias,
            $Dadofuncionario->hora_ini,
            $Dadofuncionario->hora_fim,
            $Dadofuncionario->hora_almoco_ini,
            $Dadofuncionario->hora_almoco_fim
        );
      
        $footer = new footer;
        $footer->show();
    }
    
    public function filter($parameters){
        $this->go("agendamento/index/".$parameters[0]."/".functions::encrypt($this->getValue("funcionario")));
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

        $dado = agendamentoModel::get($id);

        $user = usuarioModel::getLogged();

        $elements->addOption("0","Agendado");
        $elements->addOption("99","Cancelado");
        $status = $elements->select("Status","status",$dado->status);

        if ($user->tipo_usuario != 3){

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
                $agendas = agendaModel::getByUser($user->id);

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

        $form->addCustomInput(12,$status)
        ->addCustomInput("6",$elements->input("dt_ini","Data Inicial:",$dado->dt_ini?:$dt_ini,true,true,"","datetime-local","form-control form-control-date"),"dt_ini")
        ->addCustomInput("6",$elements->input("dt_fim","Data Final:",$dado->dt_fim?:$dt_fim,true,true,"","datetime-local","form-control form-control-date"),"dt_fim")
        ->setCustomInputs();

        $Dadofuncionario = funcionarioModel::get($id_funcionario);
        
        $form->setInputs($elements->label("Serviços"));

        $i = 0;
        $servicos = servicoModel::getByFuncionario($Dadofuncionario->id);
        if ($this->isMobile()){
        
            $table = new tabelaMobile();

            foreach ($servicos as $servico){
                $agendaItem = agendamentoItemModel::getItemByServico($dado->id,$servico->id);
                if (isset($agendaItem->id_servico) && $agendaItem->id_servico == $servico->id){
                    $form->setHidden("id_item_".$i,$agendaItem->id);
                    $table->addColumnsRows($elements->checkbox("servico_index_".$i,"",false,false,false,$agendaItem->id_servico,"checkbox","form-check-input check_item",'data-index-check="'.$i.'"'),"Selecionar");
                    $table->addColumnsRows($servico->nome,"Nome");
                    $table->addColumnsRows($elements->input("qtd_item_".$i,"",$agendaItem->qtd_item,false,false,"","number","form-control qtd_item",'min="1" data-index-servico="'.$i.'"'),"Quantidade");
                    $table->addColumnsRows($elements->input("tempo_item_".$i,"",$agendaItem->tempo_item,false,true,"","text","form-control",'data-vl-base="'.$servico->tempo.'"'),"Tempo");
                    $table->addColumnsRows($elements->input("total_item_".$i,"",functions::formatCurrency($agendaItem->total_item),false,true,"","text","form-control",'data-vl-base="'.$servico->valor.'" data-vl-atual="'.$agendaItem->total_item.'"'),"Total");
                }else{
                    $table->addColumnsRows($elements->checkbox("servico_index_".$i,"",false,isset($agendaItem->id_servico)?true:false,false,$agendaItem->id_servico,"checkbox","form-check-input check_item",'data-index-check="'.$i.'"'),"Selecionar");
                    $table->addColumnsRows($servico->nome,"Nome");
                    $table->addColumnsRows($elements->input("qtd_item_".$i,"",isset($agendaItem->qtd_item)?$agendaItem->qtd_item:1,false,false,"","number","form-control qtd_item",'min="1" data-index-servico="'.$i.'"'),"Quantidade");
                    $table->addColumnsRows($elements->input("tempo_item_".$i,"",isset($agendaItem->tempo_item)?$agendaItem->tempo_item:$servico->tempo,false,true,"","text","form-control",'data-vl-base="'.$servico->tempo.'"'),"Tempo");
                    $table->addColumnsRows($elements->input("total_item_".$i,"",functions::formatCurrency($agendaItem->total_item),false,true,"","text","form-control",'data-vl-base="'.$servico->valor.'" data-vl-atual="'.$agendaItem->total_item.'"'),"Total");
                }
                $i++;
            }
            $form->setInputs($table->parse());
        }else {
            $table = new tabela();

            $table->addColumns("1","");
            $table->addColumns("68","Nome");
            $table->addColumns("10","Quantidade");
            $table->addColumns("10","Tempo");
            $table->addColumns("12","Total");

            foreach ($servicos as $servico){
                $agendaItem = agendamentoItemModel::getItemByServico($dado->id,$servico->id);
                if (isset($agendaItem->id_servico) && $agendaItem->id_servico == $servico->id){
                    $form->setHidden("id_item_".$i,$agendaItem->id);
                    $table->addRow([
                        $elements->checkbox("servico_index_".$i,"",false,$agendaItem->id_servico?true:false,false,$agendaItem->id_servico,"checkbox","form-check-input check_item",'data-index-check="'.$i.'"'),
                        $servico->nome,
                        $elements->input("qtd_item_".$i,"",$agendaItem->qtd_item,false,false,"","number","form-control qtd_item",'min="1" data-index-servico="'.$i.'"'),
                        $elements->input("tempo_item_".$i,"",$agendaItem->tempo_item,false,true,"","text","form-control",'data-vl-base="'.$servico->tempo.'"'),
                        $elements->input("total_item_".$i,"",functions::formatCurrency($agendaItem->total_item),false,true,"","text","form-control",'data-vl-base="'.$servico->valor.'" data-vl-atual="'.$agendaItem->total_item.'"')
                    ]);
                }
                else{
                    $table->addRow([
                        $elements->checkbox("servico_index_".$i,"",false,false,false,$servico->id,"checkbox","form-check-input check_item",'data-index-check="'.$i.'"'),
                        $servico->nome,
                        $elements->input("qtd_item_".$i,"",1,false,false,"","number","form-control qtd_item",'min="1" data-index-servico="'.$i.'"'),
                        $elements->input("tempo_item_".$i,"",$servico->tempo,false,true,"","text","form-control",'data-vl-base="'.$servico->tempo.'"'),
                        $elements->input("total_item_".$i,"",functions::formatCurrency($servico->valor),false,true,"","text","form-control",'data-vl-base="'.$servico->valor.'" data-vl-atual="'.$servico->valor.'"')
                    ]); 
                }
                $i++;
            }
            $form->setInputs($table->parse());
        }

        $form->setHidden("qtd_servico",$i);

        $form->setInputs($elements->textarea("obs","Observações:",$dado->obs,false,false,"","3","12"));

        $total = $dado->total?:0;

        $form->addCustomInput("1 col-2 d-flex align-items-end mb-2",$elements->label("Total"),"total");
        $form->addCustomInput("11 col-10",$elements->input("total","",$dado->total?functions::formatCurrency($dado->total):"R$ 0.00",false,true,"","text","form-control",'data-vl-total="'.$total.'"'));
        $form->setCustomInputs();

        $form->setButton($elements->button("Salvar","submit"));
        $form->setButton($elements->button("Voltar","voltar","button","btn btn-primary w-100 btn-block","location.href='".$this->url."agendamento/index/".$parameters[0]."/".$parameters[1]."'"));
        
        $form->show();

        $footer = new footer;
        $footer->show();
    }
    public function action($parameters){

        if ($parameters){
            agendamentoModel::delete($parameters[0]);
            $this->go("home");
            return;
        }

        $erro = false;
        $id = functions::decrypt($this->getValue('cd'));
        $dt_ini = $this->getValue('dt_ini');
        $dt_fim = $this->getValue('dt_fim');
        $qtd_servico = intval($this->getValue("qtd_servico"));
        $status = $this->getValue("status");
        $id_agenda = $this->getValue("id_agenda"); 
        $id_funcionario = functions::decrypt($this->getValue("id_funcionario"));
        $array_itens = []; 
        $total = 0;
        if ($qtd_servico){
            for ($i = 0; $i <= $qtd_servico; $i++) {
                $objeto_item = false;
                $id_servico = $this->getValue('servico_index_'.$i);
                $qtd_item = $this->getValue('qtd_item_'.$i);
                $tempo_item = $this->getValue('tempo_item_'.$i);
                $total_item = $this->getValue('total_item_'.$i);
                $id_agendamento_item = $this->getValue('id_item_'.$i);
                if($id_servico && $qtd_item && $tempo_item && $total_item){
                    $objeto_item = new stdClass;
                    $objeto_item->id = $id_agendamento_item;
                    $objeto_item->id_servico = $id_servico;
                    $objeto_item->qtd_item = $qtd_item;
                    $objeto_item->tempo_item = $tempo_item;
                    $objeto_item->total_item = functions::removeCurrency($total_item);
                    $total = $total + $objeto_item->total_item;
                }
                elseif ($id_agendamento_item && !$id_servico){
                    agendamentoItemModel::delete($id_agendamento_item);
                }
                if ($objeto_item)
                    $array_itens[] = $objeto_item;
                $id_servico = $qtd_item = $tempo_item = $total_item = $id_agendamento_item = null;
            }
        }

        if (!$array_itens){
            mensagem::setErro("Selecione ao menos um serviço");
            $this->go("agendamento/manutencao/".functions::encrypt($id_agenda)."/".functions::encrypt($id_funcionario)."/".functions::encrypt($id));
        }

        $cor = $this->getValue('cor');
        $obs = $this->getValue('obs');

        $user = usuarioModel::getLogged();
        $id_agendamento = "";

        if ($user->tipo_usuario != 3 && $cliente = $this->getValue('cliente')){
            $id_cliente = "";
            if (intval($cliente))
                $id_cliente = $cliente;
            else 
                $id_cliente = clienteModel::set($cliente,$user->id_empresa);

            $cliente = clienteModel::get($id_cliente);
            if ($cliente)
                $id_agendamento = agendamentoModel::set($id_agenda,null,$cliente->id,$id_funcionario,$cliente->nome,$dt_ini,$dt_fim,$cor,$obs,$total,$status,$id);
        }
        elseif($user->tipo_usuario == 3) 
            $id_agendamento = agendamentoModel::set($id_agenda,$user->id,null,$id_funcionario,$user->nome,$dt_ini,$dt_fim,$cor,$obs,$total,$status,$id);
        elseif($usuario = $this->getValue('usuario')){
            $usuario = usuarioModel::get($usuario);
            if ($usuario)
                $id_agendamento = agendamentoModel::set($id_agenda,$usuario->id,null,$id_funcionario,$usuario->nome,$dt_ini,$dt_fim,$cor,$obs,$total,$status,$id);
        }

        if ($id_agendamento){
            if(!agendamentoItemModel::setMultiple($array_itens,$id_agendamento))
                $erro = agendamentoModel::delete($id_agendamento);
        }
        else 
            $erro = true;

        if ($erro)
            mensagem::setErro("Falha ao Agendar, tente novamente");
        else 
            mensagem::setSucesso("Agendamento Concluido");

        $this->go("agendamento/index/".functions::encrypt($id_agenda)."/".functions::encrypt($id_funcionario));
    }
}