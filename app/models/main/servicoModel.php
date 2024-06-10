<?php 
namespace app\models\main;

use app\classes\functions;
use app\db\servico;
use app\db\servicoFuncionario;
use app\db\servicoGrupoServico;
use app\db\agendaServico;
use app\classes\mensagem;

/**
 * Classe servicoModel
 * 
 * Esta classe fornece métodos para interagir com os serviços.
 * Ela utiliza a classe servico para realizar operações de consulta, inserção, atualização e exclusão no banco de dados.
 * 
 * @package app\models\main
*/
class servicoModel{

    /**
     * Obtém um serviço pelo ID.
     * 
     * @param int $id O ID do serviço.
     * @return object Retorna o objeto do serviço ou null se não encontrado.
    */
    public static function get(null|int|string $value = null,string $column = "id"):object
    {
        return (new servico)->get($value,$column);
    }

    /**
     * Obtém uma lista de serviços por empresa, podendo filtrar por nome, funcionário ou grupo de serviço.
     * 
     * @param int $id_empresa O ID da empresa.
     * @param string|null $nome O nome do serviço (opcional).
     * @param int|null $id_funcionario O ID do funcionário (opcional).
     * @param int|null $id_grupo_servico O ID do grupo de serviço (opcional).
     * @return array Retorna um array com os serviços filtrados.
    */
    public static function getListByEmpresa(int $id_empresa,string $nome = null,int $id_funcionario = null,int $id_grupo_servico = null):array
    {
        $db = new servico;

        $db->addFilter("servico.id_empresa","=",$id_empresa);

        if($nome){
            $db->addFilter("servico.nome","like","%".$nome."%");
        }

        if($id_funcionario){
            $db->addJoin("INNER","servico_funcionario","servico_funcionario.id_servico","servico.id");
            $db->addFilter("servico_funcionario.id_funcionario","=",$id_funcionario);
        }

        if($id_grupo_servico){
            $db->addJoin("INNER","servico_grupo_servico","servico_grupo_servico.id_servico","servico.id");
            $db->addFilter("servico_grupo_servico.id_grupo_servico","=",$id_grupo_servico);
        }

        $db->addGroup("servico.id");
        
        $values = $db->selectColumns("servico.id","servico.nome","servico.tempo","servico.valor");

        $valuesFinal = [];

        if ($db->getError()){
            return [];
        }

        if ($values){
            foreach ($values as $value){
                if ($value->valor){
                    $value->valor = functions::formatCurrency($value->valor);
                }
                $valuesFinal[] = $value;
            }

            return $values;
        }

        return [];
    }

    /**
     * Obtém os serviços associados a um funcionário.
     * 
     * @param int $id_funcionario O ID do funcionário.
     * @return array Retorna um array com os serviços associados ao funcionário.
    */
    public static function getByFuncionario(int $id_funcionario):array
    {
        $db = new servico;

        $db->addJoin("INNER","servico_funcionario","servico_funcionario.id_servico","servico.id");
        $db->addFilter("servico_funcionario.id_funcionario","=",$id_funcionario);
        
        $db->addGroup("servico.id");
        
        $values = $db->selectColumns("servico.id","servico.nome","servico.tempo","servico.valor");

        if ($db->getError()){
            return [];
        }

        if ($values){
            return $values;
        }

        return [];
    }

    /**
     * Associa um serviço a um grupo de serviço.
     * 
     * @param int $id_servico O ID do serviço.
     * @param int $id_grupo_servico O ID do grupo de serviço.
     * @return bool Retorna o ID da associação se a operação for bem-sucedida, caso contrário retorna false.
    */
    public static function setServicoGrupoServico(int $id_servico,int $id_grupo_servico):bool
    {
        $values = new servicoGrupoServico;

        if(!grupoServicoModel::get($values->id_grupo_servico = $id_grupo_servico)->id){
            mensagem::setErro("Grupo de serviço não existe");
            return false;
        }
        if(self::get($values->id_servico = $id_servico)->id){
            mensagem::setErro("Serviço não existe");
            return false;
        }

        $result = $values->addFilter("id_grupo_servico","=",$id_grupo_servico)
                        ->addFilter("id_servico","=",$id_servico)
                        ->selectAll();

        if (!$result){
           
            $retorno = $values->storeMutiPrimary();

            mensagem::setSucesso("Serviço Adicionado com Sucesso");

            return $retorno;
        }

        mensagem::setSucesso("Serviço já Adicionado");

        return True;
    }

