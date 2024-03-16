<?php 
namespace app\controllers\main;
use app\classes\head;
use app\classes\form;
use app\classes\modal;
use app\classes\consulta;
use app\classes\controllerAbstract;
use app\classes\elements;
use app\classes\footer;
use app\classes\functions;
use app\classes\filter;
use app\models\main\agendaModel;
use app\models\main\servicoModel;
use app\models\main\usuarioModel;
use app\models\main\funcionarioModel;
use app\models\main\grupoServicoModel;

class servicoController extends controllerAbstract{

    public function index($parameters = []){

        $id_funcionario = "";

        if($parameters && array_key_exists(0,$parameters)){
            $id_funcionario = functions::decrypt($parameters[0]);
        }

        $head = new head();
        $head -> show("serviços","consulta");

        $elements = new elements;

        $user = usuarioModel::getLogged();

        $filter = new filter($this->url."servico/filter/");
        $filter->addbutton($elements->button("Buscar","buscar","submit","btn btn-primary pt-2"));

        $filter->addFilter(4,[$elements->input("pesquisa","Pesquisa:")]);

        $funcionarios = funcionarioModel::getByEmpresa($user->id_empresa);

        if ($funcionarios){
            $i = 1;
            $firstFuncionario = "";
            $elements->addOption("","Selecione/Todos");
            foreach ($funcionarios as $funcionario){
                if ($i == 1){
                    $firstFuncionario = $funcionario->id;
                    $i++;
                }
                $elements->addOption($funcionario->id,$funcionario->nome);
            }

            $funcionarios = $elements->select("Funcionario","funcionario",$id_funcionario=""?$firstFuncionario:$id_funcionario);

            $modal = new modal($this->url."servico/massActionFuncionario/","massActionFuncionario");

            $modal->setinputs($funcionarios);

            $modal->setButton($elements->button("Salvar","submitModalConsulta","button","btn btn-primary w-100 pt-2 btn-block"));

            $modal->show();

            $filter->addFilter(4,[$funcionarios]);
        }

        $grupo_servicos = grupoServicoModel::getByEmpresa($user->id_empresa);

        if ($grupo_servicos){

            $elements->addOption("","Selecione/Todos");
            foreach ($grupo_servicos as $grupo_servico){
                $elements->addOption($grupo_servico->id,$grupo_servico->nome);
            }

            $grupo_servico = $elements->select("Grupo Serviço","grupo_servico");

            $modal = new modal($this->url."servico/massActionGrupoServico/","massActionGrupoServico");

            $modal->setinputs($grupo_servico);

            $modal->setButton($elements->button("Salvar","submitModalConsulta","button","btn btn-primary w-100 pt-2 btn-block"));

            $modal->show();

            $filter->addFilter(4,[$grupo_servico]);
        }

        $filter->show();

        $servico = new consulta();

        $servico->addButtons($elements->button("Voltar","voltar","button","btn btn-primary","location.href='".$this->url."opcoes'")); 
        $servico->addButtons($elements->button("Adicionar Serviço ao Funcionario","openModelFuncionario","button","btn btn-primary","openModal('massActionFuncionario')")); 
        $servico->addButtons($elements->button("Adicionar Serviço ao Grupo","openModelGrupoServico","button","btn btn-primary","openModal('massActionGrupoServico')")); 
        

        $data = [];

        if ($id_funcionario)
            $data = servicoModel::getByFuncionario($id_funcionario);
        else 
            $data = servicoModel::getByEmpresa($user->id_empresa);

        $servico->addColumns("1","Id","id")
                ->addColumns("60","Nome","nome")
                ->addColumns("5","Tempo","tempo")
                ->addColumns("10","Valor","valor")
                ->addColumns("11","Ações","acoes")
                ->show($this->url."servico/manutencao/",$this->url."servico/action/",$data,"id",true);
      
        $footer = new footer;
        $footer->show();
    }
    public function manutencao($parameters){

        $id = "";

        $head = new head;
        $head->show("Manutenção Serviço");
        
        $form = new form($this->url."servico/action/");

        $elements = new elements;

        if (array_key_exists(0,$parameters)){
            $id = functions::decrypt($parameters[0]);
            $form->setHidden("cd",$parameters[0]);
        }
        
        $dado = servicoModel::get($id);
        
        $elements->setOptions("grupo_servico","id","nome");
        $id_grupo_servico = $elements->select("Grupo de Servicos:","id_grupo_servico");

        $elements->setOptions("funcionario","id","nome");

        $form->setDoisInputs(
            $elements->input("nome","Nome:",$dado->nome,true),
            $id_grupo_servico,
            array("nome","id_grupo_servico")
        );

        $form->setDoisInputs(
            $elements->input("tempo","Tempo de Trabalho:",$dado->tempo==""?"00:30":functions::formatTime($dado->tempo),true,true,"","time2",""),
            $elements->input("valor","Valor:",$dado->valor,true,false,""),
            array("tempo","valor")
        );

        $form->setButton($elements->button("Salvar","submit"));
        $form->setButton($elements->button("Voltar","voltar","button","btn btn-primary w-100 btn-block","location.href='".$this->url."servico'"));
        $form->show();

        $footer = new footer;
        $footer->show();
    }
    public function action($parameters){

        $user = usuarioModel::getLogged();

        if ($parameters){
            $id = functions::decrypt($parameters[0]);
            $agendas = agendaModel::getByUserServico($id,$user->id);
            if ($agendas){
                $agenda = $agendas[0];
            }
            servicoModel::deleteAgendaServico($agenda->id,$id);
            servicoModel::delete($id);
            $this->go("servico");
        }

        $id = functions::decrypt($this->getValue('cd'));
        $nome  = $this->getValue('nome');
        $id_grupo_servico  = $this->getValue('id_grupo_servico');
        $tempo  = $this->getValue('tempo');
        $valor  = $this->getValue('valor');

        if ($id_servico = servicoModel::set($nome,$valor,$tempo,$user->id_empresa,$id) && $id_grupo_servico && !$id){ 
            servicoModel::setServicoGrupoServico($id_grupo_servico,$id_servico);
        }

        $this->go("servico");
    }

    public function filter(){
        $this->go("servico/index/".functions::encrypt($this->getValue("funcionario")));
    }

    public function massActionFuncionario(){

        $qtd_list = $this->getValue("qtd_list");
        $id_funcionario = $this->getValue("funcionario");

        if ($qtd_list && $id_funcionario){
            for ($i = 1; $i <= $qtd_list; $i++) {
                if($id_servico = $this->getValue("id_check_".$i)){
                    servicoModel::setServicoFuncionario($id_servico,$id_funcionario);
                }
            }
        }

        $this->go("servico");
    }

    public function massActionGrupoServico(){

        $qtd_list = $this->getValue("qtd_list");
        $id_grupo_servico = $this->getValue("grupo_servico");

        if ($qtd_list && $id_grupo_servico){
            for ($i = 1; $i <= $qtd_list; $i++) {
                if($id_servico = $this->getValue("id_check_".$i)){
                    ServicoModel::setServicoGrupoServico($id_servico,$id_grupo_servico);
                }
            }
        }

        $this->go("servico");
    }
}