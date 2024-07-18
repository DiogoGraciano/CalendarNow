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
     * @return array|object Retorna os dados do endereço ou null se não encontrado.
     */
    public static function get(string|null|int $value = null,string $column = "id",$limit = 1):array|object
    {
        return (new endereco)->get($value,$column,$limit);
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
     * @param bool $valid_fk valida outras tabelas vinculadas.
     * @return string|bool Retorna o ID do endereço inserido ou atualizado se a operação for bem-sucedida, caso contrário retorna false.
     */
    public static function set(string $cep,int $id_estado,int $id_cidade,string $bairro,string $rua,string $numero,string|null $complemento = null,null|int $id = null,null|int $id_usuario = null,null|int $id_empresa = null,$valid_fk = true){
        $values = new endereco;
        $mensagens = [];

        if(!functions::validaCep($values->cep = functions::onlynumber($cep))){
            $mensagens[] = "CEP é invalido";
        }

        if(!($values->id_estado = estadoModel::get($id_estado)->id)){
            $mensagens[] = "Estado é invalido";
        }

        if(!($values->id_cidade = cidadeModel::get($id_cidade)->id)){
            $mensagens[] = "Cidade é invalida";
        }

        if(!($values->bairro = htmlspecialchars(trim($bairro)))){
            $mensagens[] = "Bairro é Invalido";
        }

        if(!($values->rua = htmlspecialchars(trim($rua)))){
            $mensagens[] = "Rua é Invalido";
        }

        if(!($values->numero = htmlspecialchars(trim($numero)))){
            $mensagens[] = "Numero é Invalido";
        }

        $values->complemento = htmlspecialchars(trim($complemento));
       
        if(!$id_usuario && !$id_empresa){
            $mensagens[] = "Usuario ou Empresa precisa ser informado para cadastro";
        }

        if(($values->id_empresa = $id_empresa) && $valid_fk && !empresaModel::get($values->id_empresa)->id){
            $mensagens[] = "Empresa não existe";
        }

        if(($values->id_usuario = $id_usuario) && $valid_fk && !usuarioModel::get($values->id_usuario)->id){
            $mensagens[] = "Usuario não existe";
        }

        if(($values->id = $id) && !self::get($id)->id){
            $mensagens[] = "Endereço não existe";
        }

        if($mensagens){
            mensagem::setErro(...$mensagens);
            return false;
        }

        $retorno = $values->store();

        if ($retorno == true){
            mensagem::setSucesso("Endereço salva com sucesso");
            return $values->getLastID();
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
