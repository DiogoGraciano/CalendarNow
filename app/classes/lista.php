<?php

namespace app\classes;
use app\classes\pagina;

class lista extends pagina{

    private $lista;

    public function __construct()
    {
        $this->getTemplate("../templates/lista.html");
    }

    public function setLista($titulo){
        $this->tpl->titulo = $titulo;
        $mensagem = new mensagem;
        $this->tpl->mensagem = $mensagem->show(false);
        if($this->lista){
            foreach ($this->lista as $objeto){
                $this->tpl->url_objeto = $objeto->url_objeto;
                $this->tpl->titulo_objeto = $objeto->titulo_objeto; 
                $this->tpl->block("BLOCK_LISTA");
            } 
        }
        else
            $this->tpl->block("BLOCK_NO_LISTA");  
    }
    public function addButton($button){
        $this->tpl->button = $button;
        $this->tpl->block("BLOCK_BUTTONS");
        
    }
    public function show(){
        $this->tpl->show();
    }
    public function addObjeto($url_objeto,$titulo_objeto){
        $this->lista[] =  json_decode('{"url_objeto":"'.$url_objeto.'","titulo_objeto":"'.$titulo_objeto.'"}');
    }
}
