<?php

namespace app\classes;
use app\classes\pagina;
use app\classes\mensagem;

class agenda extends pagina{
    
    public function show($titulo,$action,$eventos)
    {
        $this->tpl = $this->getTemplate("agenda_template.html");
        $this->tpl->titulo = $titulo;
        $mensagem = new mensagem;
        $this->tpl->mensagem = $mensagem->show(false);
        $this->tpl->action = $action;
        $this->tpl->events = $eventos;
        $this->tpl->block("BLOCK_CALENDARIO");
        $this->tpl->show();
    }
}
