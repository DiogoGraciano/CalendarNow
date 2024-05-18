<?php
namespace app\db;
use app\classes\logger;
use ErrorException;
use PDO;
use PDOException;

/**
 * Classe para configuração e obtenção da conexão com o banco de dados.
 */
class connectionDb{

    protected const host = "localhost";

    protected const port = "3306";

    protected const dbname = "agenda";

    protected const charset = "utf8mb4";

    protected const user = "root";

    protected const password = "";

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
    public function startConnection() {

        $host = self::host;
        $port = self::port;
        $dbname = self::dbname;
        $charset = self::charset;

        try {
            $this->pdo = new PDO("mysql:host={$host};port={$port};dbname={$dbname};charset=".$charset,self::user,self::password);
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
