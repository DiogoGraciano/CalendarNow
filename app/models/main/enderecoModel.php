<?php
namespace app\models\main;

use app\db\endereco;
use app\classes\mensagem;
use app\classes\modelAbstract;
use app\classes\functions;
use app\models\main\cidadeModel;

/**
 * Classe enderecoModel
 * 
 * Esta classe fornece métodos para interagir com os dados de endereços.
 * Ela utiliza a classe endereco para realizar operações de consulta, inserção e exclusão no banco de dados.
 * 
 * @package app\models\main
 */
class enderecoModel{

    /**
     * Obtém um endereço pelo ID.
     * 
     * @param string $id O ID do endereço a ser buscado.
     * @return array|null Retorna os dados do endereço ou null se não encontrado.
     */
    public static function get($id = ""){
        return (new endereco)->get($id);
    }

    /**
     * Obtém endereços por ID de usuário.
     * 
     * @param string $id_usuario O ID do usuário para buscar endereços.
     * @return array Retorna um array de endereços.
     */
    public static function getbyIdUsuario($id_usuario = ""):array
    {
        $db = new endereco;
        $values = $db->addFilter("id_usuario","=",$id_usuario)->selectAll();
        
        if ($db->getError()){
            return [];
        }

        return $values;
    }

    /**
     * Insere ou atualiza um endereço.
     * 
     * @param string $cep O CEP do endereço.
     * @param int $id_estado O ID do estado associado.
     * @param int $id_cidade O ID da cidade associada.
     * @param string $bairro O bairro do endereço.
     * @param string $rua A rua do endereço.
     * @param string $numero O número do endereço.
     * @param string $complemento O complemento do endereço (opcional).
     * @param int $id O ID do endereço (opcional).
     * @param int $id_usuario O ID do usuário associado (opcional).
     * @param int $id_empresa O ID da empresa associada (opcional).
     * @return string|bool Retorna o ID do endereço inserido ou atualizado se a operação for bem-sucedida, caso contrário retorna false.
     */
    public static function set(string $cep,int $id_estado,int $id_cidade,string $bairro,string $rua,string $numero,string $complemento = null,int $id = null,int $id_usuario = null,int $id_empresa = null){
        $db = new endereco;
        $mensagens = [];

        if(!functions::validaCep($cep)){
            $mensagens[] = "CEP é invalido";
        }

        if(!estadoModel::get($id_estado)->id){
            $mensagens[] = "Estado é invalido";
        }

        if(!cidadeModel::get($id_cidade)->id){
            $mensagens[] = "Cidade é invalida";
        }

        if(!filter_var($bairro)){
            $mensagens[] = "Bairro é Invalido";
        }

        if(!filter_var($rua)){
            $mensagens[] = "Rua é Invalido";
        }

        if(!filter_var($numero)){
            $mensagens[] = "Numero é Invalido";
        }

        if(!filter_var($complemento)){
            $mensagens[] = "Complemento é Invalido";
        }

        if(!$id_usuario && !$id_empresa){
            $mensagens[] = "Usuario ou Empresa precisa ser informado para cadastro";
        }

        if($mensagens){
            mensagem::setErro(...$mensagens);
            return false;
        }

        $values = $db->getObject();

        $values->id = intval($id);
        $values->id_usuario = intval($id_usuario);
        $values->id_empresa = intval($id_empresa);
        $values->cep = (int)functions::onlynumber($cep);
        $values->id_estado = intval($id_estado);
        $values->id_cidade = intval($id_cidade);
        $values->bairro = trim($bairro);
        $values->rua = trim($rua);
        $values->numero = trim($numero);
        $values->complemento = trim($complemento);
        $retorno = $db->store($values);

        if ($retorno == true){
            mensagem::setSucesso("Endereço salva com sucesso");
            return $db->getLastID();
        }else{
            mensagem::setErro("Erro ao cadastrar a endereço");
            return False;
        }
    }

    /**
     * Exclui um registro de endereço.
     * 
     * @param string $id O ID do endereço a ser excluído.
     * @return bool Retorna true se a operação for bem-sucedida, caso contrário retorna false.
     */
    public static function delete($id){
       return (new endereco)->delete($id);
    }
}
