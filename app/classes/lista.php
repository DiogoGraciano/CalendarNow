<?php

namespace app\classes;
use app\classes\pagina;

class lista extends pagina{

    public function __construct()
    {
        $this->getTemplate("../templates/lista.html");
    }

    public function setLista($titulo,array $lista){
        $this->tpl->titulo = $titulo;
        $mensagem = new mensagem;
        $this->tpl->mensagem = $mensagem->show(false);
        if($lista){
            foreach ($lista as $objeto){
                $this->tpl->url_objeto = $objeto->url_objeto;
                $this->tpl->titulo_objeto = $objeto->titulo_objeto; 
                $this->tpl->block("BLOCK_LISTA");
            } 
        }
        else
            $this->tpl->block("BLOCK_NO_LISTA");  
    }
    public function setButtons(array $buttons){
        foreach ($buttons as $button){
            $this->tpl->button = $button;
            $this->tpl->block("BLOCK_BUTTONS");
        }
    }
    public function show(){
        $this->tpl->show();
    }
    public function getObjeto($url_objeto,$titulo_objeto){
        return json_decode('{"url_objeto":"'.$url_objeto.'","titulo_objeto":"'.$titulo_objeto.'"}');
    }
}
