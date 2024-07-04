<?php

namespace app\classes;
use app\classes\pagina;

/**
 * Classe para criação e manipulação de tabelas adaptadas para dispositivos móveis.
 */
class tabelaMobile extends pagina{

    /**
     * Array para armazenar os nomes das colunas.
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
     * Gera a representação HTML da tabela adaptada para dispositivos móveis.
     *
     * @return string   Retorna a representação HTML da tabela.
     */
    public function parse(){

        $this->tpl = $this->getTemplate("table_mobile_template.html");

        if($this->rows){
            foreach ($this->rows as $row){
                if(is_subclass_of($row,"app\db\db")){
                    $row = $row->getArrayData();
                }
                foreach ($this->columns as $column){
                    if(array_key_exists($column["coluna"],$row)){
                        $this->tpl->columns_name = $column["nome"];
                        $this->tpl->data = $row[$column["coluna"]];
                    }
                    $this->tpl->block("BLOCK_ROW");
                }
            }
        }

        $this->columns = $this->rows = [];
        return $this->tpl->parse();
    }

    /**
     * Adiciona uma nova coluna à tabela.
     *
     * @param string|int $width Nome da coluna.
     * @param string $nome      Largura da coluna.
     * @param string $coluna    Nome da coluna DB.
     *
     * @return tabelaMobile   Retorna a instância atual da tabela para permitir encadeamento de métodos.
     */
    public function addColumns(string|int $width,string $nome,string $coluna){

        $this->columns[] = ["nome" => $nome,"width" => $width.'%',"coluna" => $coluna];

        return $this;
    }

    /**
     * Adiciona uma nova linha à tabela.
     *
     * @param array $row     Dados da linha como um array associativo.
     *
     * @return tabelaMobile  Retorna a instância atual da tabela para permitir encadeamento de métodos.
     */
    public function addRow(array $row = []){

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
