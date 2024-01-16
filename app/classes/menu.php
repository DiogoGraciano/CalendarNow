<?php

namespace app\classes;
use app\classes\pagina;

class menu extends pagina{

    public function __construct()
    {
        $this->getTemplate("../templates/menu.html");
    }

    public function setLista(array $buttons){
        $mensagem = new mensagem;
        $this->tpl->mensagem = $mensagem->show(false);
        foreach ($buttons as $objeto){
            $this->tpl->button = $objeto;
            $this->tpl->block("BLOCK_MENU");
        }  
    }
    public function show(){
        $this->tpl->show();
    }
   
}
