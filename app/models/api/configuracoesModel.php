<?php
namespace app\models\api;

use app\db\tables\config;
use app\helpers\mensagem;

/**
 * Classe clienteModel
 * 
 * Esta classe fornece métodos para interagir com os dados de clientes.
 * Ela utiliza a classe cliente para realizar operações de consulta, inserção e exclusão no banco de dados.
 * 
 * @package app\models\main
 */
final class configuracoesModel{

   /**
     * Obtém um registro da agenda com base em um valor e coluna especificados.
     * 
     * @param string $value O valor para buscar.
     * @param string $column A coluna onde buscar o valor.
     * @param int $limit O número máximo de registros a serem retornados.
     * @return object|array Retorna os dados da agenda caso limit seja 1 ira retornar um objeto caso não um array.
    */
    public static function get(mixed $value = "",string $column = "id",int $limit = 1):array|object
    {
        return (new config)->get($value,$column,$limit);
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
                      ->addLimit(1)->selectColumns("configuracao");

        if($config)
            return $config[0]->configuracao;
         
        return false;
    }

    /**
     * retorna o valor de uma config.
     * 
     * @param int $identificador O $identificador associado aos clientes.
     * @return array Retorna um array de clientes ou um array vazio se não encontrado.
     */
    public static function getConfigStore(string $identificador, int $id_empresa):config|bool
    {
        $db = new config;
        $config = $db->addFilter(config::table.".identificador", "=", $identificador)
                      ->addFilter(config::table.".id_empresa", "=", $id_empresa)
                      ->addLimit(1)->selectAll();

        if($config)
            return $config[0];
         
        return false;
    }

    /**
     * salva uma config.
     * 
     * @param string $identificador da config.
     * @param int $id_empresa associado aos clientes.
     * @param string|int|float $configuracao valor da config.
     * @return bool|int false se não salvo id se for salvo.
     */
    public static function set(string $identificador, int $id_empresa,string|int|float $configuracao):bool|int
    {
        $mensagens = [];

        if(!($db = self::getConfigStore($identificador,$id_empresa))){
            $db = new config();

            if(!empresaModel::get($db->id_empresa = $id_empresa)->id)
                $mensagens[] = "Empresa não existe";

            if(!($db->identificador = htmlspecialchars($identificador)))
                $mensagens[] = "Identificador é obrigatorio";
        }

        if(!($db->configuracao = htmlspecialchars($configuracao)))
            $mensagens[] = "Valor é obrigatorio";
        
        if($mensagens){
            mensagem::setErro(...$mensagens);
            return false;
        }

        if ($db->store()){
            mensagem::setSucesso("Configuração salvo com sucesso");
            return $db->id;
        }
        
        return False;
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
