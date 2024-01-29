<?php

namespace app\classes;
use app\classes\pagina;
use app\classes\mensagem;

class consulta extends pagina{

    private $columns = [];

    public function show($pagina_manutencao,$pagina_action,array $array_button,$dados,$coluna_action="id"){

        $this->tpl = $this->getTemplate("consulta_template.html");

        $this->tpl->pagina_manutencao = $pagina_manutencao;
        $this->tpl->pagina_action = $pagina_action;
        $mensagem = new mensagem;
        $this->tpl->mensagem = $mensagem->show(false);

        foreach ($array_button as $button){
            $this->tpl->button = $button;
            $this->tpl->block("BLOCK_BUTTONS");   
        }

        foreach ($this->columns as $columns){
            $this->tpl->columns_width = $columns->width;
            $this->tpl->columns_name = $columns->nome;
            $this->tpl->block("BLOCK_COLUMNS");   
        }

        if ($dados){
            foreach ($dados as $data){
                foreach ($data as $key => $value){
                    $this->tpl->data = $value;
                    $this->tpl->block("BLOCK_DADOS");
                    if ($key == $coluna_action){
                        $this->tpl->cd_editar = functions::encrypt($value);
                        $this->tpl->cd_excluir = functions::encrypt($value);
                        $this->tpl->block("BLOCK_BUTTONS_TB"); 
                    } 
                } 
                $this->tpl->block('BLOCK_TABELA');
            }
        }
       else 
            $this->tpl->block('BLOCK_SEMDADOS');

        $this->tpl->show();
    }

    public function addColumns($width,$nome,$coluna){

        $this->columns[] = json_decode('{"nome":"'.$nome.'","width":"'.$width.'%","coluna":"'.$coluna.'"}');

        return $this;
    }

}

?>