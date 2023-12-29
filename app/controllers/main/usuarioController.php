<?php 
namespace app\controllers\main;
use app\classes\head;
use app\classes\form;
use app\classes\consulta;
use app\classes\controllerAbstract;
use app\classes\footer;
use app\models\main\usuarioModel;

class usuarioController extends controllerAbstract{

    public function index(){

        $head = new head();
        $head->show("Usuario","consulta");
    
        $consulta = new consulta();
        $buttons = array($consulta->getButton($this->url."home","Voltar"));
        $columns = array($consulta->getColumns("15","NOME CLIENTE","nm_cliente"),
                        $consulta->getColumns("2","LOJA","nr_loja"),
                        $consulta->getColumns("10","TERMINAL","nm_terminal"),
                        $consulta->getColumns("10","SISTEMA","nm_sistema"),
                        $consulta->getColumns("10","USUARIO","nm_usuario"),
                        $consulta->getColumns("10","SENHA","senha"),
                        $consulta->getColumns("10","OBSERVAÇÕES","obs"),
                        $consulta->getColumns("12.5","AÇÕES",""));
        
        $consulta->show("CONSULTA USUARIO",$this->url."usuario/manutencao",$this->url."usuario/action",$buttons,$columns,"tb_usuario","select 
        cd_usuario,tb_usuario.cd_cliente,nm_cliente,nr_loja,nm_usuario,nm_terminal,nm_sistema,senha,obs 
        from tb_usuario 
        inner join tb_cliente on tb_cliente.cd_cliente = tb_usuario.cd_cliente;");

        $footer = new footer;
        $footer->show();
    }
    public function manutencao($parameters){

        $cd = "";

        if ($parameters)
            $cd = $parameters[0];

        $head = new head;
        $head->show("Manutenção Usuario");

        $dado = usuarioModel::get($cd);

        $form = new form("Manutenção Usuario",$this->url."usuario/action/".$cd);

        $form->setHidden("cd",$cd);
        $form->setHidden("cd_enderenco",$dado->id_usuario);

        $Terminais = array($form->getObjectOption("Balcão","Balcão"),
                        $form->getObjectOption("Deposito","Deposito"),
                        $form->getObjectOption("Escritorio","Escritorio"),
                        $form->getObjectOption("Frente De Caixa","Frente De Caixa"),
                        $form->getObjectOption("Servidor APP","Servidor APP"),
                        $form->getObjectOption("Servidor Super","Servidor Super"),
                        $form->getObjectOption("Outro","Outro"),
        );

        $Sistemas = array($form->getObjectOption("Windows","Windows"),
                        $form->getObjectOption("Linux","Linux"),
                        $form->getObjectOption("Mac OS","Mac OS"),
                        $form->getObjectOption("TEF WEB","TEF WEB"),
                        $form->getObjectOption("Token Email","Token Email"),
                        $form->getObjectOption("Outro","Outro"),
        );

        $form->setTresInputs($form->select("Cliente","cd_cliente",$form->getOptions("tb_cliente","cd_cliente","nm_cliente"),true),
                            $form->datalist("Terminais","nm_terminal",$Terminais,$dado->nm_terminal,true),
                            $form->datalist("Sistema:","nm_sistema",$Sistemas,$dado->nm_sistema,true)
        );
        $form->setDoisInputs($form->input("nm_usuario","Nome Usuario:",$dado->nm_usuario,true),
                            $form->input("senha","Senha:",$dado->senha,true)
        );
        $form->setInputs($form->textarea("obs","Observações:",$dado->obs,false,false,"","3","12"));

        $form->setButton($form->button("Salvar","btn_submit"));
        $form->setButton($form->button("Voltar","btn_submit","button","btn btn-dark pt-2 btn-block","location.href='".$this->url."usuario'"));
        $form->show();

        $footer = new footer;
        $footer->show();
    }
    public function action($parameters){
    
        if ($parameters){
            usuarioModel::delete($parameters[0]);
            $this->go("usuario");
            return;
        }
        
        $cd = $this->getValue('cd');
        $cd_cliente = $this->getValue('cd_cliente');
        $nm_terminal = $this->getValue('nm_terminal');
        $nm_sistema = $this->getValue('nm_sistema');
        $nm_usuario = $this->getValue('nm_usuario');
        $senha = $this->getValue('senha');
        $obs = $this->getValue('obs');
        
        usuarioModel::set($cd_cliente,$nm_terminal,$nm_sistema,$nm_usuario,$senha,$obs,$cd);
    }

    public function export(){
        $this->go("tabela/exportar/tb_usuario");
    }
    
}

