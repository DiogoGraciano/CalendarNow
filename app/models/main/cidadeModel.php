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
     * @return array|null Retorna os dados da cidade ou null se não encontrado.
     */
    public static function get(int $id){
        return (new cidade)->get($id);
    }

    /**
     * Obtém uma cidade pelo nome.
     * 
     * @param string $nome O nome da cidade a ser buscada.
     * @return array Retorna um array com os dados da cidade ou um array vazio se não encontrado.
     */
    public static function getByNome(string $nome){
        $db = new cidade;
        $cidade = $db->addFilter("nome", "LIKE", "%" . $nome . "%")->addLimit(1)->selectAll();
        return $cidade;
    }

    /**
     * Obtém uma cidade pelo nome e ID do estado (UF).
     * 
     * @param string $nome O nome da cidade a ser buscada.
     * @param string $uf O ID do estado (UF) da cidade.
     * @return array Retorna um array com os dados da cidade ou um array vazio se não encontrado.
     */
    public static function getByNomeIdUf(string $nome,string $uf){
        $db = new cidade;
        $cidade = $db->addFilter("nome", "LIKE", "%" . $nome . "%")->addLimit(1)->selectByValues(["uf"], [$id_uf], true);
        return $cidade;
    }

    /**
     * Obtém uma cidade pelo código IBGE.
     * 
     * @param string $ibge O código IBGE da cidade.
     * @return array Retorna um array com os dados da cidade ou um array vazio se não encontrado.
     */
    public static function getByIbge(string $ibge){
        $db = new cidade;
        $cidade = $db->selectByValues(["ibge"], [$ibge], true);
        return $cidade;
    }

    /**
     * Obtém cidades pela (UF).
     * 
     * @param string $id_estado O (UF) das cidades.
     * @return array Retorna um array de cidades.
     */
    public static function getByEstado(string $uf){
        $db = new cidade;
        $cidade = $db->addFilter("uf", "=", $id_estado)->selectAll();
        return $cidade;
    }

}
