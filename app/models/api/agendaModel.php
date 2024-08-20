<?php 
namespace app\models\api;
use app\db\tables\agenda;
use app\db\tables\funcionario;
use app\db\tables\agendaUsuario;
use app\db\tables\agendaFuncionario;
use app\models\main\empresaModel;
use app\helpers\mensagem;
use app\helpers\functions;
use app\models\abstract\model;

/**
 * Classe agendaModel
 * 
 * Esta classe fornece métodos para interagir com os dados da agenda e suas relações.
 * Ela utiliza as classes agenda, agendaUsuario e agendaFuncionario para realizar operações de consulta, inserção e exclusão no banco de dados.
 * 
 * @package app\models\main
 */
final class agendaModel extends model{

    /**
     * 
     * @param mixed $value valor usado na busca.
     * @param string $column coluna usado na busca.
     * @param string $limit limite usado na busca.
     * @return object Retorna o objeto do funcionário ou null se não encontrado.
    */
    public static function get(mixed $value = null,string $column = "id",?int $limit = 1):object
    {
        return (new agenda)->get($value,$column,$limit);
    }

    /**
     * Obtém varios ou um registro com base nos ids informados.
     * 
     * @param array $ids ids dos registros.
     * @return array Retorna os dados da agenda ou null se não encontrado.
    */
    public static function getbyIds(array $ids):array
    {
        $db = (new agenda); 
        
        foreach ($ids as $id){
            $db->addFilter("id","=",$id);
        }

        $db->asArray();

        return $db->selectAll();
    }

    /**
     * Obtém agendas por empresa.
     * 
     * @param int $id_empresa O ID da empresa para buscar agendas.
     * @param string $nome O Nome para buscar agendas.
     * @param string $codigo O Codigo para buscar agendas.
     * @param int $limit limit da query (opcional).
     * @param int $offset offset da query(opcional).
     * @return array|bool Retorna um array de agendas ou false se não encontrado.
    */
    public static function getByEmpresa(int $id_empresa,?string $nome = null,?string $codigo = null,?int $limit = null,?int $offset = null):array
    {
        $db = new agenda;
        
        $db->addFilter("agenda.id_empresa","=",$id_empresa);

        if($nome){
            $db->addFilter("nome","LIKE","%".$nome."%");
        }

        if($codigo){
            $db->addFilter("codigo","LIKE","%".$codigo."%");
        }

        if($limit && $offset){
            self::setLastCount($db);
            $db->addLimit($limit);
            $db->addOffset($offset);
        }
        elseif($limit){
            self::setLastCount($db);
            $db->addLimit($limit);
        }

        $db->asArray();

        return $db->selectColumns("id","agenda.nome","agenda.codigo");
    }

    /**
     * Obtém agendas por usuário.
     * 
     * @param int|null $id_usuario O ID do usuário para buscar agendas.
     * @return array Retorna um array de agendas ou false se não encontrado.
    */
    public static function getByUsuario(int $id_usuario):array
    {
        $db = new agendaUsuario;
        
        return $db->addJoin("agenda","agenda_usuario.id_agenda","agenda.id")
                    ->addJoin("empresa","agenda.id_empresa","empresa.id")
                    ->addJoin("agenda_funcionario","agenda_funcionario.id_agenda","agenda.id")
                    ->addFilter("agenda_usuario.id_usuario","=",$id_usuario)
                    ->asArray()
                    ->selectColumns("agenda.id","agenda.nome","empresa.nome as emp_nome");
    }

    /**
     * Busca todos os grupos vinculados a um funcionario
     * 
     * @param int $id_funcionario O ID do funcionário.
     * @return array Retorna array com os registros encontrados.
    */
    public static function getFuncionarioByAgenda(int $id_agenda):array
    {
        $db = new agendaFuncionario;

        $db->addJoin(funcionario::table,"id","id_funcionario")
           ->addFilter("id_agenda","=",$id_agenda);

        return $db->asArray()->selectColumns(funcionario::table.".id",funcionario::table.".nome");
    }

