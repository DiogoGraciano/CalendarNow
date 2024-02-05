<?php

namespace app\classes;
use app\classes\pagina;

class menu extends pagina{

    private $buttons = [];

    public function __construct()
    {
        $this->getTemplate("../templates/menu.html");
    }

    public function setLista(){
        $mensagem = new mensagem;
        $this->tpl->mensagem = $mensagem->show(false);
        foreach ($this->buttons as $objeto){
            $this->tpl->button = $objeto;
            $this->tpl->block("BLOCK_MENU");
        }  
        $this->buttons = [];
    }

    public function addButton($button){
        $this->buttons[] = $button;

        return $this;
    }

    public function show(){
        $this->tpl->show();
    }
   
}
