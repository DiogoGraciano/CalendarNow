<?php 
namespace app\controllers\main;
use app\classes\head;
use app\classes\form;
use app\classes\elements;
use app\classes\controllerAbstract;
use app\classes\consulta;
use app\classes\footer;
use app\classes\functions;
use app\classes\filter;
use app\models\main\grupoFuncionarioModel;
use app\models\main\grupoServicoModel;
use app\models\main\usuarioModel;

class gruposController extends controllerAbstract{

    public function index($parameters = array()){

        $nome = $this->getValue("nome");

        if (array_key_exists(0,$parameters)){
            $tipo_grupo = functions::decrypt($parameters[0]);
        }

        $user = usuarioModel::getLogged();

        if ($tipo_grupo == "grupo_funcionario")
            $dados = grupoFuncionarioModel::getByEmpresa($user->id_empresa,$nome);
        elseif ($tipo_grupo == "grupo_servico")
            $dados = grupoServicoModel::getByEmpresa($user->id_empresa,$nome);
        else    
            $this->go("home");

        $head = new head();
        $head -> show("grupos","consulta");

        $elements = new elements;

        
        $filter = new filter($this->url."agenda/index/");

        $filter->addbutton($elements->button("Buscar","buscar","submit","btn btn-primary pt-2"))
                ->addFilter(3,$elements->input("nome","Nome:",$nome));

        $filter->show();

        $cadastro = new consulta();

        $cadastro->addButtons($elements->button("Voltar","voltar","button","btn btn-primary","location.href='".$this->url."opcoes'")); 

        $cadastro->addColumns("1","Id","id")
                ->addColumns("90","Nome","nome")
                ->addColumns("10","Ações","acoes");

        $cadastro->show($this->url."grupos/manutencao/".functions::encrypt($tipo_grupo),$this->url."grupos/action/".functions::encrypt($tipo_grupo),$dados,"id");
      
        $footer = new footer;
        $footer->show();
    }
    public function manutencao($parameters = array()){

        $head = new head();
        $head->show("Cadastro","");

        $id = null;
        $tipo_grupo = null;
        
        if (array_key_exists(0,$parameters)){
            $tipo_grupo = functions::decrypt($parameters[0]);
            $form->setHidden("tipo_grupo",$tipo_grupo);
        }else 
            $this->go("home");

        $form = new form($this->url."grupos/action/".$parameters[0]);

        if (array_key_exists(1,$parameters)){
            $form->setHidden("cd",$parameters[1]);
            $id = intval(functions::decrypt($parameters[1]));
        }

        if ($tipo_grupo == "grupo_funcionario")
            $dado = grupoFuncionarioModel::get($id);
        elseif ($tipo_grupo == "grupo_servico")
            $dado = grupoServicoModel::get($id);
        else    
            $this->go("home");

        $elements = new elements;

        $form->setInputs(
            $elements->input("nome","Nome",$dado->nome,true),
        );
      
        $form->setButton($elements->button("Salvar","submit"));
        $form->setButton($elements->button("Voltar","voltar","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."grupos/index/'"));

        $form->show();

        $footer = new footer;
        $footer->show();
    }
    public function action($parameters){

        $id = $this->getValue("cd");
        $tipo_grupo = $this->getValue("tipo_grupo");

        if ($tipo_grupo == "grupo_funcionario"){
            if ($id){
                grupoFuncionarioModel::delete($id);
            }
            else{
                $id = functions::decrypt($this->getValue('cd'));
                $nome  = $this->getValue('nome');
                grupoFuncionarioModel::set($nome,$id);
            } 
        }elseif ($tipo_grupo == "grupo_servico"){
            if ($id){
                grupoServicoModel::delete($id);
            }
            else{
                $id = functions::decrypt($this->getValue('cd'));
                $nome  = $this->getValue('nome');
                grupoServicoModel::set($nome,$id);
            }

        }   
        
        $this->go("grupos/index/".functions::encrypt($tipo_grupo));
    }

}

