<?php 
namespace app\controllers\main;
use app\classes\head;
use app\classes\menu;
use app\classes\elements;
use app\classes\functions;
use app\classes\footer;
use app\classes\controllerAbstract;
use app\models\main\loginModel;
use app\models\main\usuarioModel;

class opcoesController extends controllerAbstract{

    public function index(){

        $head = new head();
        $head->show("Home","");

        $elements = new elements;

        $user = usuarioModel::getLogged();

        $menu = new menu();

        if ($user->tipo_usuario == 0 || $user->tipo_usuario == 1){
            $menu->addButton($elements->button("Agendas","agenda","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."agenda'"))
            ->addButton($elements->button("Funcionarios","funcionario","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."funcionario'"))
            ->addButton($elements->button("Grupos","grupo","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."grupos'"))
            ->addButton($elements->button("Serviços","servico","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."funcionario'"))
            ->addButton($elements->button("Clientes","clientes","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."clientes'"))
            ->addButton($elements->button("Relatorios","relatorio","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."relatorio'"))
            ->addButton($elements->button("Cadastro","cadastro","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."cadastro/manutencao/".functions::encrypt($user->tipo_usuario)."/".functions::encrypt($user->id)."'"))
            //->addButton($elements->button("Opções","opcoes","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."opcoes'"))
            ->addButton($elements->button("Voltar","voltar","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."home'"));
        }
        elseif ($user->tipo_usuario == 2){
            $menu->addButton($elements->button("Agendas","agenda","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."agenda'"))
            ->addButton($elements->button("Serviços","servico","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."funcionario'"))
            ->addButton($elements->button("Clientes","clientes","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."clientes'"))
            ->addButton($elements->button("Relatorios","relatorio","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."relatorio'"))
            ->addButton($elements->button("Cadastro","cadastro","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."cadastro/manutencao/".functions::encrypt($user->tipo_usuario)."/".functions::encrypt($user->id)."'"))
            //->addButton($elements->button("Opções","opcoes","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."opcoes'"))
            ->addButton($elements->button("Voltar","voltar","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."home'"));
        }
        elseif ($user->tipo_usuario == 3){
            $menu->addButton($elements->button("Cadastro","cadastro","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."cadastro/manutencao/".functions::encrypt($user->tipo_usuario)."/".functions::encrypt($user->id)."'"))
            //->addButton($elements->button("Opções","opcoes","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."opcoes'"))
            ->addButton($elements->button("Voltar","voltar","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."home'"));
        }
        
        $menu->setLista();              
        $menu->show();

        $footer = new footer;
        $footer->show();
    }
    public function deslogar(){
        loginModel::deslogar();
        $this->go("login");
    }
}