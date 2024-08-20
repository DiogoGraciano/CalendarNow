<?php
namespace app\controllers\api;

use app\controllers\abstract\apiController;
use app\models\api\empresaModel;
use app\helpers\mensagem;

class empresaController extends apiController {

    public function __construct($requestType, $data, $query){
        $this->requestType = $requestType;
        $this->data = $data;
        $this->query = $query;
    }

    /**
     * Obtém empresas por IDs ou deleta empresas por IDs.
     *
     * @param array $parameters Parâmetros da requisição.
     */
    public function getByIds($parameters): void {
        try {
            $this->validRequest(['GET', 'DELETE']);

            $empresas = [];
            $errors = [];
            $empresasEncontradas = [];
            $empresasNaoEncontradas = [];

            $empresa = empresaModel::getbyIds($parameters);

            foreach ($empresa as $e) {
                if ($this->requestType === 'GET' && $e['id']) {
                    $empresas[] = $e;
                }
                if ($this->requestType === 'DELETE' && $e['id'] && empresaModel::delete($e['id'])) {
                    $empresas[] = "Empresa com ID ({$e['id']}) deletada com sucesso";
                } else {
                    $errors[] = "Erro ao deletar empresa com ID ({$e['id']})";
                }

                $empresasEncontradas[] = $e['id'];
            }

            foreach ($parameters as $id) {
                if (!in_array($id, $empresasEncontradas)) {
                    $empresasNaoEncontradas[] = $id;
                }
            }

            if (count($empresasNaoEncontradas) > 0) {
                $errors[] = "Empresa(s) com ID(s) " . json_encode($empresasNaoEncontradas) . " não encontrada(s)";
            }

            $this->sendResponse(["result" => $empresas, "errors" => $errors, "id_errors" => $empresasNaoEncontradas]);

        } catch (\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage(), "result" => false], 400);
        }
    }

    /**
     * Define ou atualiza empresas.
     */
    public function set(): void {
        try {
            $errors = [];
            $result = [];
            $idsSalvos = [];

            $this->validRequest(['POST']);

            if (!$this->data) {
                $this->sendResponse(['error' => "Body da requisição não informado", "result" => false], 400);
            }

            $columns = $this->getMethodsArgNames("app\models\api\empresaModel", "set");
            foreach ($this->data as $registro) {
                $registro = $this->setParameters($columns, $registro);
                if ($empresaId = empresaModel::set(...$registro)) {
                    $result[] = "Empresa com ID ({$empresaId}) salva com sucesso";
                    $idsSalvos[] = $empresaId;
                } else {
                    $errors[] = mensagem::getErro();
                }
            }

            $this->sendResponse(["result" => $result, "id_salvos" => $idsSalvos, "errors" => $errors]);

        } catch (\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage(), "result" => false], 400);
        }
    }

    /**
     * Obtém todas as empresas.
     */
    public function getAll($parameters = []): void {
        try {
            $this->validRequest(['GET']);

            $result = empresaModel::getAll();

            if ($result) {
                $this->sendResponse(["result" => $result]);
            } else {
                $this->sendResponse(['error' => mensagem::getErro(), "result" => false]);
            }

        } catch (\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage(), "result" => false], 400);
        }
    }

    /**
     * Obtém empresa por agenda.
     */
    public function getByAgenda(): void {
        try {
            $this->validRequest(['GET']);

            $id_agenda = $this->query['id_agenda'] ?? null;

            if (!$id_agenda) {
                $this->sendResponse(['error' => "ID da agenda não informado", "result" => false], 400);
            }

            $result = empresaModel::getByAgenda($id_agenda);

            if ($result) {
                $this->sendResponse(["result" => $result]);
            } else {
                $this->sendResponse(['error' => mensagem::getErro(), "result" => false]);
            }

        } catch (\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage(), "result" => false], 400);
        }
    }
}
