<?php 
namespace app\controllers\api;

use app\controllers\abstract\apiController;
use app\helpers\functions;
use app\helpers\mensagem;
use app\models\api\usuarioModel;

class usuarioController extends apiController{

    public function __construct($requestType, $data, $query){
        $this->requestType = $requestType;
        $this->data = $data;
        $this->query = $query;
    }

    /**
     * Obtém usuários por IDs ou deleta usuários por IDs.
     *
     * @param array $parameters Parâmetros da requisição.
     */
    public function getByIds($parameters): void {
        try {
            $this->validRequest(['GET', 'DELETE']);

            $usuarios = [];
            $errors = [];
            $usuariosEncontrados = [];
            $usuariosNaoEncontrados = [];

            $usuario = usuarioModel::getbyIds($parameters);

            foreach ($usuario as $u) {
                if ($this->requestType === 'GET' && $u['id']) {
                    $usuarios[] = $u;
                }
                if ($this->requestType === 'DELETE' && $u['id'] && usuarioModel::delete($u['id'])) {
                    $usuarios[] = "Usuário com ID ({$u['id']}) deletado com sucesso";
                } else {
                    $errors[] = "Erro ao deletar usuário com ID ({$u['id']})";
                }

                $usuariosEncontrados[] = $u['id'];
            }

            foreach ($parameters as $id) {
                if (!in_array($id, $usuariosEncontrados)) {
                    $usuariosNaoEncontrados[] = $id;
                }
            }

            if (count($usuariosNaoEncontrados) > 0) {
                $errors[] = "Usuário(s) com ID(s) " . json_encode($usuariosNaoEncontrados) . " não encontrado(s)";
            }

            $this->sendResponse(["result" => $usuarios, "errors" => $errors, "id_errors" => $usuariosNaoEncontrados]);
            
        } catch (\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage(), "result" => false], 400);
        }
    }

    /**
     * Define ou atualiza usuários.
     */
    public function set() {
        try {
            $errors = [];
            $result = [];
            $idsSalvos = [];

            $this->validRequest(['POST']);

            if (!$this->data) {
                $this->sendResponse(['error' => "Body da requisição não informado", "result" => false], 400);
            }

            $columns = $this->getMethodsArgNames("app\models\api\usuarioModel", "set");
            foreach ($this->data as $registro) {
                $registro = $this->setParameters($columns, $registro);
                if ($usuarioId = usuarioModel::set(...$registro)) {
                    $result[] = "Usuário com ID ({$usuarioId}) salvo com sucesso";
                    $idsSalvos[] = $usuarioId;
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
     * Obtém todos os usuários.
     */
    public function getAll($parameters = []) {
        try {
            $this->validRequest(['GET']);

            $id_empresa = isset($this->query['id_empresa']) ? $this->query['id_empresa'] : 0;

            if (!$id_empresa) {
                $this->sendResponse(['error' => "Empresa é obrigatória", "result" => false], 400);
            }

            $nome = isset($this->query['nome']) ? $this->query['nome'] : null;
            $tipo_usuario = isset($this->query['tipo_usuario']) ? $this->query['tipo_usuario'] : null;
            $limit = isset($this->query['limit']) ? $this->query['limit'] : 100;
            $offset = isset($this->query['offset']) ? $this->query['offset'] : null;

            $result = usuarioModel::getByEmpresa($id_empresa, $nome, null, $tipo_usuario, $limit, $offset);

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
     * Bloqueia um usuário em uma agenda.
     */
    public function setBloqueio() {
        try {
            $this->validRequest(['POST']);

            $id_usuario = $this->data['id_usuario'] ?? null;
            $id_agenda = $this->data['id_agenda'] ?? null;

            if ($id_usuario && $id_agenda && usuarioModel::setBloqueio($id_usuario, $id_agenda)) {
                $this->sendResponse(["result" => "Usuário bloqueado com sucesso"]);
            } else {
                $this->sendResponse(['error' => "Erro ao bloquear usuário", "result" => false], 400);
            }

        } catch (\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage(), "result" => false], 400);
        }
    }

    /**
     * Desbloqueia um usuário em uma agenda.
     */
    public function deleteBloqueio() {
        try {
            $this->validRequest(['DELETE']);

            $id_usuario = $this->query['id_usuario'] ?? 0;
            $id_agenda = $this->query['id_agenda'] ?? 0;

            if ($id_usuario && $id_agenda && usuarioModel::deleteBloqueio($id_usuario, $id_agenda)) {
                $this->sendResponse(["result" => "Usuário desbloqueado com sucesso"]);
            } else {
                $this->sendResponse(['error' => "Erro ao desbloquear usuário", "result" => false], 400);
            }

        } catch (\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage(), "result" => false], 400);
        }
    }
}
