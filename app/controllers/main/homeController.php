<?php 
namespace app\controllers\main;
use app\layout\head;
use app\layout\lista;
use app\layout\elements;
use app\layout\footer;
use app\controllers\abstract\controller;
use app\helpers\functions;
use app\models\main\loginModel;
use app\models\main\agendaModel;
use app\models\main\usuarioModel;

class homeController extends controller{

    public function index(){

        $head = new head();
        $head->show("Home","");

        $elements = new elements;

        $user = usuarioModel::getLogged();

        $agendas = agendaModel::getByUsuario($user->id);

        $lista = new lista();

        if ($agendas){
            foreach ($agendas as $agenda){
               $lista->addObjeto($this->url."agendamento/index/".functions::encrypt($agenda->id),$agenda->nome." - ".$agenda->emp_nome);
            }
        }

        if ($user->tipo_usuario != 3){
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