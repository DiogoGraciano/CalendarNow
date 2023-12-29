<?php 

namespace app\classes;
use stdClass;
use Exception;

class integracaoWs{

    public function getEmpresa($cnpj){
       
        $url = "https://receitaws.com.br/v1/cnpj/".$cnpj;

        return $this->getResult($url);
    }

    public function getEndereco($cep){

        $urls = array('https://viacep.com.br/ws/'. $cep . '/json/',
        'http://republicavirtual.com.br/web_cep.php?cep='.$cep.'&formato=json'
        );
        
        foreach ($urls as $url){
            if(is_object($retornoWS = $this->getResult($url))){
                if ($url == 'https://viacep.com.br/ws/'. $cep . '/json/')
                    return $retornoWS;
                elseif ($url == 'http://republicavirtual.com.br/web_cep.php?cep='.$cep.'&formato=json'){
                    if($retornoWS->resultado){
                        $retorno = new stdClass;
                        $retorno->cep = $cep;
                        $retorno->uf = $retornoWS->uf;
                        $retorno->logradouro = $retornoWS->tipo_logradouro." ".$retornoWS->logradouro;
                        $retorno->bairro = $retornoWS->bairro;
                        $retorno->localidade = functions::utf8_urldecode($retornoWS->cidade);

                        return $retorno;
                    }
                }
            }
        }
    }

    private function getResult($urlCompleta){

        try{
            $response = file_get_contents($urlCompleta);
            
            if ($response)
                return json_decode($response);
            else 
                return False;
        }
        catch(Exception $e){
            return $e->getMessage();
        }

    }
}