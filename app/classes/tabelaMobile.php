<?php

namespace app\classes;
use app\classes\pagina;

/**
 * Classe para criação e manipulação de tabelas adaptadas para dispositivos móveis.
 */
class tabelaMobile extends pagina{

    /**
     * Array para armazenar os títulos e as linhas da tabela.
     *
     * @var array
     */
    private $columnsrows = [];

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

    /**
     * Adiciona uma nova coluna à tabela.
     *
     * @param string|int $width   Nome da coluna.
     * @param string $nome    Largura da coluna.
     *
     * @return tabelaMobile   Retorna a instância atual da tabela para permitir encadeamento de métodos.
     */
    public function addColumns(string|int $width,string $nome){

        $this->columns[] = $nome;

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
     * Adiciona uma nova linha e título à tabela.
     *
     * @param string $row     Base 64 do Dado da linha a ser adicionado.
     * @param string $titulo  Título da coluna correspondente ao dado da linha.
     *
     * @return tabelaMobile  Retorna a instância atual da tabela para permitir encadeamento de métodos.
     */
    private function addColumnsRows(string $row,string $titulo){

        $this->columnsrows[] = json_decode('{"row":"'.base64_encode($row).'","titulo":"'.$titulo.'"}');

        return $this;
    }

}

?>
