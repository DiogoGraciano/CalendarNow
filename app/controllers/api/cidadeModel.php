<?php 
namespace app\controllers\api;

use app\controllers\abstract\apiController;
use app\models\api\cidadeModel;

/**
 * Classe cidadeController
 * 
 * Este controlador lida com as requisições da API relacionadas a cidades.
 * 
 * @package app\controllers\api
 */
class cidadeController extends apiController{

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
     * Obtém uma cidade pelo ID.
     *
     * @param array $parameters Parâmetros da requisição, incluindo o ID da cidade.
     */
    public function getById($parameters):void{
        try {
            $this->validRequest(['GET']);

            if (!isset($parameters[0])) {
                $this->sendResponse(['error' => 'ID da cidade não informado', 'result' => false], 400);
            }

            $cidade = cidadeModel::get($parameters[0]);

            if ($cidade) {
                $this->sendResponse(['result' => $cidade]);
            } else {
                $this->sendResponse(['error' => 'Cidade não encontrada', 'result' => false], 404);
            }

        } catch (\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage(), 'result' => false], 400);
        }
    }

    /**
     * Obtém uma cidade pelo nome.
     */
    public function getByNome():void{
        try {
            $this->validRequest(['GET']);

            $nome = isset($this->query['nome']) ? $this->query['nome'] : null;

            if (!$nome) {
                $this->sendResponse(['error' => 'Nome da cidade não informado', 'result' => false], 400);
            }

            $cidade = cidadeModel::getByNome($nome);

            if ($cidade) {
                $this->sendResponse(['result' => $cidade]);
            } else {
                $this->sendResponse(['error' => 'Cidade não encontrada', 'result' => false], 404);
            }

        } catch (\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage(), 'result' => false], 400);
        }
    }

    /**
     * Obtém uma cidade pelo nome e ID do estado (UF).
     */
    public function getByNomeUf():void{
        try {
            $this->validRequest(['GET']);

            $nome = isset($this->query['nome']) ? $this->query['nome'] : null;
            $uf = isset($this->query['uf']) ? $this->query['uf'] : null;

            if (!$nome || !$uf) {
                $this->sendResponse(['error' => 'Nome da cidade ou UF não informados', 'result' => false], 400);
            }

            $cidade = cidadeModel::getByNomeIdUf($nome, $uf);

            if ($cidade) {
                $this->sendResponse(['result' => $cidade]);
            } else {
                $this->sendResponse(['error' => 'Cidade não encontrada', 'result' => false], 404);
            }

        } catch (\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage(), 'result' => false], 400);
        }
    }

    /**
     * Obtém uma cidade pelo código IBGE.
     */
    public function getByIbge():void{
        try {
            $this->validRequest(['GET']);

            $ibge = isset($this->query['ibge']) ? $this->query['ibge'] : null;

            if (!$ibge) {
                $this->sendResponse(['error' => 'Código IBGE não informado', 'result' => false], 400);
            }

            $cidade = cidadeModel::getByIbge($ibge);

            if ($cidade) {
                $this->sendResponse(['result' => $cidade]);
            } else {
                $this->sendResponse(['error' => 'Cidade não encontrada', 'result' => false], 404);
            }

        } catch (\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage(), 'result' => false], 400);
        }
    }

    /**
     * Obtém cidades por UF (estado).
     */
    public function getByUf():void{
        try {
            $this->validRequest(['GET']);

            $uf = isset($this->query['uf']) ? $this->query['uf'] : null;

            if (!$uf) {
                $this->sendResponse(['error' => 'UF não informado', 'result' => false], 400);
            }

            $cidades = cidadeModel::getByEstado($uf);

            if ($cidades) {
                $this->sendResponse(['result' => $cidades]);
            } else {
                $this->sendResponse(['error' => 'Nenhuma cidade encontrada', 'result' => false], 404);
            }

        } catch (\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage(), 'result' => false], 400);
        }
    }
}
