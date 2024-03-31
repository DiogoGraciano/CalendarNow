<?php

namespace app\classes;
use app\classes\pagina;

class filter extends pagina{

    private $tplform;
    
    public function __construct($action)
    {
        $this->tplform = $this->getTemplate("filter_template.html");
        $this->tplform->action = $action;
    }

    public function setLinha(){
        $this->tplform->block("BLOCK_LINHA_FILTER");
    }

    public function addFilter($tamanho, $input){
        $this->tplform->tamanho = $tamanho;
        $this->tplform->filter = $input;
        $this->tplform->block("BLOCK_INPUT");
        $this->tplform->block("BLOCK_FILTER");
    }

    public function addbutton($button){
        $this->tplform->button = $button;
        $this->tplform->block("BLOCK_BUTTON");
    }

    public function show(){
        $this->tplform->show();
    }
}
