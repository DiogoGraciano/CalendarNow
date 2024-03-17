<?php

namespace app\classes;
use app\classes\pagina;

class tabelaMobile extends pagina{

    private $columnsrows = [];
    private $columns = [];
    private $rows = [];

    public function parse(){

        $this->tpl = $this->getTemplate("table_mobile_template.html");

        if($this->rows){
            foreach ($this->rows as $row){
                foreach ($row as $key => $data){
                    if(array_key_exists($key,$this->columns))
                        $this->addColumnsRows($data,$this->columns[$key]);
                }
            }
        }

        foreach ($this->columnsrows as $columnrow){
            $this->tpl->titulo = $columnrow->titulo;
            $this->tpl->row = base64_decode($columnrow->row) ;
            $this->tpl->block("BLOCK_ROW");   
        }

        return $this->tpl->parse();
    }

    public function addColumns($width,$nome){

        $this->columns[] = $nome;

        return $this;
    }

    public function addRow(array $row = []){

        $this->rows[] = $row;

        return $this;
    }

    private function addColumnsRows($row,$titulo){

        $this->columnsrows[] = json_decode('{"row":"'.base64_encode($row).'","titulo":"'.$titulo.'"}');

        return $this;
    }

}

?>