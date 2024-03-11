<?php 
namespace app\models\main;
use app\db\db;
use app\classes\mensagem;
use app\classes\modelAbstract;
use app\classes\functions;

class agendaModel{

    public static function get($id = ""){
        return agenda::selectOne("agenda",$id);
    }

    public static function getByEmpresa($id_empresa = ""){
        $db = new agenda;
        
        $values = $db->addFilter("agenda.id_empresa","=",$id_empresa)
                     ->selectColumns(["id","agenda.nome","agenda.codigo"]);
        
        if($values)
            return $values;
        
        return false;
    }

    public static function getByCodigo($codigo = ""){
        $db = new agenda;
        
        $values = $db->addFilter("agenda.codigo","=",$codigo)
                     ->selectColumns(["id","agenda.nome","agenda.codigo"]);
        
        if($values)
            return $values;
        
        return false;
    }

    public static function getByUser($id_usuario = ""){
        $db = new agendaUsuario;
        
        $db->addJoin("INNER","agenda","agenda_usuario.id_agenda","agenda.id")->addJoin("INNER","empresa","agenda.id_empresa","empresa.id");
                    
        if($id_usuario){
            $db->addFilter("agenda_usuario.id_usuario","=",$id_usuario);  
        }
        
        if($values = $db->selectColumns(["agenda.id","agenda.nome","empresa.nome as emp_nome"]))
            return $values;
        
        return false;
    }

    public static function getByUserServico($id_servico,$id_usuario){
        $db = new agendaUsuario;
        
        $values = $db->addJoin("INNER","agenda","agenda_usuario.id_agenda","agenda.id")
                     ->addJoin("INNER","empresa","agenda.id_empresa","empresa.id")
                     ->addJoin("INNER","agenda_servico","agenda_servico.id_agenda","agenda.id")
                     ->addJoin("INNER","agenda_usuario","agenda_usuario.id_agenda","agenda_servico.id_agenda")
                     ->addFilter("agenda_servico.id_servico","=",$id_servico)
                     ->addFilter("agenda_usuario.id_usuario","=",$id_usuario)
                     ->selectColumns(["id","agenda.nome","empresa.nome as emp_nome"]);

        if($values)
            return $values;
        
        return false;
    }

    public static function setAgendaUsuario($id_usuario,$id_agenda){
        $db = new agendaUsuario;

        $retorno = $db->addFilter("agenda_usuario.id_usuario","=",$id_usuario)
                ->addFilter("agenda_usuario.id_agenda","=",$id_agendas)
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

    public static function setAgendaFuncionario($id_funcionario,$id_agenda){
        $db = new agendaFuncionario;

        $retorno = $db->addFilter("agenda_funcionario.id_funcionario","=",$id_funcionario)
                ->addFilter("agenda_funcionario.id_agenda","=",$id_agendas)
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


    public static function set($nome,$id_empresa,$codigo="",$id=""){

        $db = new agenda;
        
        $values = $db->getObject();

        $values->id = $id;
        $values->id_empresa = $id_empresa;
        $values->nome = $nome;
        if ($codigo)
            $values->codigo = $codigo;
        else 
            $values->codigo = functions::genereteCode(7);

        if ($values)
            $retorno = $db->store($values);

        if ($retorno == true){
            return $db->getLastID();
        }
        else {
            return False;
        }
    }

    public static function delete($id){
        agenda::delete($id);
    }

    public static function deleteAgendaUsuario($id_agenda){
        $db = new agendaUsuario;

        $retorno =  $db->addFilter("agenda_usuario.id_agenda","=",$id_agenda)->deleteByFilter();

        if ($retorno == true){
            return True;
        }
        else {
            return False;
        }
    }

    public static function deleteAgendaFuncionario($id_agenda){
        $db = new agendaFuncionario;

        $retorno =  $db->addFilter("agenda_funcionario.id_agenda","=",$id_agenda)->deleteByFilter();

        if ($retorno == true){
            return True;
        }
        else {
            return False;
        }
    }

}