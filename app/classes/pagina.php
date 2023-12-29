<?php
namespace app\classes;
use app\classes\template;
use app\classes\functions;

class pagina{

    public $tpl;

    public function getTemplate($caminho,$accurate=false){

        $this->tpl = new template(Functions::getRaiz()."/app/view/templates/".$caminho,$accurate); 

        if ($this->tpl)
            return $this->tpl;
        else 
            return false;     
    }

}


