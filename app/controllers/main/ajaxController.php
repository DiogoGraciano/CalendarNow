<?php
namespace app\controllers\main;
use app\helpers\functions;
use app\controllers\abstract\controller;
use app\helpers\integracaoWs;
use app\models\main\cidadeModel;
use app\models\main\estadoModel;
use app\models\main\usuarioModel;

class ajaxController extends controller{

    public function index(){
        $method = $this->getValue("method");
        $parameters = $this->getValue("parameters");
        if ($method && method_exists($this,$method)){
            $this->$method($parameters); 
        }
        else{
            $retorno = ["sucesso" => False,
                        "retorno" => "Função não Encontrada"];
            echo json_encode($retorno);
        }
    }

    public function getCidadeOption($id_estado){
        echo json_encode(["sucesso" => True,
                          "retorno" => cidadeModel::getByEstado($id_estado)]);
    }
    
    public function getEmpresa($cnpj){
        $integracao = new integracaoWs;
        $retorno = $integracao->getEmpresa($cnpj);

        if ($retorno && is_object($retorno)){
            $retorno = ["sucesso" => True,
                        "retorno" => $retorno];
        }
        else{
            $retorno = ["sucesso" => False,
            "retorno" => $retorno];
        }

        echo json_encode($retorno);
    }
    public function getEndereco($cep){
        $integracao = new integracaoWs;
        $retorno = $integracao->getEndereco($cep);

        if ($retorno && is_object($retorno)){
            $cidade = "";
            $estado = estadoModel::getByUf($retorno->uf);
            if (array_key_exists(0,$estado)){
                $retorno->uf = $estado[0]->id;
            }
            if (isset($retorno->ibge)){
                $cidade = cidadeModel::getByIbge($retorno->ibge);
            }
            elseif(!$cidade){
                $cidade = cidadeModel::getByNomeIdUf($retorno->localidade,$estado->id);  
            }
            if (array_key_exists(0,$cidade)){
                $retorno->localidade = $cidade[0]->id;
            }
            $retorno = ["sucesso" => True,
            "retorno" => $retorno];
        }
        else{
            $retorno = ["sucesso" => False,
            "retorno" => $retorno];
        }

        echo json_encode($retorno);
    }

    public function existsCpfCnpj($cpf_cnpj){
        $cpf_cnpj = functions::onlynumber($cpf_cnpj);

        $usuario = usuarioModel::getByCpfCnpj($cpf_cnpj);

        if($usuario)
            $retorno = True;
        else 
            $retorno = False;

        $retorno = ["sucesso" => True,
                    "retorno" => $retorno];
            
        echo json_encode($retorno); 
    }

    public function existsEmail($email){
        $usuario = usuarioModel::getByEmail($email);

        if(array_key_exists(0,$usuario) && !array_key_exists(1,$usuario))
            $retorno = True;
        else 
            $retorno = False;

        $retorno = ["sucesso" => True,
                    "retorno" => $retorno];
                 
        echo json_encode($retorno); 
    }
}

?>