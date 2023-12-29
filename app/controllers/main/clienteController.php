<?php 
namespace app\controllers\main;
use app\classes\head;
use app\classes\form;
use app\classes\consulta;
use app\classes\footer;
use app\classes\controllerAbstract;
use app\models\main\clienteModel;

class clienteController extends controllerAbstract{

    public function index(){

        $head = new head();
        $head -> show("Cliente","consulta");

        $consulta = new consulta();
        $buttons = array($consulta->getButton($this->url."home","Voltar"),$consulta->getButton($this->url."cliente/export","Exportar"));
        $columns = array($consulta->getColumns("10","ID","cd_cliente"),$consulta->getColumns("67","Nome","nm_cliente"),$consulta->getColumns("10","Loja","nr_loja"),$consulta->getColumns("13","Ações",""));
        $consulta->show("CONSULTA CLIENTES",$this->url."cliente/manutencao",$this->url."cliente/action",$buttons,$columns,"tb_cliente");
    
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
    public function export(){
        
        if (clienteModel::export())
            $this->go('arquivos/Clientes.csv');
        else 
            $this->go("cliente");
    
    }
}