    /**
     * Obtém agendas por usuário e serviço.
     * 
     * @param int $id_servico O ID do serviço para buscar agendas.
     * @param int $id_usuario O ID do usuário para buscar agendas.
     * @return array|bool Retorna um array de agendas ou false se não encontrado.
    */
    public static function getByUsuarioServico(int $id_servico,int $id_usuario):array
    {
        $db = new agendaUsuario;
        
       return $db->addJoin("agenda","agenda_usuario.id_agenda","agenda.id")
                    ->addJoin("empresa","agenda.id_empresa","empresa.id")
                    ->addJoin("agenda_servico","agenda_servico.id_agenda","agenda.id")
                    ->addFilter("agenda_servico.id_servico","=",$id_servico)
                    ->addFilter("agenda_usuario.id_usuario","=",$id_usuario)
                    ->asArray()
                    ->selectColumns("id","agenda.nome","empresa.nome as emp_nome");
    }

    /**
     * Define a relação entre usuário e agenda.
     * 
     * @param int $id_usuario O ID do usuário.
     * @param int $id_agenda O ID da agenda.
     * @return bool Retorna true se a operação for bem-sucedida, caso contrário retorna false.
    */
    public static function setAgendaUsuario(int $id_usuario,int $id_agenda):bool
    {
        $db = new agendaUsuario;

        $result = $db->addFilter("agenda_usuario.id_usuario","=",$id_usuario)
                    ->addFilter("agenda_usuario.id_agenda","=",$id_agenda)
                    ->asArray()
                    ->selectAll();

        if (!$result){

            $db->id_usuario = $id_usuario;
            $db->id_agenda = $id_agenda;

            if($db->storeMutiPrimary()){
                mensagem::setSucesso("Agenda já vinculada ao Usuario");
                return true;
            }else{
                mensagem::setErro("Erro ao vincular agenda ao usuario");
                return false;
            }
        }

        mensagem::setSucesso("Agenda já vinculada ao Usuario");
        return true;
    }

    /**
     * Define a relação entre funcionário e agenda.
     * 
     * @param int $id_funcionario O ID do funcionário.
     * @param int $id_agenda O ID da agenda.
     * @return bool Retorna true se a operação for bem-sucedida, caso contrário retorna false.
    */
    public static function setAgendaFuncionario(int $id_funcionario,int $id_agenda):bool
    {
        $db = new agendaFuncionario;

        $result = $db->addFilter("agenda_funcionario.id_funcionario","=",$id_funcionario)
                ->addFilter("agenda_funcionario.id_agenda","=",$id_agenda)
                ->asArray()
                ->selectAll();

        if (!$result){
            $db->id_funcionario = $id_funcionario;
            $db->id_agenda = $id_agenda;

            if($db->storeMutiPrimary()){
                mensagem::setSucesso("Agenda já vinculada ao funcionario");
                return true;
            }else{
                mensagem::setErro("Erro ao vincular agenda ao funcionario");
                return false;
            }
        }

        mensagem::setSucesso("Agenda já vinculada ao funcionario");
        return true;
    }


    /**
     * Insere ou atualiza um registro na tabela de agenda.
     * 
     * @param string $nome O nome da agenda.
     * @param int $id_empresa O ID da empresa associada.
     * @param string $codigo O código da agenda (opcional).
     * @param string $id O ID da agenda (opcional).
     * @return int|bool Retorna o ID da agenda inserida ou atualizada se a operação for bem-sucedida, caso contrário retorna false.
    */
    public static function set(string $nome,int $id_empresa,string $codigo="",int|null $id = null):int|bool
    {
        $values = new agenda;

        $mensagens = [];

        if($id && !self::get($id)->id)
            $mensagens[] = "Agenda não encontrada";

        $values->id = $id;
        
        if(!($values->id_empresa = empresaModel::get($id_empresa)->id))
            $mensagens[] = "Empresa não encontrada"; 

        if(!($values->nome = htmlspecialchars(trim($nome))))
            $mensagens[] = "Nome é obrigatorio";

        if($mensagens){
            mensagem::setErro(...$mensagens);
            return false;
        }

        if ($codigo)
            $values->codigo = htmlspecialchars($codigo);
        else 
            $values->codigo = functions::genereteCode(7);

        if ($values->store()){
            mensagem::setSucesso("Agenda salvo com sucesso");
            return $values->id;
        }
        
        return False;
    }

