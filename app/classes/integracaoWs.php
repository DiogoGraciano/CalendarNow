<?php 

namespace app\classes;
use stdClass;
use Exception;

/**
 * Classe para integração com serviços web externos.
 * Esta classe oferece métodos para obter informações de empresas e endereços usando diferentes serviços web.
 */
class integracaoWs{

    /**
     * Obtém informações da empresa com base no CNPJ fornecido.
     *
     * @param string $cnpj CNPJ da empresa.
     * @return mixed Retorna os dados da empresa em formato JSON ou False em caso de falha.
     */
    public function getEmpresa(string $cnpj){
       
        $url = "https://receitaws.com.br/v1/cnpj/".$cnpj;

        return $this->getResult($url);
    }

    /**
     * Obtém informações de endereço com base no CEP fornecido.
     * Utiliza múltiplos serviços web para obter os dados e retorna a primeira resposta válida.
     *
     * @param string|int $cep CEP do endereço.
     * @return stdClass|null Retorna um objeto contendo os dados do endereço ou null se nenhum resultado válido for encontrado.
     */
    public function getEndereco(string|int $cep){

        $urls = array(
            'https://viacep.com.br/ws/'. $cep . '/json/',
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

    /**
     * Realiza uma requisição HTTP GET para o URL fornecido e retorna a resposta decodificada em JSON.
     *
     * @param string $urlCompleta URL completa para a requisição.
     * @return mixed Retorna os dados da resposta em formato JSON ou False em caso de falha.
     */
    private function getResult(string $urlCompleta){

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

?>
