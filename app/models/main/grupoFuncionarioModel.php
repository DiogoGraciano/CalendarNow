<?php
namespace app\models\main;

use app\db\grupoFuncionario;
use app\db\funcionario;
use app\classes\mensagem;
use app\db\funcionarioGrupoFuncionario;

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
     * Busca todos os grupos vinculados a um funcionario
     * 
     * @param int $id_funcionario O ID do funcionário.
     * @return array Retorna array com os registros encontrados.
    */
    public static function getByFuncionario(int $id_funcionario):array
    {

        $db = new funcionarioGrupoFuncionario;

        $db->addJoin(grupoFuncionario::table,"id","id_grupo_funcionario")
        ->addFilter("id_funcionario","=",$id_funcionario);

        return $db->selectAll();
    }

    /**
     * Busca todos os funcionarios vinculados a um grupo
     * 
     * @param int $id_grupo_funcionario O ID do grupo de funcionario.
     * @return array Retorna array com os registros encontrados.
    */
    public static function getVinculados(int $id_grupo_funcionario):array
    {
        $db = new funcionarioGrupoFuncionario;

        $db->addJoin(funcionario::table,"id","id_funcionario")
        ->addFilter("id_grupo_funcionario","=",$id_grupo_funcionario);

        return $db->selectColumns("funcionario.id","funcionario.nome");
    }

    /**
     * Desvincula um funcionario de um grupo de funcionarios
     * 
     * @param int $id_grupo O ID do grupo de funcionário.
     * @param int $id_funcionario O ID do funcionário.
     * @return bool Retorna true se a operação for bem-sucedida, caso contrário retorna false.
    */
    public static function detachFuncionario(int $id_grupo,int $id_funcionario):bool
    {
        $db = new funcionarioGrupoFuncionario;

        if($db->addFilter("id_grupo_funcionario","=",$id_grupo)->addFilter("id_funcionario","=",$id_funcionario)->deleteByFilter()){
            mensagem::setSucesso("Funcionario Desvinculado Com Sucesso");
            return true;
        }

        mensagem::setErro("Erro ao Desvincular Funcionario");
        return false;
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

        if(!$values->nome = htmlspecialchars((trim($nome)))){
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
