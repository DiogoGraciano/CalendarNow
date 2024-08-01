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
     * Construtor da classe.
     *
     * @param string $requestType Tipo de requisição HTTP.
     * @param mixed $data Dados enviados na requisição.
     */
    public function __construct($requestType, $data){
        $this->requestType = $requestType;
        $this->data = $data;
    }

      /**
     * Obtém agendas por IDs ou deleta agendas por IDs.
     *
     * @param array $parameters Parâmetros da requisição.
     */
    public function getByIds($parameters):void{
        try {
             
            if($this->requestType !== 'GET' || !empty($_GET) || $this->requestType !== 'DELETE'){
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
            echo json_encode(['error' => $e->getMessage(),"result" => false]);
            http_response_code(400);
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
            if($this->requestType === 'POST' && $this->data){
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
                echo json_encode(["result" => $result,"id_salvos"=> $idsalvos,"errors" => $errors]);
            }else{
                echo json_encode(['error' => "Modo da requisição inválido ou Json enviado inválido","result" => false]); 
                http_response_code(400);
            }
        } catch(\Exception $e) {
            echo json_encode(['error' => $e->getMessage(),"result" => false]);
        }
    }

    public function detachAgendaFuncionario($parameters = []){

        $id_agenda = functions::decrypt($parameters[0] ?? '');
        $id_funcionario = functions::decrypt($parameters[1] ?? '');

        if($id_agenda && $id_funcionario){
            FuncionarioModel::detachAgendaFuncionario($id_agenda,$id_funcionario);
            $this->go("funcionario/manutencao/".$parameters[1]);
        }

        mensagem::setErro("Agenda ou Funcionario não informados");
        $this->go("funcionario");
    }
}