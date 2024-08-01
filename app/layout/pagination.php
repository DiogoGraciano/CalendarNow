<?php

use app\helpers\functions;
use app\layout\abstract\pagina;
use core\request;

class pagination extends pagina{

    private int $page;
    /**
     * Construtor da classe filter.
     *
     * @param string $action URL para onde o formulário será enviado.
     */
    public function __construct($page)
    {
        $this->getTemplate("pagination.html");
        $this->page = ($page?:request::getValue("page"))?:1;
    }

    public function getOffset(int $limit = 30){
        return ($this->page-1)*$limit;
    }

    public function show(){

        $url = str_replace(functions::getUriQuery(),"",functions::getUrlCompleta());

        if($this->page > 1){
            $this->tpl->link_anterior = $url.$this->getQuery($this->page-1);
            $this->tpl->block("BLOCK_ANTERIOR");
        }

        $i = $this->page-3;
        for ($i; $i < $this->page+3; $i++)
        { 
            if($this->page == $i){
                $this->tpl->link_page = $url.$this->getQuery($this->page);
                $this->tpl->class_page = "active";
                $this->tpl->page = $this->page;
            }
            else{
                $this->tpl->link_page = $url.$this->getQuery($i);
                $this->tpl->class_page = "";
                $this->tpl->page = $i;
            }

            $this->tpl->block("BLOCK_PAGINA");
        }
        
        if($this->page > 1)
            $this->tpl->link_proximo = $url.$this->getQuery($this->page+1);
    }

    public function getMaxPage(){

    }

    public function getQuery($page){
        $query = functions::getUriQueryArray();

        $query["page"] = $page;

        return http_build_query($query);
    }
}