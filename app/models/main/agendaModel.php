<?php 
namespace app\models\main;
use app\db\agenda;
use app\db\agendaUsuario;
use app\db\agendaFuncionario;
use app\classes\mensagem;
use app\classes\modelAbstract;
use app\classes\functions;

/**
 * Classe agendaModel
 * 
 * Esta classe fornece métodos para interagir com os dados da agenda e suas relações.
 * Ela utiliza as classes agenda, agendaUsuario e agendaFuncionario para realizar operações de consulta, inserção e exclusão no banco de dados.
 * 
 * @package app\models\main
 */
class agendaModel{

    /**
     * Obtém um registro da agenda com base em um valor e coluna especificados.
     * 
     * @param string $value O valor para buscar.
     * @param string $column A coluna onde buscar o valor.
     * @param int $limit O número máximo de registros a serem retornados.
     * @return object|array Retorna os dados da agenda ou null se não encontrado.
    */
    public static function get($value = "",string $column = "id",int $limit = 1):array|object
    {
        return (new agenda)->get($value,$column,$limit);
    }

    /**
     * Obtém agendas por empresa.
     * 
     * @param int $id_empresa O ID da empresa para buscar agendas.
     * @param string $nome O Nome para buscar agendas.
     * @param string $codigo O Codigo para buscar agendas.
     * @return array|bool Retorna um array de agendas ou false se não encontrado.
    */
    public static function getByEmpresa(int $id_empresa,string $nome = null,string $codigo = null):array|bool
    {
        $db = new agenda;
        
        $db->addFilter("agenda.id_empresa","=",$id_empresa);

        if($nome){
            $db->addFilter("nome","LIKE","%".$nome."%");
        }

        if($codigo){
            $db->addFilter("codigo","LIKE","%".$codigo."%");
        }

        $values = $db->selectColumns("id","agenda.nome","agenda.codigo");

        if($values)
            return $values;
        
        return false;
    }

    /**
     * Obtém agendas por código.
     * 
     * @param string $codigo O código da agenda para buscar.
     * @return array|bool Retorna um array de agendas ou false se não encontrado.
    */
    public static function getByCodigo(string $codigo = ""):array|bool
    {
        $db = new agenda;
        
        $values = $db->addFilter("agenda.codigo","=",$codigo)
                     ->selectColumns("id","agenda.nome","agenda.codigo");
        
        if($values)
            return $values;
        
        return false;
    }

    /**
     * Obtém agendas por usuário.
     * 
     * @param int|null $id_usuario O ID do usuário para buscar agendas.
     * @return array|bool Retorna um array de agendas ou false se não encontrado.
    */
    public static function getByUser(int $id_usuario):array|bool 
    {
        $db = new agendaUsuario;
        
        $db->addJoin("INNER","agenda","agenda_usuario.id_agenda","agenda.id")
        ->addJoin("INNER","empresa","agenda.id_empresa","empresa.id")
        ->addJoin("INNER","agenda_funcionario","agenda_funcionario.id_agenda","agenda.id")
        ->addFilter("agenda_usuario.id_usuario","=",$id_usuario);

        if($values = $db->selectColumns("agenda.id","agenda.nome","empresa.nome as emp_nome"))
            return $values;
        
        return false;
    }

    /**
     * Obtém agendas por usuário e serviço.
     * 
     * @param int $id_servico O ID do serviço para buscar agendas.
     * @param int $id_usuario O ID do usuário para buscar agendas.
     * @return array|bool Retorna um array de agendas ou false se não encontrado.
    */
    public static function getByUserServico(int $id_servico,int $id_usuario):array|bool 
    {
        $db = new agendaUsuario;
        
        $values = $db->addJoin("INNER","agenda","agenda_usuario.id_agenda","agenda.id")
                     ->addJoin("INNER","empresa","agenda.id_empresa","empresa.id")
                     ->addJoin("INNER","agenda_servico","agenda_servico.id_agenda","agenda.id")
                     ->addJoin("INNER","agenda_usuario","agenda_usuario.id_agenda","agenda_servico.id_agenda")
                     ->addFilter("agenda_servico.id_servico","=",$id_servico)
                     ->addFilter("agenda_usuario.id_usuario","=",$id_usuario)
                     ->selectColumns("id","agenda.nome","empresa.nome as emp_nome");

        if($values)
            return $values;
        
        return false;
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

        $retorno = $db->addFilter("agenda_usuario.id_usuario","=",$id_usuario)
                ->addFilter("agenda_usuario.id_agenda","=",$id_agenda)
                ->selectAll();

        if (!$retorno){
            $values = $db->getObject();

            $values->id_usuario = $id_usuario;
            $values->id_agenda = $id_agenda;

            $retorno = $db->storeMutiPrimary($values);

            return $retorno;
        }

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

        $retorno = $db->addFilter("agenda_funcionario.id_funcionario","=",$id_funcionario)
                ->addFilter("agenda_funcionario.id_agenda","=",$id_agenda)
                ->selectAll();

        if (!$retorno){
            $values = $db->getObject();

            $values->id_funcionario = $id_funcionario;
            $values->id_agenda = $id_agenda;

            if ($values)
                $retorno = $db->storeMutiPrimary($values);

            return $retorno;
        }
  
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
    public static function set(string $nome,int $id_empresa,string $codigo="",string $id=""):int|bool
    {

        $db = new agenda;
        
        $values = $db->getObject();

        $values->id = $id;
        $values->id_empresa = $id_empresa;
        $values->nome = trim($nome);

        if ($codigo)
            $values->codigo = $codigo;
        else 
            $values->codigo = functions::genereteCode(7);

        if ($values){
            $retorno = $db->store($values);
        }

        if ($retorno == true){
            mensagem::setSucesso("Agenda salvo com sucesso");
            return $db->getLastID();
        }
        else {
            return False;
        }
    }

    /**
     * Exclui um registro da agenda.
     * 
     * @param int $id O ID da agenda a ser excluída.
     * @return bool Retorna true se a operação for bem-sucedida, caso contrário retorna false.
    */
    public static function delete(int $id):bool
    {
        return (new agenda)->delete($id);
    }

    /**
     * Exclui as relações de um usuário com a agenda.
     * 
     * @param int $id_agenda O ID da agenda para excluir as relações.
     * @return bool Retorna true se a operação for bem-sucedida, caso contrário retorna false.
    */
    public static function deleteAgendaUsuario(int $id_agenda):bool
    {
        $db = new agendaUsuario;

        return $db->addFilter("agenda_usuario.id_agenda","=",$id_agenda)->deleteByFilter();  
    }

    /**
     * Exclui as relações de um funcionário com a agenda.
     * 
     * @param int $id_agenda O ID da agenda para excluir as relações.
     * @return bool Retorna true se a operação for bem-sucedida, caso contrário retorna false.
    */
    public static function deleteAgendaFuncionario(int $id_agenda):bool
    {
        $db = new agendaFuncionario;

        return $db->addFilter("agenda_funcionario.id_agenda","=",$id_agenda)->deleteByFilter();
    }

}