<?php
    namespace core;
    use app\classes\uri;

    class Controller{
        private $uri;
        private $folders = [];
        private $namespace;
        private $controller;

        public function __construct()
        {
            $this->uri = uri::getUri();
            $this->getFolders();
        }
        private function getFolders(){
            $pasta = substr(__DIR__, 0, -5).DIRECTORY_SEPARATOR."app".DIRECTORY_SEPARATOR."controllers";
            $arquivos = scandir($pasta);
            foreach ($arquivos as $arquivo) {
                if (!str_contains($arquivo, '.'))
                    $this->folders[] = "app\controllers\\".$arquivo;
            }
        }
        public function load($controller=false){

            if ($controller){
                return $this->controllerSet($controller.'Controller'); 
            }
            
            if($this->isHome())
                return $this->controllerHome();
            

            return $this->controllerNotHome();
        }
        private function controllerHome(){
            if (!$this->controllerExist('homeController'))
                throw new \Exception("Essa pagina não existe");
            
            return $this->instatiateController();
        }
        private function controllerSet($controller){
            if (!$this->controllerExist($controller))
                throw new \Exception("Essa pagina não existe");
            
            return $this->instatiateController();
        }
        private function controllerNotHome(){
           $controller = $this->getControllerNotHome();

           if (!$this->controllerExist($controller))
                throw new \Exception("Essa pagina não existe");
           
           return $this->instatiateController();
        }
        private function getControllerNotHome(){

            if(substr_count($this->uri,'/') > 1){
                list($controller) = array_values(array_filter(explode('/',$this->uri)));
                return (($controller).'Controller');
            }
            return ((ltrim($this->uri,"/")).'Controller');
        }
        private function instatiateController(){
            $controller = $this->namespace.'\\'.$this->controller;
            return new $controller;
        }
        private function controllerExist($controller){
            $exists = false;

            foreach ($this->folders as $folder){
                if(class_exists($folder."\\".$controller)){
                    $exists = true;
                    $this->namespace = $folder;
                    $this->controller = $controller; 
                }
            }
            return $exists;
        }
        private function isHome(){
            return ($this->uri == "/");    
        }
    }
?>