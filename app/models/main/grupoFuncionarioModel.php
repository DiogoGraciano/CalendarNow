<?php
namespace app\models\main;

use app\db\funcionario;
use app\db\grupoFuncionario;
use app\classes\mensagem;
use app\classes\modelAbstract;

/**
 * Classe grupoFuncionarioModel
 * 
 * Esta classe fornece métodos para interagir com os dados de grupos de funcionários.
 * Ela utiliza a classe grupoFuncionario para realizar operações de consulta, inserção e exclusão no banco de dados.
 * 
 * @package app\models\main
 */
class grupoFuncionarioModel{

    /**
     * Obtém um grupo de funcionário pelo ID.
     * 
     * @param string $id O ID do grupo de funcionário a ser buscado.
     * @return array|null Retorna os dados do grupo de funcionário ou null se não encontrado.
     */
    public static function get($id = ""){
        return (new grupoFuncionario)->get($id);
    }

    /**
     * Obtém todos os grupos de funcionários.
     * 
     * @return array Retorna um array com todos os grupos de funcionários.
     */
    public static function getAll(){
        return (new grupoFuncionario)->selectAll();
    }

    /**
     * Obtém grupos de funcionários por ID da empresa.
     * 
     * @param string $id_empresa O ID da empresa dos grupos de funcionários a serem buscados.
     * @return array Retorna um array com os grupos de funcionários da empresa especificada.
     */
    public static function getByEmpresa($id_empresa){
        $db = new grupoFuncionario;

        $values = $db->addFilter("id_empresa", "=", $id_empresa)->selectAll();

        if ($Mensagems = ($db->getError())){
            return [];
        }

        return $values;
    }

    /**
     * Insere ou atualiza um grupo de funcionário.
     * 
     * @param string $nome O nome do grupo de funcionário.
     * @param string $id O ID do grupo de funcionário (opcional).
     * @return bool Retorna true se a operação for bem-sucedida, caso contrário retorna false.
     */
    public static function set($nome, $id){
        $db = new grupoFuncionario;
        
        $values = $db->getObject();

        $values->id = $id;
        $values->nome = $nome;

        if ($values)
            $retorno = $db->store($values);

        if ($retorno == true){
            return true;
        }
        
        return false;
    }

    /**
     * Exclui um grupo de funcionário pelo ID.
     * 
     * @param string $id O ID do grupo de funcionário a ser excluído.
     * @return bool Retorna true se a operação for bem-sucedida, caso contrário retorna false.
     */
    public static function delete($id){
        return (new grupoFuncionario)->delete($id);
    }

}
