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
use app\classes\tabela;
use app\classes\tabelaMobile;
use app\models\main\grupoFuncionarioModel;
use app\models\main\grupoServicoModel;
use app\models\main\usuarioModel;

class gruposController extends controllerAbstract{

    public function index($parameters = array()){

        $nome = $this->getValue("nome");

        $tipo_grupo = null;

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
                ->addColumns("85","Nome","nome")
                ->addColumns("15","Ações","acoes");

        $cadastro->show($this->url."grupos/manutencao/".functions::encrypt($tipo_grupo),$this->url."grupos/action/".functions::encrypt($tipo_grupo),$dados,"id");
      
        $footer = new footer;
        $footer->show();
    }
    public function manutencao($parameters = []){

        $head = new head();
        $head->show("Cadastro","");

        $id = null;
        $tipo_grupo = null;

        if (array_key_exists(0,$parameters))
            $tipo_grupo = functions::decrypt($parameters[0]);
        else 
            $this->go("home");

        $form = new form($this->url."grupos/action/".$parameters[0]);
        
        if (array_key_exists(1,$parameters)){
            $form->setHidden("cd",$parameters[1]);
            $id = functions::decrypt($parameters[1]);
        }

        if ($tipo_grupo == "grupo_funcionario")
            $model = new grupoFuncionarioModel;
        elseif ($tipo_grupo == "grupo_servico")
            $model = new grupoServicoModel;
        else    
            $this->go("home");

        $dado = $this->getSessionVar("gruposController")?:$model::get($id);

        $elements = new elements;

        $form->setInputs(
            $elements->input("nome","Nome",$dado->nome,true)
        );

        if($dado->id && $vinculos = $model::getVinculados($dado->id)){

            $form->setInputs($elements->label("Funcionarios Vinculados"));

            $this->isMobile() ? $table = new tabelaMobile() : $table = new tabela();
            
            $table->addColumns("1","ID","id");
            $table->addColumns("90","Nome","nome");
            $table->addColumns("10","Ações","acoes");

            foreach ($vinculos as $vinculo){
                $vinculo->acoes = $elements->button("Desvincular", "desvincular", "button", "btn btn-primary w-100 pt-2 btn-block", "location.href='".$this->url."funcionario/desvincularGrupo/".functions::encrypt($dado->id)."/".functions::encrypt($vinculo->id)."'");
                $table->addRow($vinculo->getArrayData());
            }

            $form->setInputs($table->parse());
        }

        $form->setButton($elements->button("Salvar","submit"));
        $form->setButton($elements->button("Voltar","voltar","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."grupos/index/".$parameters[0]."'"));

        $form->show();

        $footer = new footer;
        $footer->show();
    }
    public function action($parameters){

        $cd = null;

        if (array_key_exists(0,$parameters))
            $tipo_grupo = functions::decrypt($parameters[0]);
        else 
            $this->go("home");

        if (array_key_exists(1,$parameters))
            $cd = functions::decrypt($parameters[1]);

        $id = intval(functions::decrypt($this->getValue("cd")));
        $nome  = $this->getValue('nome');

        $grupo = new \stdClass;
        $grupo->id   = $id;
        $grupo->nome = $nome;

        $this->setSessionVar("gruposController",$grupo);

        $id_empresa = UsuarioModel::getLogged()->id_empresa;

        if ($tipo_grupo == "grupo_funcionario"){
            if ($cd && !$nome)
                grupoFuncionarioModel::delete($cd);
            else
                grupoFuncionarioModel::set($nome,$id_empresa,$id);
            
        }elseif ($tipo_grupo == "grupo_servico"){
            if ($cd && !$nome)
                grupoServicoModel::delete($cd);
            else
                grupoServicoModel::set($nome,$id_empresa,$id);
        }   
        
        $this->go("grupos/index/".$parameters[0]);
    }

}

