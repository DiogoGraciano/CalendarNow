<?php

namespace core;
use app\classes\functions;

/**
 * Classe para manipular parâmetros da URI.
 */
class Parameter{
    /**
     * URI atual.
     *
     * @var string
     */
    private $uri;

    /**
     * Construtor da classe.
     * Inicializa a URI atual.
     */
    public function __construct()
    {
        $this->uri = functions::getUri();
    }

    /**
     * Carrega e retorna os parâmetros da URI.
     *
     * @return array|null   Retorna um array contendo os parâmetros da URI ou null se não houver parâmetros.
     */
    public function load(){
        if (substr_count($this->uri,'/') > 2){
            $parameter = array_values(array_filter(explode('/',$this->uri)));

            return array_slice($parameter, 2);
        }
        return null;
    }
}

?>
