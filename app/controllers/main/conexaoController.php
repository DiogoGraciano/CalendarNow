<?php 
namespace app\controllers\main;
use app\classes\head;
use app\classes\form;
use app\classes\consulta;
use app\classes\controllerAbstract;
use app\classes\footer;
use app\models\main\conexaoModel;

class conexaoController extends controllerAbstract{

    public function index(){
        $head = new head();
        $head -> show("Conexão","consulta");

        $consulta = new consulta();
        $buttons = array($consulta->getButton($this->url."home","Voltar"));
        $columns = array($consulta->getColumns("15","NOME CLIENTE","nm_cliente"),
                        $consulta->getColumns("2","LOJA","nr_loja"),
                        $consulta->getColumns("10","CONEXÃO","id_conexao"),
                        $consulta->getColumns("10","TERMINAL","nm_terminal"),
                        $consulta->getColumns("2","CAIXA","nr_caixa"),
                        $consulta->getColumns("10","PROGRAMA","nm_programa"),
                        $consulta->getColumns("10","USUARIO","nm_usuario"),
                        $consulta->getColumns("10","SENHA","senha"),
                        $consulta->getColumns("10","OBSERVAÇÕES","obs"),
                        $consulta->getColumns("12.5","AÇÕES",""));
        
        $consulta->show("CONSULTA CONEXÃO",$this->url."conexao/manutencao",$this->url."conexao/action",$buttons,$columns,"tb_conexao","select cd_conexao,
        tb_conexao.cd_cliente,nm_cliente,nr_loja,id_conexao,nm_terminal,nr_caixa,nm_programa,nm_usuario,senha,obs 
        from tb_conexao 
        inner join tb_cliente on tb_cliente.cd_cliente = tb_conexao.cd_cliente");

        $footer = new footer;
        $footer->show();
    }
    public function manutencao($parameters){

        $cd = "";

        if ($parameters)
            $cd = $parameters[0];

        $head = new head;
        $head->show("Manutenção Conexão");

        $dado = conexaoModel::get($cd);

        $form = new form("Manutenção Conexão",$this->url."conexao/action/".$cd);

        $form->setHidden("cd",$cd);

        $Terminais = array($form->getObjectOption("Balcão","Balcão"),
                        $form->getObjectOption("Deposito","Deposito"),
                        $form->getObjectOption("Escritorio","Escritorio"),
                        $form->getObjectOption("Frente De Caixa","Frente De Caixa"),
                        $form->getObjectOption("Servidor APP","Servidor APP"),
                        $form->getObjectOption("Servidor Super","Servidor Super")
        );

        $Programas = array($form->getObjectOption("Anydesk","Anydesk"),
                        $form->getObjectOption("Teamviwer","Teamviwer"),
                        $form->getObjectOption("NetSuporte","NetSuporte"),
                        $form->getObjectOption("Ruskdesk","Ruskdesk"),
                        $form->getObjectOption("WTS","WTS"),
                        $form->getObjectOption("Radmin","Radmin"),
                        $form->getObjectOption("VNC","VNC")
        );

        $form->setDoisInputs($form->input("id_conexao","Conexão:",$dado->id_conexao,true),      
                            $form->input("nr_caixa","Caixa:",$dado->nr_caixa,false,"","number","form-control",'min="1"')
        );
        $form->setTresInputs($form->select("Cliente","cd_cliente",$form->getOptions("tb_cliente","cd_cliente","nm_cliente"),$dado->cd_cliente,true),
                            $form->datalist("Terminais","nm_terminal",$Terminais,$dado->nm_terminal,true),
                            $form->datalist("Programas","nm_programa",$Programas,$dado->nm_programa,true)
        );
        $form->setDoisInputs($form->input("nm_usuario","Nome Usuario:",$dado->nm_usuario),
                            $form->input("senha","Senha:",$dado->senha)
        );
        $form->setInputs($form->textarea("obs","Observações:",$dado->obs,false,false,"","3","12"));

        $form->setButton($form->button("Salvar","btn_submit"));
        $form->setButton($form->button("Voltar","btn_submit","button","btn btn-dark pt-2 btn-block","location.href='".$this->url."conexao'"));
        $form->show();

        $footer = new footer;
        $footer->show();
    }
    public function action($parameters){

        if ($parameters){
            conexaoModel::delete($parameters[0]);
            $this->go("conexao");
            return;
        }

        $cd_conexao = $this->getValue('cd');
        $cd_cliente = $this->getValue('cd_cliente');
        $id_conexao = $this->getValue('id_conexao');
        $nm_terminal = $this->getValue('nm_terminal');
        $nr_caixa = $this->getValue('nr_caixa');
        $nm_programa = $this->getValue('nm_programa');
        $nm_usuario = $this->getValue('nm_usuario');
        $senha = $this->getValue('senha');
        $obs = $this->getValue('obs');

        conexaoModel::set($cd_cliente,$id_conexao,$nm_terminal,$nr_caixa,$nm_programa,$nm_usuario,$senha,$obs,$cd_conexao);

        $this->go("cliente/manutencao/".$cd_conexao);
    }

    public function export(){
        $this->go("tabela/exportar/tb_conexao");
    }

}