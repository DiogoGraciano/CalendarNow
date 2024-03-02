<?php

namespace app\classes;
use app\classes\pagina;

class tabelaMobile extends pagina{

    private $rows = [];

    public function parse(){

        $this->tpl = $this->getTemplate("table_mobile_template.html");

        foreach ($this->rows as $row){
            $this->tpl->titulo = $row->titulo;
            $this->tpl->row = base64_decode($row->row) ;
            $this->tpl->block("BLOCK_ROW");   
        }

        return $this->tpl->parse();
    }

    public function addColumnsRows($row,$titulo){

        $this->rows[] = json_decode('{"row":"'.base64_encode($row).'","titulo":"'.$titulo.'"}');

        return $this;
    }

}

?>