    /**
     * Associa um serviço a um funcionário.
     * 
     * @param int $id_servico O ID do serviço.
     * @param int $id_funcionario O ID do funcionário.
     * @return bool Retorna o ID da associação se a operação for bem-sucedida, caso contrário retorna false.
    */
    public static function setServicoFuncionario(int $id_servico,int $id_funcionario):bool
    {
        $values = new servicoFuncionario;

        if(!funcionarioModel::get($values->id_funcionario = $id_funcionario)){
            mensagem::setErro("Funcionario não existe");
            return false;
        }
        if(self::get($values->id_servico = $id_servico)){
            mensagem::setErro("Serviço não existe");
            return false;
        }

        $result = $values->addFilter("id_funcionario","=",$id_funcionario)
                    ->addFilter("id_servico","=",$id_servico)
                    ->selectAll();

        if (!$result){

            $retorno = $values->storeMutiPrimary($values);

            mensagem::setSucesso("Serviço Adicionado com Sucesso");

            return $retorno;
        }

        mensagem::setSucesso("Serviço já Adicionado");

        return True;
    }

    /**
     * Insere ou atualiza um serviço.
     * 
     * @param string $nome O nome do serviço.
     * @param float $valor O valor do serviço.
     * @param string $tempo O tempo estimado do serviço.
     * @param int|string $id_empresa O ID da empresa.
     * @param string $id O ID do serviço (opcional).
     * @return int|bool Retorna o ID do serviço se a operação for bem-sucedida, caso contrário retorna false.
    */
    public static function set(string $nome,float $valor,string $tempo,int|null $id_empresa = null,int|null $id = null):int|bool
    {

        $values = new servico;
        
        $mensagens = [];

        if(!$values->nome = filter_var(trim($nome))){
            $mensagens[] = "Nome é invalido";
        }

        if(($values->valor = $valor) <= 0){
            $mensagens[] = "Valor do serviço invalido";
        }

        if(!functions::validaHorario($values->tempo = functions::formatTime($tempo))){
            $mensagens[] = "Tempo do serviço invalido";
        }

        if(($values->id_empresa = $id_empresa) && !empresaModel::get($values->id_empresa)){
            $mensagens[] = "Empresa não existe";
        }

        if($values->id = $id && !self::get($values->id)){
            $mensagens[] = "Serviço não existe";
        }

        if($mensagens){
            mensagem::setErro(...$mensagens);
            return false;
        }

        if ($values)
            $retorno = $values->store();

        if ($retorno == true){
            mensagem::setSucesso("Serviço salvo com sucesso");
            return $values->getLastID();
        }

        return False;
    }

    /**
     * Exclui um serviço pelo ID.
     * 
     * @param int $id O ID do serviço a ser excluído.
     * @return bool Retorna true se a operação for bem-sucedida, caso contrário retorna false.
    */
    public static function delete(int $id):bool{
        return (new servico)->delete($id);
    }

    /**
     * Exclui a associação de um serviço com uma agenda.
     * 
     * @param int $id_servico O ID do serviço.
     * @param int $id_agenda O ID da agenda.
     * @return bool Retorna true se a operação for bem-sucedida, caso contrário retorna false.
    */
    public static function deleteAgendaServico(int $id_servico,int $id_agenda):bool
    {
        $db = new agendaServico;

        return $db->addFilter("agenda_servico.id_servico","=",$id_servico)->addFilter("agenda_servico.id_agenda","=",$id_agenda)->deleteByFilter();
    }

}