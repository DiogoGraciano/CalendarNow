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
use app\models\main\enderecoModel;
use app\models\main\cidadeModel;
use app\models\main\empresaModel;

class clienteController extends controllerAbstract{

    public function index(){

        $head = new head();
        $head -> show("Clientes","consulta");

        $tipo_usuario = "";

        $elements = new elements;

        $cadastro = new consulta();

        $cadastro->addButtons($elements->button("Voltar","voltar","button","btn btn-primary","location.href='".$this->url."opcoes'")); 

        $cadastro->addColumns("1","Id","id")
                ->addColumns("10","CPF/CNPJ","cpf_cnpj")
                ->addColumns("15","Nome","nome")
                ->addColumns("15","Email","email")
                ->addColumns("11","Telefone","telefone")
                ->addColumns("17","Agendas","agendas")
                ->addColumns("17","Agendamentos","agendamentos")
                ->addColumns("14","Ações","acoes");

        $user = usuarioModel::getLogged();

        $dados = agendamentoModel::getAgendamentosByTipoUsuario($user->tipo_usuario);

        $cadastro->show($this->url."cadastro/manutencao/".functions::encrypt($tipo_usuario),$this->url."cadastro/action/",$dados);
      
        $footer = new footer;
        $footer->show();
    }
    public function manutencao($parameters){

        $cd="";

        if ($parameters)
            $cd = $parameters[0];

        $head = new head;
        $head->show("Manutenção Cliente");

        $dado = clienteModel::get($cd);
        
        $form = new form("Manutenção Cliente",$this->url."cliente/action/");

        $form->setHidden("cd",$cd);
        $form->setDoisInputs($form->input("nome","Nome:",$dado->nm_cliente,true),
                            $form->input("nrloja","Loja:",$dado->nr_loja,true,false,"","number","form-control",'min="1"')
        );
        $form->setButton($form->button("Salvar","btn_submit"));
        $form->setButton($form->button("Voltar","btn_submit","button","btn btn-dark pt-2 btn-block","location.href='".$this->url."cliente'"));
        $form->show();

        $footer = new footer;
        $footer->show();
    }
    public function action($parameters){

        if ($parameters){
            clienteModel::delete($parameters[0]);
            $this->go("cliente");
            return;
        }

        $cd = $this->getValue('cd');
        $nome = $this->getValue('nome');
        $nrloja = $this->getValue('nrloja');

        clienteModel::set($nome,$nrloja,$cd);

        $this->go("cliente/manutencao/".$cd);
        
    }
   
}