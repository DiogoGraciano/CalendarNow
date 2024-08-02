<?php

namespace app\layout;
use app\layout\abstract\pagina;
use app\helpers\functions;

class head extends pagina{

    public function show($titulo="",$type="",$logo="pequena"){

        $this->getTemplate("head.html");
        $this->tpl->caminho = Functions::getUrlBase();
        $this->tpl->title = $titulo;

        if ($logo=="pequena"){
            $this->tpl->image = "logo_pequena.png";
            $this->tpl->block("BLOCK_LOGO"); 
        }
        elseif ($logo=="grande"){
            $this->tpl->image = "logo_pequena.png";
            $this->tpl->block("BLOCK_LOGO"); 
        }
        elseif ($logo){
            $this->tpl->image = $logo;
            $this->tpl->block("BLOCK_LOGO"); 
        }

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