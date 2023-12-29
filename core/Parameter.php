<?php
    namespace core;
    use app\classes\Uri;

    class Parameter{
        private $uri;

        public function __construct()
        {
            $this->uri = Uri::getUri();
        }

        public function load(){
            if (substr_count($this->uri,'/') > 2){
                $parameter = array_values(array_filter(explode('/',$this->uri)));

                return array_slice($parameter, 2);
            }
        }
    }

?>