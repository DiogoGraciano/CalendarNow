<?php
namespace app\models\main;

use app\db\estado;

/**
 * Classe estadoModel
 * 
 * Esta classe fornece métodos para interagir com os dados de estados.
 * Ela utiliza a classe estado para realizar operações de consulta no banco de dados.
 * 
 * @package app\models\main
 */
class estadoModel{

    /**
     * Obtém um estado pelo ID.
     * 
     * @param string $id O ID do estado a ser buscado.
     * @return object Retorna os dados do estado ou null se não encontrado.
     */
    public static function get(int $id):object
    {
        return (new estado)->get($id);
    }

    /**
     * Obtém um estado pela UF.
     * 
     * @param string $uf A UF (Unidade Federativa) do estado a ser buscado.
     * @return array Retorna um array com os dados do estado ou um array vazio se não encontrado.
     */
    public static function getByUf(string $uf):array
    {
        $db = new estado;
        
        $estado = $db->selectByValues(["uf"], [$uf], true);
        
        if ($db->getError()){
            return [];
        }

        return $estado;
    }
}
