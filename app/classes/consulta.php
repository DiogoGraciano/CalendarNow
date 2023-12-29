<?php

namespace app\classes;
use app\classes\pagina;
use app\db\db;
use app\classes\mensagem;

class consulta extends pagina{

    public function show($titulo_pagina,$pagina_manutencao,$pagina_action,array $array_button, array $array_columns,$table_db,$sql_intruction=""){

        $db = new db($table_db);

        $this->tpl = $this->getTemplate("consulta_template.html");

        $this->tpl->titulo_pagina = $titulo_pagina;
        $this->tpl->pagina_manutencao = $pagina_manutencao;
        $this->tpl->pagina_action = $pagina_action;
        $mensagem = new mensagem;
        $this->tpl->mensagem = $mensagem->show(false);

        foreach ($array_button as $button){
            $this->tpl->button_caminho = $button->caminho;
            $this->tpl->button_nome = $button->nome;
            $this->tpl->block("BLOCK_BUTTONS");   
        }

        $colunas_html = [];
        foreach ($array_columns as $columns){
            $this->tpl->columns_width = $columns->width;
            $this->tpl->columns_name = $columns->nome;
            $colunas_html[] = $columns->coluna;
            $this->tpl->block("BLOCK_COLUMNS");   
        }

        $columns = $db->getColumns();

        if (!$sql_intruction)
            $dados = $db->selectAll();
        else 
            $dados = $db->selectInstruction($sql_intruction,true);

        if ($dados){
            foreach ($dados as $data){
                foreach ($data as $key => $value){
                    if (in_array($key,$colunas_html)){
                        $this->tpl->data = $value;
                        $this->tpl->block("BLOCK_DADOS");
                    }  
                    if ($key == $columns[0]){
                        $this->tpl->cd_editar = $value;
                        $this->tpl->cd_excluir = $value;
                        $this->tpl->block("BLOCK_BUTTONS_TB");  
                    }
                } 
                $this->tpl->block('BLOCK_TABELA');
            }
        }

        $this->tpl->show();
    }


    public function getButton($caminho,$nome){
        return json_decode('{"caminho":"'.$caminho.'","nome":"'.$nome.'"}');
    }
    public function getColumns($width,$nome,$coluna){
        return json_decode('{"nome":"'.$nome.'","width":"'.$width.'%","coluna":"'.$coluna.'"}');
    }

}

?>