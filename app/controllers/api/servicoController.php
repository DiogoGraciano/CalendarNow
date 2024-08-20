<?php

namespace app\controllers\api;

use app\controllers\abstract\apiController;
use app\helpers\mensagem;
use app\models\api\servicoModel;

class servicoController extends apiController{

    public function __construct($requestType, $data, $query){
        $this->requestType = $requestType;
        $this->data = $data;
        $this->query = $query;
    }

    public function getByIds($parameters): void {
        try {
            $this->validRequest(['GET', 'DELETE']);

            $servicos = [];
            $errors = []; 
            $servicosEncontrados = []; 
            $servicosNaoEncontrados = [];

            $servicos = servicoModel::get($parameters);
            foreach ($servicos as $servico){
                if ($this->requestType === 'GET' && $servico->id)
                    $servicos[] = $servico;
                if ($this->requestType === 'DELETE' && $servico->id && servicoModel::delete($servico->id))
                    $servicos[] = "Serviço com Id ({$servico->id}) deletado com sucesso";
                else
                    $errors[] = "Erro ao deletar serviço com Id ({$servico->id})";

                $servicosEncontrados[] = $servico->id;
            }

            foreach ($parameters as $id){
                if(!in_array($id,$servicosEncontrados)){
                    $servicosNaoEncontrados[] = $id;
                }
            }

            $errors[] = "Serviço(s) com Id(s) ".json_encode($servicosNaoEncontrados)." não encontrado(s)";

            $this->sendResponse(["result" => $servicos, "errors" => $errors, "id_errors" => $servicosNaoEncontrados]);
            
        } catch(\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage(),"result" => false],400);
        }
    }

    public function set(): void {
        try {
            $errors = [];
            $result = []; 
            $idsalvos = [];

            $this->validRequest(['POST']);

            if(!$this->data){
                $this->sendResponse(['error' => "Body da requisão não informado","result" => false],400);
            }

            $columns = $this->getMethodsArgNames("app\models\api\servicoModel","set");
            foreach ($this->data as $registro){
                
                $registro = $this->setParameters($columns,$registro);
                if ($servico = servicoModel::set(...$registro)){
                    $result[] = "Serviço com Id ({$servico}) salvo com sucesso";
                    $idsalvos[] = $servico;
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

    public function getAll($parameters = []): void {
        try {
            $this->validRequest(['GET']);

            $id_empresa = isset($this->query["id_empresa"]) ? $this->query["id_empresa"] : 0;

            if(!$id_empresa){
                $this->sendResponse(['error' => "Empresa é obrigatória","result" => false],400);
            }

            $nome = isset($this->query["nome"]) ? $this->query["nome"] : null;
            $id_funcionario = isset($this->query["id_funcionario"]) ? $this->query["id_funcionario"] : null;
            $id_grupo_servico = isset($this->query["id_grupo_servico"]) ? $this->query["id_grupo_servico"] : null;
            $limit = isset($this->query["limit"]) ? $this->query["limit"] : 100;
            $offset = isset($this->query["offset"]) ? $this->query["offset"] : null;

            $result = servicoModel::getListByEmpresa($id_empresa, $nome, $id_funcionario, $id_grupo_servico, $limit, $offset);

            if($result){
                $this->sendResponse(["result" => $result]);
            }

            $this->sendResponse(['error' => mensagem::getErro(),"result" => false]);

        } catch (\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage(),"result" => false],400);
        }
    }

    public function setServicoFuncionario(): void {
        try {
            $errors = [];
            $result = []; 

            $this->validRequest(['POST']);

            if(!$this->data){
                $this->sendResponse(['error' => "Body da requisição não informado","result" => false],400);
            }

            foreach ($this->data as $registro){
                if (isset($registro["id_funcionario"], $registro["id_servico"])){
                    if ($servicoFuncionario = servicoModel::setServicoFuncionario($registro["id_servico"], $registro["id_funcionario"])){
                        $result[] = "Serviço vinculado ao funcionário com sucesso";
                    }
                    else{
                        $errors[] = mensagem::getErro();
                    }
                }
                else {
                    $errors[] = "Serviço ou funcionário não informado corretamente";
                }
            }

            $this->sendResponse(["result" => $result, "errors" => $errors]);

        } catch(\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage(),"result" => false],400);
        }
    }

    public function detachFuncionario($parameters = []): void {
        try {
            $this->validRequest(['DELETE']);

            $id_servico = isset($this->query["id_servico"]) ? $this->query["id_servico"] : 0;
            $id_funcionario = isset($this->query["id_funcionario"]) ? $this->query["id_funcionario"] : 0;

            if($id_servico && $id_funcionario && servicoModel::detachFuncionario($id_funcionario, $id_servico)){
                $this->sendResponse(["result" => true]);
            }

            $this->sendResponse(['error' => "Erro ao desvincular funcionário do serviço","result" => false],400);

        } catch(\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage(),"result" => false],400);
        }
    }

    public function delete($parameters): void {
        try {
            $this->validRequest(['DELETE']);

            $id = $parameters[0] ?? null;
            if (!$id) {
                $this->sendResponse(['error' => "Id do serviço não informado", "result" => false], 400);
            }

            if (servicoModel::delete($id)) {
                $this->sendResponse(["result" => "Serviço deletado com sucesso"]);
            } else {
                $this->sendResponse(['error' => mensagem::getErro(), "result" => false], 400);
            }
        } catch (\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage(), "result" => false], 400);
        }
    }
}
