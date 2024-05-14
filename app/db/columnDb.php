<?php
namespace app\db;
use app\classes\logger;

/**
 * Classe base para criação do banco de dados.
 */
class TableDb extends connectionDB
{
    /**
     * Colunas.
     *
     * @var string
     */
    private $column;


    /**
     * Tipos de dados do mysql.
     *
     * @var array
     */
    private const types = [
        'INT',
        'TINYINT',
        'SMALLINT',
        'MEDIUMINT',
        'BIGINT',
        'DECIMAL',
        'FLOAT',
        'DOUBLE',
        'BIT',
        'DATE',
        'TIME',
        'DATETIME',
        'TIMESTAMP',
        'YEAR',
        'CHAR',
        'VARCHAR',
        'BINARY',
        'VARBINARY',
        'TINYBLOB',
        'BLOB',
        'MEDIUMBLOB',
        'LONGBLOB',
        'TINYTEXT',
        'TEXT',
        'MEDIUMTEXT',
        'LONGTEXT'
    ];

    public function __construct(string $name,string $type,int|null $size = null)
    {
        $type = strtoupper(trim($type));
        
        if(in_array($this->types)){

            if($size){
                validateSize($type,$size);
            }

            $name = strtolower(trim($name));

            if(!validateName($name)){
                throw new Exception("Nome é invalido");
            }

            $this->column = new StdClass;
            $this->columns->name = $name;
            $this->columns->type = $type;
            $this->columns->size = $size;
            $this->columns->null = "";
            $this->columns->defaut = "";
            $this->columns->comment = "";
        }
        else 
            throw new Exception("Tipo é invalido");
        
    }

    public function isNotNull(){
        $this->column->null = "NOT NULL";
        return $this;
    }

    public function setDefaut(string|int|float|null $value = null){

        if(is_string($value))
            $this->column->defaut = " DEFAULT '".$value."'";
        elseif(isNull($value) && !$this->column->null) 
            $this->column->defaut = " DEFAULT NULL";
        elseif(!isNull($value)) 
            $this->column->defaut = " DEFAULT ".$value;

        return $this;
    }

    public function getColumn(){
        return $this->column;
    }

    public function setComment($comment){
        $this->column->comment = "COMMENT '{$comment}'";
        return $this;
    }

    private function validateSize(string $type,int $size){
        if($size < 0){
            throw new Exception("Tamanho é invalido");
        }
        else if(($type == "CHAR" || $type ==  "BINARY") && $size > 255){
            throw new Exception("Tamanho é invalido para o tipo informado");
        }
        else if(($type == "VARCHAR" || $type ==  "VARBINARY") && $size > 65535){
            throw new Exception("Tamanho é invalido para o tipo informado");
        }
        else{
            throw new Exception("Tamanho não deve ser informado para o tipo informado");
        }

        return true;
    }

    private function validateName($name) {
        // Expressão regular para verificar se o nome da tabela contém apenas caracteres permitidos
        $regex = '/^[a-zA-Z_][a-zA-Z0-9_]*$/';
        
        // Verifica se o nome da tabela corresponde à expressão regular
        if (preg_match($regex, $nomeTabela)) {
            return true; // Nome da tabela é válido
        } else {
            return false; // Nome da tabela é inválido
        }
    }

    

}