<?php

namespace app\classes;
use app\classes\pagina;
use app\classes\mensagem;
use app\classes\elements;

class consulta extends pagina{

    private $columns = [];
    private $buttons = [];

public function show($pagina_manutencao,$pagina_action,$dados,$coluna_action="id",$checkbox=false){

        $this->tpl = $this->getTemplate("consulta_template.html");
        $mensagem = new mensagem;
        $this->tpl->mensagem = $mensagem->show(false);
        $this->tpl->pagina_manutencao = $pagina_manutencao;

        foreach ($this->buttons as $button){
            $this->tpl->button = $button;
            $this->tpl->block("BLOCK_BUTTONS");   
        }

        if($this->isMobile())
            $table = new tabelaMobile;
        else{
            $table = new tabela;
        }

        if ($checkbox){
            if($this->isMobile())
                $table->addColumns("1","Selecionar");
            else
                $table->addColumns("1","");
        }

        foreach ($this->columns as $columns){
            $table->addColumns($columns->width,$columns->nome);
        }

        if ($dados){
            $i = 0;
            foreach ($dados as $data){
                $row = [];
                $b = 1;
                $row_action = "";
                foreach ($data as $key => $value){
                    if ($checkbox && $b == 1){
                        $row[] = (new elements)->checkbox("id_check_".$i+1,false,false,false,false,$value);
                        $b++;
                    }
                    $row[] = $value;
                    if ($key == $coluna_action){
                        $row[] = '<button type="button" class="btn btn btn-primary">
                                    <a href="'.$pagina_manutencao.'/'.functions::encrypt($value).'">Editar</a>
                                </button>
                                <button class="btn btn btn-primary" onclick="confirmaExcluir()" type="button">
                                    <a href="'.$pagina_action.'/'.functions::encrypt($value).'">Excluir</a>
                                </button>';
                        $row_action = array_key_last($row);
                    }
                   
                } 
                $row_buttoms = $row[$row_action];
                unset($row[$row_action]);
                $row[] = $row_buttoms;
                $i++;
                $table->addRow(array_values($row));
            }
            $this->tpl->qtd_list = $i;
            $this->tpl->table = $table->parse();
        }
        else 
            $this->tpl->block('BLOCK_SEMDADOS');

        $this->tpl->show();
    }

    public function addColumns($width,$nome,$coluna){

        $this->columns[] = json_decode('{"nome":"'.$nome.'","width":"'.$width.'%","coluna":"'.$coluna.'"}');

        return $this;
    }

    public function addButtons($button){

        $this->buttons[] = $button;

        return $this;
    }

}

?>