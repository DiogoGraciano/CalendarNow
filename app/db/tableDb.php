<?php
namespace app\db;
use app\classes\logger;

/**
 * Classe base para criação do banco de dados.
 */
class tableDb extends connectionDB
{
    /**
     * Nome da tabela.
     *
     * @var string
     */
    protected $table;

    /**
     * Colunas.
     *
     * @var string
     */
    private $columns = [];

    /**
     * Primary Keys.
     *
     * @var string
     */
    private $primarys = [];


    /**
     * foreing Keys.
     *
     * @var string
     */
    private $foreingKeys = [];


    /**
     * uniques.
     *
     * @var string
    */
    private $uniques = [];

    /**
     * outros comandos.
     *
     * @var string
    */
    private $others = [];

    function __construct($table)
    {
        // Inicia a Conexão
        if (!$this->pdo)
            $this->startConnection(); 
        
        if(!validateName($this->table = strtolower(trim($table)))){
            throw new Exception("Nome é invalido");
        }
    }

    public function addColumn(columnDb $column){
        $column_ = $column->getColumn();

        if($column_->size)
            $this->columns[$column_->name] = $column;
        else 
            $this->columns[$column_->name] = $column;

        return $this;
    }

    public function isForeingKey(tableDb $foreingTable,string $foreingColumn){

        if($this->columns){
            $column = array_key_first(end($this->columns));

            $this->foreingKeys[] =  "ALTER TABLE {$this->table}
            ADD FOREIGN KEY ({$column}) REFERENCES {$foreingTable->table}({$foreingColumn});";
        }
        else{
            throw new Exception("é preciso ter pelo menos uma coluna para adicionar uma ForeingKey");
        }

        return $this;
    }

    public function isPrimary(){
        if($this->columns){
            $column = array_key_first(end($this->columns));

            $this->primarys[] = "ALTER TABLE {$this->table} ADD PRIMARY KEY ({$column});";
        }
        else{
            throw new Exception("é preciso ter pelo menos uma coluna para adicionar uma PrimaryKey");
        }

        return $this;
    }

    public function isAutoIncrement(){
        if($this->primarys){
            $this->other[] = "ALTER TABLE {$this->table} AUTO_INCREMENT = 1";
        }
        else{
            throw new Exception("é preciso ter pelo menos uma primary key para adicionar o Auto Increment");
        }

        return $this;
    }

    public function addIndex(string $name,array $columns){
        if($this->columns){
            $tableColumns = array_keys($this->columns);
            $columnsFinal = [];
            foreach ($columns as $column){
                $column = strtolower(trim($column));
                if($this->validateName($column) && in_array($column,$tableColumns))
                    $columnsFinal[] = $column;
                else 
                    throw new Exception("Coluna é invalida: ".$column); 
            }
            $this->others[] = "CREATE INDEX {$name} ON {$this->table} (".implode(",",$columnsFinal).";";
        }
        else{
            throw new Exception("É preciso ter pelo menos uma coluna para adicionar o um index");
        }
    }

    public function isUnique(){
        if($this->columns){
            $column = array_key_first(end($this->columns));

            $this->unique[] = "ALTER TABLE {$this->table} ADD UNIQUE ({$column});";
        }
        else{
            throw new Exception("é preciso ter pelo menos uma coluna para adicionar uma Unique");
        }

        return $this;
    }

    public function create($engine="InnoDB",$charset="utf8mb4",$collate="utf8mb4_general_ci"){
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table} ( ";
        foreach ($this->columns as $column) {
            if($column->size)
                $sql .= "{$column->name} {$column->type}({$column->size}) {$column->null} {$column->defaut} {$column->comment},";
            else 
                $sql .= "{$column->name} {$column->type} {$column->null} {$column->defaut} {$column->comment},";
        }
        $sql .= ") ENGINE={$engine} DEFAULT CHARSET={$charset} COLLATE={$collate};";

        foreach ($this->foreingKeys as $foreingKey) {
            $sql .= $foreingKey;
        }

        foreach ($this->primarys as $primary) {
            $sql .= $primary;
        }

        foreach ($this->unique as $unique) {
            $sql .= $unique;
        }

        foreach ($this->others as $other) {
            $sql .= $other;
        }

        $this->pdo->prepare($sql);

        $this->pdo->execute();
    }

    public function update($engine="InnoDB",$charset="utf8mb4",$collate="utf8mb4_general_ci"){
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table} ( ";
        foreach ($this->columns as $column) {
            $sql .= $column;
        }
        $sql .= ") ENGINE={$engine} DEFAULT CHARSET={$charset} COLLATE={$collate};";

        foreach ($this->foreingKeys as $foreingKey) {
            $sql .= $foreingKey;
        }

        foreach ($this->primarys as $primary) {
            $sql .= $primary;
        }

        foreach ($this->unique as $unique) {
            $sql .= $unique;
        }

        foreach ($this->others as $other) {
            $sql .= $other;
        }

        $this->pdo->prepare($sql);

        $this->pdo->execute();
    }

    //Pega as colunas da tabela e tranforma em Objeto
    private function getObjectTable()
    {
        $sql = $this->pdo->prepare('SELECT column_name, data_type, character_maximum_length
        FROM information_schema.columns
        WHERE table_schema = '.$this->database.' AND table_name = '.$this->table);
       
        $sql->execute();

        $rows = [];

        if ($sql->rowCount() > 0) {
            $rows = $sql->fetchAll(\PDO::FETCH_COLUMN, 0);
        }

        if ($rows) {
            $object = new \stdClass;
            foreach ($rows as $row) {
                $object->$row = null;
            }

            return $object;
        } 

        $this->error[] = "Erro: Tabela não encontrada";
        Logger::error("Erro: Tabela não encontrada");
        return new \StdClass;
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