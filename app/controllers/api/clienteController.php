<?php 
namespace app\controllers\api;

use app\controllers\abstract\apiController;
use app\helpers\mensagem;
use app\models\api\clienteModel;

class clienteController extends apiController{

    /**
     * Construtor da classe.
     *
     * @param string $requestType Tipo de requisição HTTP.
     * @param mixed $data Dados enviados na requisição.
     * @param mixed $query Parâmetros da query string.
     */
    public function __construct($requestType, $data, $query){
        $this->requestType = $requestType;
        $this->data = $data;
        $this->query = $query;
    }

    /**
     * Obtém clientes por IDs ou deleta clientes por IDs.
     *
     * @param array $parameters Parâmetros da requisição.
     */
    public function getByIds($parameters) 
    {
        try {
            $this->validRequest(['GET', 'DELETE']);

            $clientes = [];
            $errors = []; 
            $clientesEncontrados = []; 
            $clientesNaoEncontrados = [];

            $clientesData = clienteModel::getbyIds($parameters);
            foreach ($clientesData as $cliente) {
                if ($this->requestType === 'GET' && $cliente["id"])
                    $clientes[] = $cliente;
                if ($this->requestType === 'DELETE' && $cliente["id"] && clienteModel::delete($cliente["id"]))
                    $clientes[] = "Cliente com Id ({$cliente['id']}) deletado com sucesso";
                else
                    $errors[] = "Erro ao deletar cliente com Id ({$cliente['id']})";

                $clientesEncontrados[] = $cliente["id"];
            }

            foreach ($parameters as $id) {
                if(!in_array($id, $clientesEncontrados)) {
                    $clientesNaoEncontrados[] = $id;
                }
            }

            if (!empty($clientesNaoEncontrados)) {
                $errors[] = "Cliente(s) com Id(s) " . json_encode($clientesNaoEncontrados) . " não encontrado(s)";
            }

            $this->sendResponse(["result" => $clientes, "errors" => $errors, "id_errors" => $clientesNaoEncontrados]);
            
        } catch(\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage(), "result" => false], 400);
        }
    }

    /**
     * Define ou atualiza clientes.
     */
    public function set() 
    {
        try {
            $errors = [];
            $result = []; 
            $idsSalvos = [];

            $this->validRequest(['POST']);

            if(!$this->data) {
                $this->sendResponse(['error' => "Body da requisição não informado", "result" => false], 400);
            }

            $columns = $this->getMethodsArgNames("app\models\api\clienteModel", "set");
            foreach ($this->data as $registro) {
                if (isset($registro["nome"], $registro["id_funcionario"])) {
                    $registro = $this->setParameters($columns, $registro);
                    if ($cliente = clienteModel::set(...$registro)) {
                        $result[] = "Cliente com Id ({$cliente}) salvo com sucesso";
                        $idsSalvos[] = $cliente;
                    } else {
                        $errors[] = mensagem::getErro();
                    }
                } else {
                    $errors[] = "Cliente não informado corretamente";
                }
            }

            $this->sendResponse(["result" => $result, "id_salvos" => $idsSalvos, "errors" => $errors]);

        } catch(\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage(), "result" => false], 400);
        }
    }

    /**
     * Obtém todos os clientes por empresa.
     */
    public function getAll() 
    {
        try {
            $this->validRequest(['GET']);

            $id_empresa = isset($this->query["id_empresa"]) ? $this->query["id_empresa"] : 0;

            if(!$id_empresa) {
                $this->sendResponse(['error' => "Empresa é obrigatória", "result" => false], 400);
            }

            $limit = isset($this->query["limit"]) ? $this->query["limit"] : 100;
            $offset = isset($this->query["offset"]) ? $this->query["offset"] : null;

            $result = clienteModel::getByEmpresa($id_empresa, $limit, $offset);

            if($result) {
                $this->sendResponse(["result" => $result]);
            }

            $this->sendResponse(['error' => mensagem::getErro(), "result" => false]);

        } catch(\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage(), "result" => false], 400);
        }
    }

    /**
     * Obtém todos os clientes por usuário.
     */
    public function getAllByUsuario($parameters = []) 
    {
        try {
            $this->validRequest(['GET']);

            $id_usuario = isset($parameters[0]) ? $parameters[0] : null;

            if(!$id_usuario) {
                $this->sendResponse(["error" => "Id do usuário não informado", "result" => false], 400);
            }

            $result = clienteModel::getByUsuario($id_usuario);

            if($result) {
                $this->sendResponse(["result" => $result]);
            }

            $this->sendResponse(['error' => "Resultado não encontrado", "result" => false]);

        } catch(\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage(), "result" => false], 400);
        }
    }

    /**
     * Exclui um cliente.
     */
    public function delete($parameters) 
    {
        try {
            $this->validRequest(['DELETE']);

            $id = isset($parameters[0]) ? $parameters[0] : null;

            if(!$id) {
                $this->sendResponse(["error" => "Id do cliente não informado", "result" => false], 400);
            }

            if(clienteModel::delete($id)) {
                $this->sendResponse(["result" => "Cliente com Id ({$id}) deletado com sucesso"]);
            }

            $this->sendResponse(['error' => "Erro ao deletar cliente", "result" => false]);

        } catch(\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage(), "result" => false], 400);
        }
    }

}
