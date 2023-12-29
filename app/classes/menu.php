<?php

namespace app\classes;
use app\classes\pagina;
use app\classes\functions;

class menu extends pagina{

    public function show($titulo,array $menus){

        $this->getTemplate("../templates/menu.html");
        $this->tpl->titulo = $titulo;
        $mensagem = new mensagem;
        $this->tpl->mensagem = $mensagem->show(false);
        
        foreach ($menus as $menu){
            $this->tpl->url_menu = $menu->url_menu;
            $this->tpl->titulo_menu = $menu->titulo_menu; 
            $this->tpl->block("BLOCK_MENU");
        }
        
        $this->tpl->show();
    }
    public function getMenu($url_menu,$titulo_menu){
        return json_decode('{"url_menu":"'.$url_menu.'","titulo_menu":"'.$titulo_menu.'"}');
    }
}
