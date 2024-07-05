<?php
namespace app\models\main;

use app\db\config;
use app\classes\mensagem;

/**
 * Classe clienteModel
 * 
 * Esta classe fornece métodos para interagir com os dados de clientes.
 * Ela utiliza a classe cliente para realizar operações de consulta, inserção e exclusão no banco de dados.
 * 
 * @package app\models\main
 */
class configuracoesModel{

    /**
     * Obtém um cliente pelo ID.
     * 
     * @param string $id O ID do cliente a ser buscado.
     * @return object Retorna os dados do cliente ou objeto se não encontrado.
     */
    public static function get(int $id):object
    {
        return (new config)->get($id);
    }

    /**
     * Obtém clientes pelo ID do funcionário associado.
     * 
     * @param int $id_empresa O ID do funcionário associado aos clientes.
     * @return array Retorna um array de clientes ou um array vazio se não encontrado.
     */
    public static function getByEmpresa(int $id_empresa):array
    {
        $db = new config;
        $config = $db->addFilter(config::table.".id_empresa", "=", $id_empresa)->selectAll();

        return $config;
    }

    /**
     * retorna o valor de uma config.
     * 
     * @param int $identificador O $identificador associado aos clientes.
     * @return array Retorna um array de clientes ou um array vazio se não encontrado.
     */
    public static function getConfig(string $identificador, int $id_empresa):bool|string|int|float
    {
        $db = new config;
        $config = $db->addFilter(config::table.".identificador", "=", $identificador)
                      ->addFilter(config::table.".id_empresa", "=", $id_empresa)
                      ->addLimit(1)->selectColumns("identificador");

        if($config)
            return $config[0];
        else 
            return false;
    }

    /**
     * Insere ou atualiza uma configuração.
     * 
     * @param string $identificador O nome da configuração.
     * @param string|int|float $configuracao valor da configuração.
     * @param int $id_empresa O ID da empresa associada.
     * @param int $id O ID da configuração (opcional).
     * @return int|bool Retorna o ID do configuração inserido ou atualizado se a operação for bem-sucedida, caso contrário retorna false.
     */
    public static function set(string $identificador,string|int|float $configuracao,int $id_empresa,int $id = null):int|bool
    {
        $values = new config;
    
        if ($values){
            $values->id = intval($id);
            $values->id_empresa = intval($id_empresa);
            $values->identificador = trim(strtolower($identificador));
            $values->configuracao = $configuracao;
            $retorno = $values->store();
        }

        if ($retorno == true){
            mensagem::setSucesso("Cliente salvo com sucesso");
            return $values->getLastID();
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
        return (new config)->delete($id);
    }

}
