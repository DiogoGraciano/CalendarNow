<?php
    namespace core;
    use app\classes\Uri;
use Exception;

    class Method{
        private $uri;

        public function __construct()
        {
            $this->uri = Uri::getUri();
        }

        public function load($controller){
            $method = $this->getMethod();

            if(!method_exists($controller, $method)){
                throw new Exception('Metodo não existe no arquivo solicitado');
            }

            return $method;
        }

        private function getMethod(){

            if (substr_count($this->uri,'/') > 1){
                $method = array_values(array_filter(explode('/',$this->uri)));
                if (array_key_exists(1,$method))
                    return $method[1];
            }

            return "index";
        }
    }
?>