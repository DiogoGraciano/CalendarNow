<?php

namespace app\view\layout;
use app\view\layout\abstract\pagina;

/**
 * Classe para criação e manipulação de tabelas HTML.
 */
class tabela extends pagina{

    /**
     * Array para armazenar as colunas da tabela.
     *
     * @var array
     */
    private $columns = [];

    /**
     * Array para armazenar as linhas da tabela.
     *
     * @var array
     */
    private $rows = [];

    /**
     * Gera a representação HTML da tabela com base nas colunas e linhas fornecidas.
     *
     * @return string   Retorna a representação HTML da tabela.
     */
    public function parse(){

        $this->tpl = $this->getTemplate("table.html");
        
        if($this->rows){
            $i = 1;
            foreach ($this->rows as $row){
                if(is_subclass_of($row,"app\db\db")){
                    $row = $row->getArrayData();
                }
                foreach ($this->columns as $column){
                    if(array_key_exists($column["coluna"],$row)){
                        $this->tpl->data = $row[$column["coluna"]];
                        $this->tpl->block("BLOCK_DATA");
                        if($i == 1){
                            $this->tpl->columns_name = $column["nome"];
                            $this->tpl->columns_width = $column["width"];
                            $this->tpl->block("BLOCK_COLUMNS");
                        }
                    }
                }
                $i++;
                $this->tpl->block("BLOCK_ROW");
            }
        }

        $this->columns = $this->rows = [];
        return $this->tpl->parse();
    }

    /**
     * Adiciona uma nova coluna à tabela.
     *
     * @param string|int $width   Largura da coluna em porcentagem.
     * @param string $nome    Nome da coluna.
     * @param string $coluna    Nome da coluna DB.
     *
     * @return tabela         Retorna a instância atual da tabela para permitir encadeamento de métodos.
     */
    public function addColumns(string|int $width,string $nome, string $coluna){

        $this->columns[] = ["nome" => $nome,"width" => $width.'%',"coluna" => $coluna];

        return $this;
    }

    /**
     * Adiciona uma nova linha à tabela.
     *
     * @param array $row     Dados da linha como um array associativo.
     *
     * @return tabela        Retorna a instância atual da tabela para permitir encadeamento de métodos.
     */
    public function addRow(array $row = array()){

        $this->rows[] = $row;

        return $this;
    }

     /**
     * Adiciona todas as linhas à tabelas.
     *
     * @param array $rows     Dados das linhas como um array associativo.
     *
     * @return tabela        Retorna a instância atual da tabela para permitir encadeamento de métodos.
     */
    public function addRows(array $rows = []){

        $this->rows = $rows;

        return $this;
    }

}

?>
