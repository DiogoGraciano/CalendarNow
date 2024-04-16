<?php
namespace core;

use app\classes\functions;
use Exception;

/**
 * Classe para carregar e instanciar controladores com base na URI.
 */
class Controller{
    /**
     * URI atual.
     *
     * @var string
     */
    private $uri;

    /**
     * Array contendo os nomes dos diretórios de controladores.
     *
     * @var array
     */
    private $folders = [];

    /**
     * Namespace do controlador atual.
     *
     * @var string
     */
    private $namespace;

    /**
     * Nome do controlador atual.
     *
     * @var string
     */
    private $controller;

    /**
     * Construtor da classe.
     * Inicializa a URI atual e obtém os diretórios de controladores.
     */
    public function __construct()
    {
        $this->uri = functions::getUri();
        $this->getFolders();
    }

    /**
     * Obtém os diretórios de controladores disponíveis.
     */
    private function getFolders(){
        $pasta = substr(__DIR__, 0, -5).DIRECTORY_SEPARATOR."app".DIRECTORY_SEPARATOR."controllers";
        $arquivos = scandir($pasta);
        foreach ($arquivos as $arquivo) {
            if (!str_contains($arquivo, '.'))
                $this->folders[] = "app\controllers\\".$arquivo;
        }
    }

    /**
     * Carrega e instanciar o controlador com base na URI.
     *
     * @param string|false $controller Nome do controlador a ser carregado ou falso para inferir o controlador com base na URI.
     * 
     * @return object                   Retorna uma instância do controlador carregado.
     * 
     * @throws Exception                Lança uma exceção se o controlador não existir ou se a página não existir.
     */
    public function load($controller=false){

        if ($controller){
            return $this->controllerSet($controller.'Controller'); 
        }
        
        if($this->isHome())
            return $this->controllerHome();
        
        return $this->controllerNotHome();
    }

    /**
     * Carrega o controlador padrão (home).
     *
     * @return object  Retorna uma instância do controlador home.
     * 
     * @throws Exception Lança uma exceção se o controlador home não existir.
     */
    private function controllerHome(){
        if (!$this->controllerExist('homeController'))
            throw new Exception("Essa pagina não existe");
        
        return $this->instatiateController();
    }

    /**
     * Carrega um controlador específico.
     *
     * @param string $controller Nome do controlador a ser carregado.
     * 
     * @return object           Retorna uma instância do controlador especificado.
     * 
     * @throws Exception        Lança uma exceção se o controlador especificado não existir.
     */
    private function controllerSet($controller){
        if (!$this->controllerExist($controller))
            throw new Exception("Essa pagina não existe");
        
        return $this->instatiateController();
    }

    /**
     * Carrega um controlador com base na URI (não sendo a home).
     *
     * @return object  Retorna uma instância do controlador inferido pela URI.
     * 
     * @throws Exception Lança uma exceção se o controlador inferido pela URI não existir.
     */
    private function controllerNotHome(){
       $controller = $this->getControllerNotHome();

       if (!$this->controllerExist($controller))
            throw new Exception("Essa pagina não existe");
       
       return $this->instatiateController();
    }

    /**
     * Obtém o nome do controlador inferido pela URI (não sendo a home).
     *
     * @return string Nome do controlador inferido.
     */
    private function getControllerNotHome(){

        if(substr_count($this->uri,'/') > 1){
            list($controller) = array_values(array_filter(explode('/',$this->uri)));
            return (($controller).'Controller');
        }
        return ((ltrim($this->uri,"/")).'Controller');
    }

    /**
     * Obtém o nome do controlador inferido pela URI (não sendo a home).
     *
     * @param string $controller Nome do controlador a ser carregado
     * 
     * @return bool retorna se o controller existe.
     */
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
    
    /**
     * Instancia o controlador atualmente definido.
     *
     * @return object Retorna uma instância do controlador.
     */
    private function instatiateController(){
        $controller = $this->namespace.'\\'.$this->controller;
        return new $controller;
    }

    /**
     * Verifica se a URI atual é a página inicial.
     *
     * @return bool Retorna verdadeiro se a URI for a página inicial, caso contrário, falso.
     */
    private function isHome(){
        return ($this->uri == "/");    
    }
}
?>
