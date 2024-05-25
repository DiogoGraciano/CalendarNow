<?php

use app\db\ConnectionDb;
use Exception;

class TransactionManager
{
    /**
     * @var PDO Conexão única com o banco de dados.
     */
    private static $pdo = null;

    /**
     * Inicializa o gerenciador de transações.
     */
    public static function init(): void
    {
        if (self::$pdo === null) {
            self::$pdo = ConnectionDb::getConnection();
        }
    }

    /**
     * Inicia uma transação.
     *
     * @throws ErrorException Lança uma exceção se ocorrer um erro ao iniciar a transação.
     */
    public static function beginTransaction(): void
    {
        try {
            self::$pdo->beginTransaction();
        } catch (PDOException $e) {
            throw new Exception("Erro ao iniciar a transação: " . $e->getMessage());
        }
    }

    /**
     * Confirma a transação.
     *
     * @throws ErrorException Lança uma exceção se ocorrer um erro ao confirmar a transação.
     */
    public static function commit(): void
    {
        try {
            self::$pdo->commit();
        } catch (PDOException $e) {
            throw new Exception("Erro ao confirmar a transação: " . $e->getMessage());
        }
    }

    /**
     * Desfaz a transação.
     *
     * @throws ErrorException Lança uma exceção se ocorrer um erro ao desfazer a transação.
     */
    public static function rollBack(): void
    {
        try {
            self::$pdo->rollBack();
        } catch (PDOException $e) {
            throw new Exception("Erro ao desfazer a transação: " . $e->getMessage());
        }
    }
}
?>
