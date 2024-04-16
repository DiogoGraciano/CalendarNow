<?php

namespace app\classes;
use app\classes\pagina;

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

    /**
     * Adiciona uma nova coluna à tabela.
     *
     * @param string|int $width   Largura da coluna em porcentagem.
     * @param string $nome    Nome da coluna.
     *
     * @return tabela         Retorna a instância atual da tabela para permitir encadeamento de métodos.
     */
    public function addColumns(string|int $width,string $nome){

        $this->columns[] = json_decode('{"nome":"'.$nome.'","width":"'.$width.'%"}');

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

}

?>
