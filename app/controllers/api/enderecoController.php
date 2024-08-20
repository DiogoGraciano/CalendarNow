<?php 
namespace app\controllers\api;

use app\controllers\abstract\apiController;
use app\helpers\mensagem;
use app\models\api\enderecoModel;

/**
 * Classe enderecoController
 * 
 * Este controlador lida com as requisições da API relacionadas a endereços.
 * 
 * @package app\controllers\api
 */
class enderecoController extends apiController{

    /**
     * Construtor da classe.
     *
     * @param string $requestType Tipo de requisição HTTP.
     * @param mixed $data Dados enviados na requisição.
     * @param array $query Parâmetros de consulta na URL.
     */
    public function __construct($requestType, $data, $query){
        $this->requestType = $requestType;
        $this->data = $data;
        $this->query = $query;
    }

    /**
     * Obtém um endereço pelo ID.
     *
     * @param array $parameters Parâmetros da requisição, incluindo o ID do endereço.
     */
    public function getById($parameters)
    {
        try {
            $this->validRequest(['GET']);

            if (!isset($parameters[0])) {
                $this->sendResponse(['error' => 'ID do endereço não informado', 'result' => false], 400);
            }

            $endereco = enderecoModel::get($parameters[0]);

            if ($endereco) {
                $this->sendResponse(['result' => $endereco]);
            } else {
                $this->sendResponse(['error' => 'Endereço não encontrado', 'result' => false], 404);
            }

        } catch (\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage(), 'result' => false], 400);
        }
    }

    /**
     * Obtém endereços por ID de usuário.
     */
    public function getByIdUsuario()
    {
        try {
            $this->validRequest(['GET']);

            $id_usuario = isset($this->query['id_usuario']) ? $this->query['id_usuario'] : null;

            if (!$id_usuario) {
                $this->sendResponse(['error' => 'ID do usuário não informado', 'result' => false], 400);
            }

            $enderecos = enderecoModel::getbyIdUsuario($id_usuario);

            if ($enderecos) {
                $this->sendResponse(['result' => $enderecos]);
            } else {
                $this->sendResponse(['error' => 'Nenhum endereço encontrado para o usuário', 'result' => false], 404);
            }

        } catch (\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage(), 'result' => false], 400);
        }
    }

    /**
     * Insere ou atualiza um endereço.
    */
    public function set(){
        try {
            $errors = [];
            $result = []; 
            $idsalvos = [];

            $this->validRequest(['POST']);

            if(!$this->data){
                $this->sendResponse(['error' => "Body da requisão não informado","result" => false],400);
            }

            $columns = $this->getMethodsArgNames("app\models\api\enderecoModel","set");
            foreach ($this->data as $registro){
                
                if($this->user->tipo_usuario == 2){
                    $registro["id_empresa"] = $this->user->id_empresa;
                }

                $registro = $this->setParameters($columns,$registro);
                if ($agenda = enderecoModel::set(...$registro)){
                    $result[] = "agenda com Id ({$agenda}) salva com sucesso";
                    $idsalvos[] = $agenda;
                }
                else{
                    $errors[] = mensagem::getErro();
                }
            }

            $this->sendResponse(["result" => $result,"id_salvos"=> $idsalvos,"errors" => $errors]);

        } catch(\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage(),"result" => false],400);
        }
    }

    /**
     * Exclui um endereço pelo ID.
     *
     * @param array $parameters Parâmetros da requisição, incluindo o ID do endereço.
     */
    public function delete($parameters)
    {
        try {
            $this->validRequest(['DELETE']);

            if (!isset($parameters[0])) {
                $this->sendResponse(['error' => 'ID do endereço não informado', 'result' => false], 400);
            }

            $result = enderecoModel::delete($parameters[0]);

            if ($result) {
                $this->sendResponse(['result' => 'Endereço excluído com sucesso']);
            } else {
                $this->sendResponse(['error' => 'Falha ao excluir endereço', 'result' => false], 400);
            }

        } catch (\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage(), 'result' => false], 400);
        }
    }
}
