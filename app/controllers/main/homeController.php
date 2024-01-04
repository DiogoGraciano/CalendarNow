<?php 
namespace app\controllers\main;
use app\classes\head;
use app\classes\form;
use app\classes\lista;
use app\classes\elements;
use app\classes\footer;
use app\classes\controllerAbstract;
use app\models\main\loginModel;

class homeController extends controllerAbstract{

    public function index(){

        $head = new head();
        $head->show("Home","");

        $elements = new elements;

        $lista = new lista();
        $objetos = array($lista->getObjeto($this->url."agenda","Agendamento"),
                       $lista->getObjeto($this->url."cliente","Cliente"),
                       $lista->getObjeto($this->url."conexao","Conexão"),
                       $lista->getObjeto($this->url."ramal","Ramal"),
                       $lista->getObjeto($this->url."usuario","Usuario"),
                       $lista->getObjeto($this->url."tabela","Exportar/Importar"),
                       $lista->getObjeto($this->url."home/deslogar","Deslogar"));

        $buttons = [
            $elements->button("Voltar","voltar","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."login'"),
            $elements->button("Voltar","voltar","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."login'")
        ]; 
        
        $lista->setLista("Agendas",$objetos);

        $lista->setButtons($buttons);
                       
        $lista->show();

        $footer = new footer;
        $footer->show();
    }
    public function deslogar(){
        loginModel::deslogar();
        $this->go("login");
    }
}