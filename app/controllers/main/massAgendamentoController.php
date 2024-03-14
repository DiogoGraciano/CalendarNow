<?php 
namespace app\controllers\main;
use app\classes\head;
use app\classes\form;
use app\classes\elements;
use app\classes\controllerAbstract;
use app\classes\consulta;
use app\classes\footer;
use app\classes\functions;
use app\models\main\usuarioModel;
use app\models\main\agendamentoModel;

class massAgendamentoController extends controllerAbstract{

    public function index(){

        $head = new head();
        $head -> show("Agendamentos","consulta");

        $tipo_usuario = "";

        $elements = new elements;

        $cadastro = new consulta();

        $cadastro->addButtons($elements->button("Voltar","voltar","button","btn btn-primary","location.href='".$this->url."opcoes'")); 

        $cadastro->addColumns("1","Id","id")
                ->addColumns("10","CPF/CNPJ","cpf_cnpj")
                ->addColumns("15","Nome","nome")
                ->addColumns("15","Email","email")
                ->addColumns("10","Telefone","telefone")
                ->addColumns("10","Agenda","agenda")
                ->addColumns("10","Funcionario","agenda")
                ->addColumns("15","Data Inicial","dt_ini")
                ->addColumns("15","Data Final","dt_fim")
                ->addColumns("14","Ações","acoes");

        $user = usuarioModel::getLogged();

        if ($user->tipo_usuario != 3)
            $dados = agendamentoModel::getAgendamentosByEmpresa($user->id_empresa);
        else 
            $dados = agendamentoModel::getAgendamentosByUsuario($user->id);

        $cadastro->show($this->url."cadastro/manutencao/".functions::encrypt($tipo_usuario),$this->url."cadastro/action/",$dados,"id",true,"massaction");
      
        $footer = new footer;
        $footer->show();
    }
    public function manutencao($parameters){

        (new agendamentoController)->manutencao($parameters);

    }
    public function action($parameters){

        (new agendamentoController)->action($parameters);
    }
    public function massaction(){

        
    }
   
}