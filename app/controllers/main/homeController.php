<?php 
namespace app\controllers\main;
use app\classes\head;
use app\classes\menu;
use app\classes\footer;
use app\classes\controllerAbstract;
use app\models\main\loginModel;

class homeController extends controllerAbstract{

    public function index(){

        $head = new head();
        $head->show("Menu","");

        $menu = new menu();
        $menus = array($menu->getMenu($this->url."agenda","Agendamento"),
                       $menu->getMenu($this->url."cliente","Cliente"),
                       $menu->getMenu($this->url."conexao","Conexão"),
                       $menu->getMenu($this->url."ramal","Ramal"),
                       $menu->getMenu($this->url."usuario","Usuario"),
                       $menu->getMenu($this->url."tabela","Exportar/Importar"),
                       $menu->getMenu($this->url."home/deslogar","Deslogar"));
                       
        $menu->show("Menu",$menus);

        $footer = new footer;
        $footer->show();
    }
    public function deslogar(){
        loginModel::deslogar();
        $this->go("login");
    }
}