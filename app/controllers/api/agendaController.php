<?php 
namespace app\controllers\api;
use app\controllers\abstract\apiController;
use app\helpers\functions;
use app\helpers\mensagem;
use app\db\transactionManeger;
use app\models\main\agendaModel;
use app\models\main\usuarioModel;
use app\models\main\funcionarioModel;
use core\session;

class agendaController extends apiController{

    /**
     * Construtor da classe.
     *
     * @param string $requestType Tipo de requisição HTTP.
     * @param mixed $data Dados enviados na requisição.
     */
    public function __construct($requestType, $data, $query){
        $this->requestType = $requestType;
        $this->data = $data;
        $this->query = $query;
    }

      /**
     * Obtém agendas por IDs ou deleta agendas por IDs.
     *
     * @param array $parameters Parâmetros da requisição.
     */
    public function getByIds($parameters):void{
        try {
            $this->validRequest(['GET',"DELETE"]);

            if($this->requestType !== 'GET' || $this->requestType !== 'DELETE'){
                echo json_encode(['error' => "Modo da requisição inválido ou Json enviado inválido","result" => false]); 
                http_response_code(400);
                return;
            }

            $agendas = [];
            $errors = []; 
            $agendasEncontradas = []; 
            $agendasNaoEncontradas = [];

            $agenda = agendaModel::getbyIds($parameters);
            foreach ($agendas as $agenda){
                if ($this->requestType === 'GET' && $agenda->cd_agenda)
                    $agendas[] = $agenda;
                if ($this->requestType === 'DELETE' && $agenda->cd_agenda && agendaModel::delete($agenda->cd_agenda))
                    $agendas[] = "Agenda com Id ({$agenda->cd_agenda}) deletado com sucesso";
                else
                    $errors[] = "Erro ao deletar agenda com Id ({$agenda->cd_agenda})";

                $agendasEncontradas[] = $agenda->cd_agenda;
            }

            foreach ($agendasEncontradas as $id){
                if(!in_array($id,$parameters)){
                    $agendasNaoEncontradas[] = $id;
                }
            }

            $errors[] = "agenda(s) com Id(s) ".json_encode($agendasNaoEncontradas)." não encontrada(s)";

            echo json_encode(["result" => $agendas, "errors" => $errors, "id_errors" => $agendasNaoEncontradas]);
            
        } catch(\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage(),"result" => false],400);
        }
    }

    /**
     * Define ou atualiza agendas.
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

            $columns = $this->getMethodsArgNames("app\models\main\agendaModel","set");
            foreach ($this->data as $registro){
                if (isset($registro["nome"],$registro["id_empresa"],$registro["codigo"])){
                    $registro = $this->setParameters($columns,$registro);
                    if ($agenda = agendaModel::set(...$registro)){
                        $result[] = "agenda com Id ({$agenda}) salva com sucesso";
                        $idsalvos[] = $agenda;
                    }
                    else{
                        $errors[] = mensagem::getErro();
                    }
                }
                else
                    $errors[] = "agenda não informado corretamente";
            }

            $this->sendResponse(["result" => $result,"id_salvos"=> $idsalvos,"errors" => $errors]);

        } catch(\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage(),"result" => false],400);
        }
    }

    public function getByEmpresa($parameters = []){
        try {

            $this->validRequest(['GET']);

            $id_empresa = isset($this->query["id_empresa"]) ? $this->query["id_empresa"] : 0;

            if(!$id_empresa){
                $this->sendResponse(['error' => "Empresa é obrigatorio","result" => false],400);
            }

            $nome = isset($this->query["nome"]) ? $this->query["nome"] : null;
            $codigo = isset($this->query["codigo"]) ? $this->query["codigo"] : null;
            $limit = isset($this->query["limit"]) ? $this->query["limit"] : 100;
            $offset = isset($this->query["offset"]) ? $this->query["offset"] : null;

            $result = AgendaModel::getByEmpresa($id_empresa,$nome,$codigo,$limit,$offset);

            if($result){
                $this->sendResponse(["result" => $result]);
            }

            $this->sendResponse(['error' => mensagem::getErro(),"result" => false]);

        } catch (\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage(),"result" => false],400);
        }
    }

    public function detachAgendaFuncionario($parameters = []){
        try {
            $this->validRequest(['DELETE']);

            $id_agenda = isset($this->query["id_agenda"]) ? $this->query["id_agenda"] : 0;
            $id_funcionario = isset($this->query["id_funcionario"]) ? $this->query["id_funcionario"] : 0;

            if($id_agenda && $id_funcionario && FuncionarioModel::detachAgendaFuncionario($id_agenda,$id_funcionario)){
                $this->sendResponse(["result" => true]);
            }

            $this->sendResponse(['error' => "Erro ao desvincular funcionario da agenda","result" => false],400);

        } catch(\Exception $e) {
            $this->sendResponse(['error' => $e->getMessage(),"result" => false],400);
        }
    }
}