<?php
namespace app\models\main;

use app\db\cidade;
use app\classes\modelAbstract;
use app\classes\elements;

/**
 * Classe cidadeModel
 * 
 * Esta classe fornece métodos para interagir com os dados de cidades.
 * Ela utiliza a classe cidade para realizar operações de consulta no banco de dados.
 * 
 * @package app\models\main
 */
class cidadeModel{

    /**
     * Obtém uma cidade pelo ID.
     * 
     * @param string $id O ID da cidade a ser buscada.
     * @return object Retorna os dados da cidade ou null se não encontrado.
     */
    public static function get(int $id):object
    {
        return (new cidade)->get($id);
    }

    /**
     * Obtém uma cidade pelo nome.
     * 
     * @param string $nome O nome da cidade a ser buscada.
     * @return array Retorna um array com os dados da cidade ou um array vazio se não encontrado.
     */
    public static function getByNome(string $nome):array
    {
        $db = new cidade;
        $cidade = $db->addFilter("nome", "LIKE", "%" . $nome . "%")->addLimit(1)->selectAll();

        if ($db->getError()){
            return [];
        }

        return $cidade;
    }

    /**
     * Obtém uma cidade pelo nome e ID do estado (UF).
     * 
     * @param string $nome O nome da cidade a ser buscada.
     * @param string $uf O ID do estado (UF) da cidade.
     * @return array Retorna um array com os dados da cidade ou um array vazio se não encontrado.
     */
    public static function getByNomeIdUf(string $nome,string $uf):array
    {
        $db = new cidade;
        $cidade = $db->addFilter("nome", "LIKE", "%" . $nome . "%")->addFilter("uf", "=", $uf)->addLimit(1)->selectAll();
        
        if ($db->getError()){
            return [];
        }
        
        return $cidade;
    }

    /**
     * Obtém uma cidade pelo código IBGE.
     * 
     * @param string $ibge O código IBGE da cidade.
     * @return array Retorna um array com os dados da cidade ou um array vazio se não encontrado.
     */
    public static function getByIbge(string $ibge):array
    {
        $db = new cidade;
        $cidade = $db->addFilter("ibge", "=", $ibge)->selectAll();

        if ($db->getError()){
            return [];
        }

        return $cidade;
    }

    /**
     * Obtém cidades pela (UF).
     * 
     * @param string $uf O (UF) das cidades.
     * @return array Retorna um array de cidades.
     */
    public static function getByEstado(string $uf):array {
        $db = new cidade;
        $cidade = $db->addFilter("uf", "=", $uf)->selectAll();

        if ($db->getError()){
            return [];
        }

        return $cidade;
    }

}
