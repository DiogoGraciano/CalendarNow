<?php 
namespace app\controllers\main;
use app\classes\head;
use app\classes\lista;
use app\classes\elements;
use app\classes\footer;
use app\classes\controllerAbstract;
use app\models\main\loginModel;
use app\models\main\agendaModel;
use app\models\main\usuarioModel;

class homeController extends controllerAbstract{

    public function index(){

        $head = new head();
        $head->show("Home","");

        $elements = new elements;

        $user = usuarioModel::getLogged();

        $agendas = agendaModel::getByUser($user->id);

        $lista = new lista();

        $objetos = [];

        if ($agendas){
            foreach ($agendas as $agenda){
                $objetos[] = $lista->getObjeto($this->url."agenda_horario/".$agenda->id,$agenda->nome." - ".$agenda->fantasia);
            }
        }

        $buttons = [
            $elements->button("Encontrar","encontrar","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."encontrar'"),
            $elements->button("Opções","opcao","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."opcoes'"),
            $elements->button("Sair","sair","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."home/deslogar'")
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