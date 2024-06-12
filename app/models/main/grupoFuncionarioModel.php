<?php
namespace app\models\main;

use app\db\grupoFuncionario;
use app\classes\mensagem;

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
     * @param int $id O ID do grupo de funcionário a ser buscado.
     * @return object Retorna os dados do grupo de funcionário ou null se não encontrado.
     */
    public static function get(int $id = null):object{
        return (new grupoFuncionario)->get($id);
    }

    /**
     * Obtém grupos de funcionários por ID da empresa.
     * 
     * @param int $id_empresa O ID da empresa dos grupos de funcionários a serem buscados.
     * @param string $nome para filtrar por nome.
     * @return array Retorna um array com os grupos de funcionários da empresa especificada.
     */
    public static function getByEmpresa(int $id_empresa,string $nome = null):array{
        $db = new grupoFuncionario;

        $db->addFilter("id_empresa", "=", $id_empresa);

        if($nome){
            $db->addFilter("nome", "like", "%".$nome."%");
        }

        $values = $db->selectColumns("id","nome");

        return $values;
    }

    /**
     * Insere ou atualiza um grupo de funcionário.
     * 
     * @param string $nome O nome do grupo de funcionário.
     * @param int $id O ID do grupo de funcionário (opcional).
     * @return bool Retorna true se a operação for bem-sucedida, caso contrário retorna false.
     */
    public static function set(string $nome,int $id_empresa,int $id = null):bool{
        $values = new grupoFuncionario;
        
        

        $mensagens = [];

        if($values->id = $id && !self::get($values->id)->id){
            $mensagens[] = "Grupo de Funcionarios não encontrada";
        }
        
        if(!empresaModel::get($values->id_empresa = $id_empresa)->id){
            $mensagens[] = "Empresa não encontrada";
        }

        if(!$values->nome = filter_var(trim($nome))){
            $mensagens[] = "Nome invalido";
        }

        $retorno = $values->store();

        if ($retorno == true){
            mensagem::setSucesso("Grupo de funcionarios salvo com sucesso");
            return true;
        }
        
        mensagem::setErro("Erro ao salvar grupo de funcionarios");
        return false;
    }

    /**
     * Exclui um grupo de funcionário pelo ID.
     * 
     * @param string $id O ID do grupo de funcionário a ser excluído.
     * @return bool Retorna true se a operação for bem-sucedida, caso contrário retorna false.
     */
    public static function delete(int $id):bool{
        return (new grupoFuncionario)->delete($id);
    }

}