    /**
     * Desvincula um funcionario de um grupo de funcionarios
     * 
     * @param int $id_agenda O ID da agenda.
     * @param int $id_funcionario O ID do funcionário.
     * @return bool Retorna true se a operação for bem-sucedida, caso contrário retorna false.
    */
    public static function detachAgendaUsuario(int $id_agenda,int $id_usuario):bool
    {
        $db = new agendaUsuario;

        if($db->addFilter("id_agenda","=",$id_agenda)->addFilter("id_usuario","=",$id_usuario)->deleteByFilter()){
            mensagem::setSucesso("Usuario Desvinculado Com Sucesso");
            return true;
        }

        mensagem::setErro("Erro ao Desvincular Usuario");
        return false;
    }

    /**
     * Desvincula um funcionario de um grupo de funcionarios
     * 
     * @param int $id_agenda O ID da agenda.
     * @param int $id_funcionario O ID do funcionário.
     * @return bool Retorna true se a operação for bem-sucedida, caso contrário retorna false.
    */
    public static function detachAgendaFuncionario(int $id_agenda,int $id_funcionario):bool
    {
        $db = new agendaFuncionario;

        if($db->addFilter("id_agenda","=",$id_agenda)->addFilter("id_funcionario","=",$id_funcionario)->deleteByFilter()){
            mensagem::setSucesso("Funcionario Desvinculado Com Sucesso");
            return true;
        }

        mensagem::setErro("Erro ao Desvincular Funcionario");
        return false;
    }

    /**
     * Exclui um registro da agenda.
     * 
     * @param int $id O ID da agenda a ser excluída.
     * @return bool Retorna true se a operação for bem-sucedida, caso contrário retorna false.
    */
    public static function delete(int $id):bool
    {
        try {
            transactionManeger::init();
            transactionManeger::beginTransaction();

            self::deleteAgendaUsuario($id);
            self::deleteAgendaFuncionario($id);
            
            if((new agenda)->delete($id)){
                mensagem::setSucesso("agenda deletada com sucesso");
                transactionManeger::commit();
                return true;
            }

            mensagem::setErro("Erro ao deletar agenda");
            transactionManeger::rollBack();
            return false;

        }catch(Exception $e){
            mensagem::setErro("Erro ao deletar agenda");
            transactionManeger::rollBack();
            return false;
        }
    }

    /**
     * Exclui as relações de um usuário com a agenda.
     * 
     * @param int $id_agenda O ID da agenda para excluir as relações.
     * @return bool Retorna true se a operação for bem-sucedida, caso contrário retorna false.
    */
    public static function deleteAgendaUsuario(int $id_agenda):bool
    {
        return (new agendaUsuario)->addFilter("agenda_usuario.id_agenda","=",$id_agenda)->deleteByFilter();  
    }

    /**
     * Exclui as relações de um funcionário com a agenda.
     * 
     * @param int $id_agenda O ID da agenda para excluir as relações.
     * @return bool Retorna true se a operação for bem-sucedida, caso contrário retorna false.
    */
    public static function deleteAgendaFuncionario(int $id_agenda):bool
    {
        return (new agendaFuncionario)->addFilter("agenda_funcionario.id_agenda","=",$id_agenda)->deleteByFilter();
    }

}