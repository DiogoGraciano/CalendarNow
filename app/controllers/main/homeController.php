<?php 
namespace app\controllers\main;
use app\classes\head;
use app\classes\lista;
use app\classes\elements;
use app\classes\footer;
use app\classes\controllerAbstract;
use app\classes\functions;
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

        if ($agendas){
            foreach ($agendas as $agenda){
               $lista->addObjeto($this->url."agendamento/index/".functions::encrypt($agenda->id),$agenda->nome." - ".$agenda->emp_nome);
            }
        }

        if ($user->tipo_usuario == 0 || $user->tipo_usuario == 1 || $user->tipo_usuario == 2){
            $lista->addButton($elements->button("Opções","opcao","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."opcoes'"));
            $lista->addButton($elements->button("Sair","sair","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."home/deslogar'"));
        }
        else{
            $lista->addButton($elements->button("Encontrar","encontrar","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."encontrar'"));
            $lista->addButton($elements->button("Opções","opcao","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."opcoes'"));
            $lista->addButton($elements->button("Sair","sair","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."home/deslogar'"));
        }
        
        $lista->setLista("Agendas");
          
        $lista->show();

        $footer = new footer;
        $footer->show();
    }
    public function deslogar(){
        loginModel::deslogar();
        $this->go("login");
    }
}