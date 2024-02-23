<?php
namespace app\db;

class ConfigDB{

    private $pdo;
    
    public function getPDO() {

        //futuramente terá possibilidade de ultilizar o postgresql
        //$this->pdo = new PDO("pgsql:host=localhost;port=5432;dbname=app;user=postgres;password=154326");
        //seta as configurações de acesso ao banco
        //$this->pdo = new \PDO("mysql:host=localhost;port=3306;dbname=agendaai;user=root");
        $this->pdo = new \PDO("mysql:host=localhost;port=3306;dbname=agenda;user=root");
       // $this->pdo = new \PDO("mysql:host=mysql;port=3306;dbname=basenox_bd;user=root;password=rootpass");

        return $this->pdo;
    }
}

?>
