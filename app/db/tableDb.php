<?php
namespace app\db;

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
    private $table;

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
        
        if(!$this->validateName($this->table = strtolower(trim($table)))){
            throw new Exception("Nome é invalido");
        }
    }

    public function addColumn(columnDb $column){
        $column = $column->getColumn();

        if($column->size)
            $column->columnSql = ["{$column->name} {$column->type}({$column->size}) {$column->null} {$column->defaut} {$column->comment}",$column->primary,$column->unique,$column->foreingKey];
        else 
            $column->columnSql = ["{$column->name} {$column->type} {$column->null} {$column->defaut} {$column->comment}",$column->primary,$column->unique,$column->foreingKey];

        $this->columns[$column->name] = $column;

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

    private function create($engine="InnoDB",$charset="utf8mb4",$collate="utf8mb4_general_ci"){
        $sql = "DROP TABLE IF EXISTS {$this->table};
                CREATE TABLE IF NOT EXISTS {$this->table} ( ";

        foreach ($this->columns as $column) {            
            $sql .= implode(",",array_filter($column->columnSql));
        }

        $sql .= ") ENGINE={$engine} DEFAULT CHARSET={$charset} COLLATE={$collate};";

        foreach ($this->others as $other) {
            $sql .= $other;
        }

        var_dump($sql);
        die;

        $this->pdo->prepare($sql);

        $this->pdo->execute();
    }

    public function execute($engine="InnoDB",$charset="utf8mb4",$collate="utf8mb4_general_ci",$recreate = false){

        if($recreate){
            $this->create($engine,$charset,$collate);
        }

        $table = $this->getObjectTable();

        if(!$table){
            return $this->create($engine,$charset,$collate);
        }

        foreach ($table->column_name as $column_db){
            if(!in_array($column_db,$this->columns)){
                $sql .= "ALTER TABLE {$this->table} DROP COLUMN $column_db;";
            }  
        }
        
        foreach ($this->columns as $column) {

            $inDb = false;
            foreach ($table->column_name as $column_db){
                if($column_db == $column->name){
                    $inDb = true;
                }
            }

            if($inDb){
                $sql .= "ALTER TABLE {$this->table} MODIFY COLUMN {$column->columnSql};";
            }
            else{
                $sql .= "ALTER TABLE {$this->table} ADD {$column->columnSql};";
            }

            foreach ($this->primarys as $primary) {
                $sql .= $primary;
            }
        }

        if($engine)
            $sql .= "ALTER TABLE {$this->table} ENGINE = {$engine};";

        if($charset)
            $sql .= "ALTER TABLE {$this->table} ENGINE = {$charset};";

        if($collate)
            $sql .= "ALTER TABLE {$this->table} ENGINE = {$collate};";

        foreach ($this->others as $other) {
            $sql .= $other;
        }

        var_dump($sql);
        die;

        $this->pdo->prepare($sql);

        $this->pdo->execute();
    }

    public function getTable(){
        return $this->table;
    }

    //Pega as colunas da tabela e tranforma em Objeto
    private function getObjectTable()
    {
        $sql = $this->pdo->prepare('SELECT COLUMN_NAME FROM information_schema.columns
        WHERE table_schema = "'.self::dbname.'" AND table_name = "'.$this->table.'"');
       
        $sql->execute();

        $rows = [];

        if ($sql->rowCount() > 0) {
            $rows = $sql->fetchAll(\PDO::FETCH_COLUMN|\PDO::FETCH_GROUP);
        }

        return $rows;   

        $this->error[] = "Erro: Tabela não encontrada";
        Logger::error("Erro: Tabela não encontrada");
        return new \StdClass;
    }
    
    private function validateName($name) {
        // Expressão regular para verificar se o nome da tabela contém apenas caracteres permitidos
        $regex = '/^[a-zA-Z_][a-zA-Z0-9_]*$/';
        
        // Verifica se o nome da tabela corresponde à expressão regular
        if (preg_match($regex, $name)) {
            return true; // Nome da tabela é válido
        } else {
            return false; // Nome da tabela é inválido
        }
    }
}