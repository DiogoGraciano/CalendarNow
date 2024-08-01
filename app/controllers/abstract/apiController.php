<?php
namespace app\controllers\abstract;

use core\request;
/**
 * Classe abstrata controller é uma classe base para controladores.
 *
 * Esta classe fornece métodos utilitários comuns que podem ser usados por controladores específicos.
 */
abstract class apiController
{
    /**
     * Define os parâmetros com base nas colunas fornecidas e nos dados retornados pela API.
     *
     * @param array $columns Colunas a serem retornadas.
     * @param array $values Dados retornados pela API.
     * @return array Array contendo os valores das colunas especificadas.
     */
    public function setParameters(array $columns, array $values)
    {
        $return = [];
        foreach ($columns as $column) {
            if (isset($values[$column])) {
                $return[] = $values[$column];
            }
        }
        return $return;
    }

    /**
     * Retorna o nome dos argumentos de um metodo de uma clase.
     *
     * @param string $className Nome da classe.
     * @param string $methodName Nome do Metodo.
     * @return array Array contendo os valores das colunas especificadas.
     */
    public function getMethodsArgNames($className, $methodName) {
        $r = new \ReflectionMethod($className, $methodName);
        $parameters = $r->getParameters();

        $return = [];
        foreach ($parameters as $parameter){
            $return[] = $parameter->getName();
        }

        return $return;
    }
}
