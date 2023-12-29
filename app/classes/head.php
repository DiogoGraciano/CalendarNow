<?php

namespace app\classes;
use app\classes\pagina;
use app\classes\functions;

class head extends pagina{

    public function show($titulo="",$type="",){

        $this->getTemplate("head_template.html");
        $this->tpl->caminho = Functions::getUrlBase();
        $this->tpl->title = $titulo;

        if ($type=="grafico"){
            $this->tpl->block("BLOCK_GRAFICO");   
        }
        elseif ($type=="consulta"){
            $this->tpl->block("BLOCK_CONSULTA");   
        }
        elseif ($type=="agenda"){
            $this->tpl->block("BLOCK_AGENDA");   
        }
        
        $this->tpl->show();
    }

}

?>