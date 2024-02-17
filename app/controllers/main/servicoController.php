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

class servicoController extends controllerAbstract{

    private $funcionario = "";

    public function index(){
        $head = new head();
        $head -> show("serviços","consulta");

        $elements = new elements;

        $user = usuarioModel::getLogged();

        $filter = new filter($this->url."servico/filter/");
        $filter->addbutton($elements->button("Buscar","buscar","submit","btn btn-primary pt-2"));

        $filter->addFilter(6,[$elements->input("pesquisa","Pesquisa:")]);

        $user = usuarioModel::getLogged();

        $funcionarios = funcionarioModel::getByEmpresa($user->id_empresa);

        if ($funcionarios){
            $i = 1;
            $firstFuncionario = "";
            foreach ($funcionarios as $funcionario){
                if ($i == 1){
                    $firstFuncionario = $funcionario->id;
                }
                $elements->addOption($funcionario->id,$funcionario->nome);
            }

            $modal = new modal($this->url."servico/massActionFuncionario/");

            $modal->setinputs($elements->select("Funcionario","funcionario",$this->$funcionario=""?$firstFuncionario:$this->$funcionario));

            $modal->show();

            foreach ($funcionarios as $funcionario){
                if ($i == 1){
                    $firstFuncionario = $funcionario->id;
                }
                $elements->addOption($funcionario->id,$funcionario->nome);
            }

            $filter->addFilter(6,[$elements->select("Funcionario","funcionario",$this->$funcionario=""?$firstFuncionario:$this->$funcionario)]);
        }

        $filter->show();

        $servico = new consulta();

        $servico->addButtons($elements->button("Voltar","voltar","button","btn btn-primary","location.href='".$this->url."opcoes'")); 

        $servico->addColumns("1","Id","id")
                ->addColumns("60","Nome","nome")
                ->addColumns("5","Tempo","tempo")
                ->addColumns("10","Valor","valor")
                ->addColumns("11","Ações","acoes")

        ->show($this->url."servico/manutencao/",$this->url."servico/action/",servicoModel::getByEmpresa($user->id_empresa),"id",true);
      
        $footer = new footer;
        $footer->show();
    }
    public function manutencao($parameters){

        $cd = "";

        $head = new head;
        $head->show("Manutenção Serviço");
        
        $form = new form($this->url."servico/action/");

        $elements = new elements;

        if (array_key_exists(0,$parameters)){
            $cd = functions::decrypt($parameters[0]);
            $form->setHidden("cd",$parameters[0]);
        }
        
        $dado = servicoModel::get($cd);
        
        $elements->setOptions("grupo_servico","id","nome");
        $id_grupo_servico = $elements->select("Grupo de Servicos:","id_grupo_servico");

        $elements->setOptions("funcionario","id","nome");

        $form->setDoisInputs(
            $elements->input("nome","Nome:",$dado->nome,true),
            $id_grupo_servico,
            array("nome","id_grupo_servico")
        );

        $form->setDoisInputs(
            $elements->input("tempo","Tempo de Trabalho:",$dado->tempo==""?"00:30:00":functions::formatTime($dado->tempo),true,false,"","time"),
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
        $tempo  = $this->getValue('tempo');
        $valor  = $this->getValue('valor');

        if ($id_servico = servicoModel::set($nome,$valor,$tempo,$user->id_empresa,$id) && $id_grupo_servico && !$id){ 
            servicoModel::setServicoGrupoServico($id_grupo_servico,$id_servico);
        }

        $this->go("servico");
    }
}