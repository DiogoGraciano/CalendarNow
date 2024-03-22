<?php
namespace app\db;
use app\classes\logger;

class ConfigDB{

    protected $pdo = false;
    
    protected function getConnection() {
        try {
            $this->pdo = new \PDO("mysql:host=localhost;port=3306;dbname=agenda;charset=utf8mb4","root");
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            return $this->pdo;
        } catch(\PDOException $e) {
            Logger::error($e->getMessage());
        }
    }
}

?>
