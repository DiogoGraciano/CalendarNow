<?php
namespace app\models\main;

use app\db\grupoServico;
use app\classes\mensagem;
use app\classes\modelAbstract;

/**
 * Classe grupoServicoModel
 * 
 * Esta classe fornece métodos para interagir com os dados de grupos de serviço.
 * Ela utiliza a classe grupoServico para realizar operações de consulta, inserção e exclusão no banco de dados.
 * 
 * @package app\models\main
 */
class grupoServicoModel{

    /**
     * Obtém um grupo de serviço pelo ID.
     * 
     * @param int $id O ID do grupo de serviço a ser buscado.
     * @return array|object Retorna os dados do grupo de serviço ou object se não encontrado.
     */
    public static function get(int $id = null){
        return (new grupoServico)->get($id);
    }

    /**
     * Obtém todos os grupos de serviço.
     * 
     * @return array Retorna um array com todos os grupos de serviço.
     */
    public static function getAll(){
        return (new grupoServico)->selectAll();
    }

    /**
     * Insere ou atualiza um grupo de serviço.
     * 
     * @param string $nome O nome do grupo de serviço.
     * @param int $id O ID do grupo de serviço (opcional).
     * @return bool Retorna true se a operação for bem-sucedida, caso contrário retorna false.
     */
    public static function set(string $nome,int $id){
        $db = new grupoServico;
        
        $values = $db->getObject();

        $values->id = $id;
        $values->nome = $nome;

        if ($values)
            $retorno = $db->store($values);

        if ($retorno == true){
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * Obtém grupos de serviço por ID da empresa.
     * 
     * @param int $id_empresa O ID da empresa dos grupos de serviço a serem buscados.
     * @param string $nome para filtrar por nome.
     * @return array Retorna um array com os grupos de serviço da empresa especificada.
     */
    public static function getByEmpresa(int $id_empresa,string $nome = null){
        $db = new grupoServico;

        $db->addFilter("id_empresa", "=", $id_empresa);

        if($nome){
            $db->addFilter("nome", "=", $nome);
        }

        $values = $db->selectAll();

        if ($Mensagems = ($db->getError())){
            return [];
        }

        return $values;
    }

    /**
     * Exclui um grupo de serviço pelo ID.
     * 
     * @param string $id O ID do grupo de serviço a ser excluído.
     * @return bool Retorna true se a operação for bem-sucedida, caso contrário retorna false.
     */
    public static function delete(int $id){
        return (new grupoServico)->delete($id);
    }

}
