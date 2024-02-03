<?php 
namespace app\controllers\main;
use app\classes\head;
use app\classes\form;
use app\classes\elements;
use app\classes\controllerAbstract;
use app\classes\consulta;
use app\classes\footer;
use app\classes\functions;
use app\models\main\grupoFuncionarioModel;
use app\models\main\grupoServicoModel;

class gruposController extends controllerAbstract{

    public function index(){

        $head = new head();
        $head -> show("grupos","consulta");

        $elements = new elements;

        $buttons = [$elements->button("Voltar","voltar","button","btn btn-primary","location.href='".$this->url."opcoes'")]; 

        $cadastro = new consulta();

        $cadastro->addColumns("5","Id","id")
                ->addColumns("79","Nome","nome")
                ->addColumns("14","Ações","acoes");

        $dados = grupoServicoModel::getAll();
        $dados = array_merge(grupoFuncionarioModel::getAll());

        $cadastro->show($this->url."grupos/manutencao/",$this->url."grupos/action/",$buttons,$dados,"id","");
      
        $footer = new footer;
        $footer->show();
    }
    public function manutencao($parameters = array()){

        $head = new head();
        $head->show("Cadastro","");

        $cd = "";
        $tipo_grupo = "";
        
        $form = new form($this->url."grupos/action/");

        if (array_key_exists(0,$parameters)){
            $form->setHidden("tipo_grupo",$parameters[0]);
            $tipo_grupo = functions::decrypt($parameters[0]);
        }

        if (array_key_exists(1,$parameters)){
            $form->setHidden("cd",$parameters[1]);
            $cd = functions::decrypt($parameters[1]);
        }

        $elements = new elements;

        if ($tipo_grupo == "grupo_funcionario")
            $dado = grupoFuncionarioModel::get($cd);
        elseif ($tipo_grupo == "grupo_servico")
            $dado = grupoServicoModel::get($cd);
        else    
            $this->go($this->url."grupos");

        $form->setInputs(
            $elements->input("nome","Nome",$dado->nome,true),
        );
      
        $form->setButton($elements->button("Salvar","submit"));
        $form->setButton($elements->button("Voltar","voltar","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."grupos'"));

        $form->show();

        $footer = new footer;
        $footer->show();
    }
    public function action(){

       
    }

}

