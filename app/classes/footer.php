<?php
namespace app\classes;
use app\classes\pagina;

class footer extends pagina{

    public function show(){
        $this->getTemplate("footer_template.html");
        $this->tpl->ano = date("Y");
        $this->tpl->show();
    }

}

?>