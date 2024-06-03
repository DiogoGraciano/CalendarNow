<?php
namespace app\models\main;

use app\db\cliente;
use app\classes\modelAbstract;

/**
 * Classe clienteModel
 * 
 * Esta classe fornece métodos para interagir com os dados de clientes.
 * Ela utiliza a classe cliente para realizar operações de consulta, inserção e exclusão no banco de dados.
 * 
 * @package app\models\main
 */
class clienteModel{

    /**
     * Obtém um cliente pelo ID.
     * 
     * @param string $id O ID do cliente a ser buscado.
     * @return object Retorna os dados do cliente ou objeto se não encontrado.
     */
    public static function get(int $id):object
    {
        return (new cliente)->get($id);
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
        $cliente = $db->addFilter("cliente.id_funcionario", "=", $id_funcionario)->selectAll();

        if ($db->getError()){
            return [];
        }

        return $cliente;
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
        $db = new cliente;
    
        $values = $db->getObject();

        if ($values){
            $values->id = intval($id);
            $values->id_funcionario = intval($id_funcionario);
            $values->nome = trim($nome);
            $retorno = $db->store($values);
        }

        if ($retorno == true){
            mensagem::setSucesso("Cliente salvo com sucesso");
            return $db->getLastID();
        } else {
            return false;
        }
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
