<?php
namespace app\models\main;

use app\db\grupoServico;
use app\classes\mensagem;

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
     * @return object Retorna os dados do grupo de serviço ou object se não encontrado.
     */
    public static function get(int $id = null):object
    {
        return (new grupoServico)->get($id);
    }

    /**
     * Insere ou atualiza um grupo de serviço.
     * 
     * @param string $nome O nome do grupo de serviço.
     * @param int $id O ID do grupo de serviço (opcional).
     * @return bool Retorna true se a operação for bem-sucedida, caso contrário retorna false.
     */
    public static function set(string $nome,int $id_empresa,int $id = null):bool
    {
        $values = new grupoServico;

        if($values->id = $id && !self::get($values->id)->id){
            $mensagens[] = "Grupo de Serviço não encontrada";
        }
        
        if(!empresaModel::get($values->id_empresa = $id_empresa)->id){
            $mensagens[] = "Empresa não encontrada";
        }

        if(!$values->nome = filter_var(trim($nome))){
            $mensagens[] = "Nome invalido";
        }

        $retorno = $values->store();

        if ($retorno == true){
            mensagem::setSucesso("Grupo de serviços salvo com sucesso");
            return true;
        }
        
        mensagem::setErro("Erro ao salvar grupo de serviços");
        return false;
        
    }

    /**
     * Obtém grupos de serviço por ID da empresa.
     * 
     * @param int $id_empresa O ID da empresa dos grupos de serviço a serem buscados.
     * @param string $nome para filtrar por nome.
     * @return array Retorna um array com os grupos de serviço da empresa especificada.
     */
    public static function getByEmpresa(int $id_empresa,string $nome = null):array
    {
        $db = new grupoServico;

        $db->addFilter("id_empresa", "=", $id_empresa);

        if($nome){
            $db->addFilter("nome", "like", "%".$nome."%");
        }

        $values = $db->selectColumns("id","nome");

        return $values;
    }

    /**
     * Exclui um grupo de serviço pelo ID.
     * 
     * @param string $id O ID do grupo de serviço a ser excluído.
     * @return bool Retorna true se a operação for bem-sucedida, caso contrário retorna false.
     */
    public static function delete():bool
    {
        return (new grupoServico)->delete();
    }

}
