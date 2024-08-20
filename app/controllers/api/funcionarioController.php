<?php 
namespace app\controllers\api;
use app\controllers\abstract\apiController;
use app\helpers\mensagem;
use app\models\api\funcionarioModel;

class funcionarioController extends apiController{

    /**
     * Construtor da classe.
     *
     * @param string $requestType Tipo de requisição HTTP.
     * @param mixed $data Dados enviados na requisição.
     * @param mixed $query Query strings enviadas na requisição.
     */
    public function __construct($requestType, $data, $query){
        $this->requestType = $requestType;
        $this->data = $data;
        $this->query = $query;
    }

    /**
     * Obtém funcionários por IDs ou deleta funcionários por IDs.
     *
     * @param array $parameters Parâmetros da requisição.
     */
    public function getByIds($parameters):void{
        try {

            $this->validRequest(['GET', 'DELETE']);

            $funcionarios = [];
            $errors = []; 
            $funcionariosEncontrados = []; 
            $funcionariosNaoEncontrados = [];

            $funcionarioList = funcionarioModel::getbyIds($parameters);
            foreach ($funcionarioList as $funcionario){
                if ($this->requestType === 'GET' && $funcionario["id"])
                    $funcionarios[] = $funcionario;
                if ($this->requestType === 'DELETE' && $funcionario["id"] && funcionarioModel::delete($funcionario["id"]))
                    $funcionarios[] = "Funcionário com Id ({$funcionario['id']}) deletado com sucesso";
                else
                    $errors[] = "Erro ao deletar funcionário com Id ({$funcionario['id']})";

                $funcionariosEncontrados[] = $funcionario["id"];
            }

            foreach ($parameters as $id){
                if(!in_array($id,$funcionariosEncontrados)){
                    $funcionariosNaoEncontrados[] = $id;
                }
            }

            $errors[] = "Funcionário(s) com Id(s) ".json_encode($funcionariosNaoEncontrados)." não encontrado(s)";

            $this->sendResponse(["result" => $funcionarios, "errors" => $errors, "id_errors" => $funcionariosNaoEncontrados]);
            
        } catch(\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage(),"result" => false],400);
        }
    }

    /**
     * Define ou atualiza funcionários.
     */
    public function set(){
        try {
            $errors = [];
            $result = []; 
            $idsalvos = [];

            $this->validRequest(['POST']);

            if(!$this->data){
                $this->sendResponse(['error' => "Body da requisição não informado","result" => false],400);
            }

            $columns = $this->getMethodsArgNames("app\models\api\funcionarioModel","set");
            foreach ($this->data as $registro){
                
                $registro = $this->setParameters($columns, $registro);
                if ($funcionario = funcionarioModel::set(...$registro)){
                    $result[] = "Funcionário com Id ({$funcionario}) salvo com sucesso";
                    $idsalvos[] = $funcionario;
                } else {
                    $errors[] = mensagem::getErro();
                }
            }

            $this->sendResponse(["result" => $result,"id_salvos"=> $idsalvos,"errors" => $errors]);

        } catch(\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage(),"result" => false],400);
        }
    }

    /**
     * Associa um funcionário a uma agenda.
     */
    public function setAgendaFuncionario(){
        try {
            $errors = [];
            $result = []; 
            $idsalvos = [];

            $this->validRequest(['POST']);

            if(!$this->data){
                $this->sendResponse(['error' => "Body da requisição não informado","result" => false],400);
            }

            $columns = $this->getMethodsArgNames("app\models\api\funcionarioModel","setAgendaFuncionario");
            foreach ($this->data as $registro){
                if (isset($registro["id_funcionario"],$registro["id_agenda"])){
                    $registro = $this->setParameters($columns,$registro);
                    if ($agenda = funcionarioModel::setAgendaFuncionario(...$registro)){
                        $result[] = "Agenda associada ao funcionário com Id ({$agenda}) salva com sucesso";
                        $idsalvos[] = $agenda;
                    } else {
                        $errors[] = mensagem::getErro();
                    }
                } else {
                    $errors[] = "Informações de agenda e funcionário não informadas corretamente";
                }
            }

            $this->sendResponse(["result" => $result,"id_salvos"=> $idsalvos,"errors" => $errors]);

        } catch(\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage(),"result" => false],400);
        }
    }

    /**
     * Associa um funcionário a um grupo de funcionários.
     */
    public function setFuncionarioGrupoFuncionario(){
        try {
            $errors = [];
            $result = []; 
            $idsalvos = [];

            $this->validRequest(['POST']);

            if(!$this->data){
                $this->sendResponse(['error' => "Body da requisição não informado","result" => false],400);
            }

            $columns = $this->getMethodsArgNames("app\models\api\funcionarioModel","setFuncionarioGrupoFuncionario");
            foreach ($this->data as $registro){
                if (isset($registro["id_funcionario"],$registro["id_grupo_funcionario"])){
                    $registro = $this->setParameters($columns,$registro);
                    if ($grupo = funcionarioModel::setFuncionarioGrupoFuncionario(...$registro)){
                        $result[] = "Grupo de funcionários associado ao funcionário com Id ({$grupo}) salvo com sucesso";
                        $idsalvos[] = $grupo;
                    } else {
                        $errors[] = mensagem::getErro();
                    }
                } else {
                    $errors[] = "Informações de grupo de funcionários e funcionário não informadas corretamente";
                }
            }

            $this->sendResponse(["result" => $result,"id_salvos"=> $idsalvos,"errors" => $errors]);

        } catch(\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage(),"result" => false],400);
        }
    }

    /**
     * Obtém lista de funcionários por empresa.
     *
     * @param array $parameters Parâmetros da requisição.
     */
    public function getAll($parameters = []){
        try {

            $this->validRequest(['GET']);

            $id_empresa = isset($this->query["id_empresa"]) ? $this->query["id_empresa"] : 0;

            if(!$id_empresa){
                $this->sendResponse(['error' => "Empresa é obrigatória","result" => false],400);
            }

            $nome = isset($this->query["nome"]) ? $this->query["nome"] : null;
            $id_agenda = isset($this->query["id_agenda"]) ? $this->query["id_agenda"] : null;
            $id_grupo_funcionarios = isset($this->query["id_grupo_funcionarios"]) ? $this->query["id_grupo_funcionarios"] : null;
            $limit = isset($this->query["limit"]) ? $this->query["limit"] : 100;
            $offset = isset($this->query["offset"]) ? $this->query["offset"] : null;

            $funcionarios = funcionarioModel::getByEmpresa($id_empresa, $nome, $id_agenda, $id_grupo_funcionarios, $limit, $offset);

            $this->sendResponse(["result" => $funcionarios]);

        } catch(\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage(),"result" => false],400);
        }
    }

}
