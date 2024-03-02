<?php

namespace app\classes;
use app\classes\pagina;

class tabela extends pagina{

    private $columns = [];
    private $rows = [];

    public function parse(){

        $this->tpl = $this->getTemplate("table_template.html");
        foreach ($this->columns as $columns){
            $this->tpl->columns_width = $columns->width;
            $this->tpl->columns_name = $columns->nome;
            $this->tpl->block("BLOCK_COLUMNS");   
        }
        
        foreach ($this->rows as $rows){
            foreach ($rows as $data){
                $this->tpl->data = $data;
                $this->tpl->block("BLOCK_DATA"); 
            }
            $this->tpl->block("BLOCK_ROW"); 
        }

        return $this->tpl->parse();
    }

    public function addColumns($width,$nome){

        $this->columns[] = json_decode('{"nome":"'.$nome.'","width":"'.$width.'%"}');

        return $this;
    }


    public function addRow(array $row = array()){

        $this->rows[] = $row;

        return $this;
    }

}

?>