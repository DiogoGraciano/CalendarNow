<?php
namespace app\models\main;

use app\db\tables\cliente;
use app\helpers\mensagem;
use app\models\abstract\model;

/**
 * Classe clienteModel
 * 
 * Esta classe fornece métodos para interagir com os dados de clientes.
 * Ela utiliza a classe cliente para realizar operações de consulta, inserção e exclusão no banco de dados.
 * 
 * @package app\models\main
 */
final class clienteModel extends model{

     /**
     * Obtém um registro da cliente com base em um valor e coluna especificados.
     * 
     * @param string $value O valor para buscar.
     * @param string $column A coluna onde buscar o valor.
     * @param int $limit O número máximo de registros a serem retornados.
     * @return object|array Retorna os dados da cliente.
    */
    public static function get(mixed $value = "",string $column = "id",int $limit = 1):array|object
    {
        return (new cliente)->get($value,$column,$limit);
    }

    /**
     * Obtém clientes pelo ID do funcionário associado.
     * 
     * @param int $id_funcionario O ID do funcionário associado aos clientes.
     * @return array Retorna um array de clientes ou um array vazio se não encontrado.
     */
    public static function getByFuncionario(int $id_funcionario):array
    {
        $db = new cliente;
        return $db->addFilter("cliente.id_funcionario", "=", $id_funcionario)->selectAll();
    }

    /**
     * Obtém clientes pelo ID do funcionário associado.
     * 
     * @param int $id_usuario O ID do usuario associado aos clientes.
     * @param int $limit limit da query (opcional).
     * @param int $offset offset da query(opcional).
     * @return array Retorna um array de clientes ou um array vazio se não encontrado.
     */
    public static function getByUsuario(int $id_usuario,?int $limit = null,?int $offset = null):array
    {
        $db = new cliente;
        $db->addJoin("funcionario","funcionario.id","cliente.id_funcionario")->addFilter("funcionario.id_usuario", "=", $id_usuario);

        if($limit && $offset){
            self::setLastCount($db);
            $db->addLimit($limit);
            $db->addOffset($offset);
        }
        elseif($limit){
            self::setLastCount($db);
            $db->addLimit($limit);
        }

        return $db->selectColumns("cliente.id","cliente.nome","cliente.id_funcionario");
    }

    /**
     * Obtém clientes pelo ID do funcionário associado.
     * 
     * @param int $id_empresa O ID da empresa associada aos clientes.
     * @param int $limit limit da query (opcional).
     * @param int $offset offset da query(opcional).
     * @return array Retorna um array de clientes ou um array vazio se não encontrado.
     */
    public static function getByEmpresa(int $id_empresa,?int $limit = null,?int $offset = null):array
    {
        $db = new cliente;
        $db->addJoin("funcionario","funcionario.id","cliente.id_funcionario")->addJoin("usuario","usuario.id","funcionario.id_usuario")->addFilter("usuario.id_empresa", "=", $id_empresa);
        
        if($limit && $offset){
            self::setLastCount($db);
            $db->addLimit($limit);
            $db->addOffset($offset);
        }
        elseif($limit){
            self::setLastCount($db);
            $db->addLimit($limit);
        }

        return $db->selectColumns("cliente.id","cliente.nome","cliente.id_funcionario");
    }


    /**
     * Insere ou atualiza um cliente.
     * 
     * @param string $nome O nome do cliente.
     * @param int $id_empresa O ID da empresa associada.
     * @param int $id_funcionario O ID do funcionário associado.
     * @param int $id O ID do cliente (opcional).
     * @return int|bool Retorna o ID do cliente inserido ou atualizado se a operação for bem-sucedida, caso contrário retorna false.
     */
    public static function set(string $nome,int $id_funcionario,int $id = null):int|bool
    {
        $values = new cliente;
    
        $mensagens = [];

        if($id && !($values->id = self::get($id)->id))
            $mensagens[] = "Cliente não encontrado";

        if(!($values->id_funcionario = funcionarioModel::get($id_funcionario)->id))
            $mensagens[] = "Funcionario informado não encontrado";

        if(!($values->nome = htmlspecialchars(trim($nome))))
            $mensagens[] = "Nome é obrigatorio";

        if($mensagens){
            mensagem::setErro(...$mensagens);
            return false;
        }

        if ($values->store()){
            mensagem::setSucesso("Cliente salvo com sucesso");
            return $values->id;
        } 

        return false;
    }

    /**
     * Exclui um registro de cliente.
     * 
     * @param int $id O ID do cliente a ser excluído.
     * @return bool Retorna true se a operação for bem-sucedida, caso contrário retorna false.
     */
    public static function delete(int $id):bool
    {
        return (new cliente)->delete($id);
    }

}
