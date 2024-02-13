<?php

namespace app\classes;
use app\classes\pagina;
use app\classes\mensagem;

class agenda extends pagina{
    
    public function show($action,$eventos,$initial_time = "8:00",$final_time = "19:00")
    {
        $this->tpl = $this->getTemplate("agenda_template.html");
        $mensagem = new mensagem;
        $this->tpl->mensagem = $mensagem->show(false);
        $this->tpl->action = $action;
        $this->tpl->initial_time = $initial_time;
        $this->tpl->final_time = $final_time;
        $this->tpl->events = $eventos;
        $this->tpl->block("BLOCK_CALENDARIO");
        $this->tpl->show();
    }
}
