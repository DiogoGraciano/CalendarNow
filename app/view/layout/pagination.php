<?php

namespace app\view\layout;

use app\helpers\functions;
use app\view\layout\abstract\pagina;
use core\request;

class pagination extends pagina{

    private int $page;
    private int $totalQuery;
    private int $limit;

    /**
     * Construtor da classe filter.
     *
     * @param string $action URL para onde o formulário será enviado.
     */
    public function __construct(int $totalQuery,int $limit = 30,?int $page = null)
    {
        $this->getTemplate("pagination.html");
        $this->page = ($page?:request::getValue("page"))?:1;
        $this->totalQuery = $totalQuery;
        $this->limit = $limit?:30;
    }

    public function parse():string
    {
        if($this->totalQuery < $this->limit){
            return "";
        }

        $url = str_replace("?".functions::getUriQuery()?:"","",functions::getUrlCompleta());

        if($this->page > 1){
            $this->tpl->link_anterior = $url.$this->getQuery($this->page-1);
            $this->tpl->block("BLOCK_ANTERIOR");
        }

        $i = $this->page-3;
        $b = ($i*-1)+1;
        $i = $i<=0?1:$i;
        for ($i; $i < $this->page+3+$b; $i++)
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

            if($this->getMaxPage() == $i){
                break;
            }
        }
        
        if($this->page < $this->getMaxPage()){
            $this->tpl->link_proximo = $url.$this->getQuery($this->page+1);
            $this->tpl->block("BLOCK_PROXIMO");
        }

        return $this->tpl->parse();
    }

    public function getMaxPage():int
    {
        return ceil($this->totalQuery/$this->limit);
    }

    public function getQuery($page):string
    {
        $query = functions::getUriQueryArray();

        $query["page"] = $page;

        return "?".http_build_query($query);
    }
}