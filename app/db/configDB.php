<?php
namespace app\db;
use app\classes\logger;
use ErrorException;
use PDO;
use PDOException;

/**
 * Classe para configuração e obtenção da conexão com o banco de dados.
 */
class ConfigDB{

    /**
     * Instância do objeto PDO para a conexão com o banco de dados.
     *
     * @var PDO
     */
    protected $pdo;
    
    /**
     * Obtém a conexão com o banco de dados usando o PDO.
     *
     * @return PDO Retorna uma instância do objeto PDO.
     * 
     * @throws ErrorException Lança uma exceção se ocorrer um erro ao conectar com o banco de dados.
     */
    protected function getConnection() {
        try {
            $this->pdo = new PDO("mysql:host=localhost;port=3306;dbname=agenda;charset=utf8mb4","root");
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $this->pdo;
        } catch(PDOException $e) {
            // Registra o erro no log antes de lançar a exceção
            Logger::error($e->getMessage());

            // Lança uma exceção personalizada
            throw new ErrorException("Erro ao conectar com ao banco de dados");
        }
    }
}
?>
