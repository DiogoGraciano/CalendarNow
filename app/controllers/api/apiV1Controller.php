<?php 
namespace app\controllers\api;

use app\controllers\abstract\controller;
use app\helpers\functions;
use app\models\api\loginApiModel;
use core\request;

class apiV1Controller extends controller{

    /**
     * Tipo de requisição HTTP (GET, POST, PUT, DELETE).
     *
     * @var string
     */
    private $requestType;

    /**
     * Dados enviados na requisição.
     *
     * @var mixed
     */
    private $data;

    /**
     * query da requisição.
     *
     * @var mixed
    */
    private $query;

    /**
     * Construtor da classe.
     *
     * Realiza a autenticação do usuário através do token fornecido nas headers
     * e inicializa os atributos $requestType e $data.
     */
    public function __construct(){
        // Verifica se as credenciais estão presentes nas headers
        if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])){
            echo json_encode(['error' => "Token não informado ou inválido","result" => false]);
            http_response_code(400);
            die;
        }

        // Realiza a autenticação do usuário
        if (!loginApiModel::login($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])){
            echo json_encode(['error' => "Token inválido","result" => false]);
            http_response_code(400);
            die;
        }

        // Obtém o tipo de requisição e os dados enviados na requisição
        $this->requestType = $_SERVER['REQUEST_METHOD'];
        if ($this->requestType === "PUT" || $this->requestType === "POST")
            $this->data = json_decode(file_get_contents('php://input'), true);
        
        if($query = functions::getUriQuery()){
            parse_str($query,$this->query);
        }   
        
        $user = usuarioApiModel::getLogged();

        if($this->user->tipo_usuario == 2){
            $this->query["id_empresa"] = $user->id_empresa;
        }
    }

    /**
     * Chama o método correspondente do controlador clienteController.
     *
     * @param array $parameters Parâmetros da requisição.
     */
    public function cliente($parameters){
        $this->callControllerMethod(new clienteController($this->requestType, $this->data, $this->query), $parameters);
    }

    /**
     * Chama o método correspondente do controlador funcionarioController.
     *
     * @param array $parameters Parâmetros da requisição.
     */
    public function funcionario($parameters){
        $this->callControllerMethod(new funcionarioController($this->requestType, $this->data, $this->query), $parameters);
    }

    /**
     * Chama o método correspondente do controlador usuarioController.
     *
     * @param array $parameters Parâmetros da requisição.
     */
    public function usuario($parameters){
        $this->callControllerMethod(new usuarioController($this->requestType, $this->data, $this->query), $parameters);
    }

    /**
     * Chama o método correspondente do controlador agendaController.
     *
     * @param array $parameters Parâmetros da requisição.
     */
    public function agenda($parameters){
        $this->callControllerMethod(new agendaController($this->requestType, $this->data, $this->query), $parameters);
    }

    /**
     * Método auxiliar para chamar o método do controlador correspondente.
     *
     * @param string $controllerName Nome do controlador.
     * @param array $parameters Parâmetros da requisição.
     */
    private function callControllerMethod($class, $parameters){
        if (array_key_exists(0, $parameters) && method_exists($class, $method = $parameters[0]) && is_subclass_of($class,"app/controllers/abstract/apiController")){
            unset($parameters[0]);
            $class->$method($parameters);
        }
        else{
            echo json_encode(['error' => "Método não encontrado","result" => false]); 
            http_response_code(400);
        }
    }
}