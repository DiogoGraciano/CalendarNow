<?php 
namespace app\models\main;
use app\db\db;
use app\classes\mensagem;
use app\classes\modelAbstract;

class agendaModel{

    public static function get($cd = ""){
        return modelAbstract::get("agenda",$cd);
    }

    public static function getByEmpresa($cd = ""){
        $db = new db("agenda");
        
        $values = $db->addFilter("agenda.id_empresa","=",$cd)
                     ->selectColumns(["id","agenda.nome"]);
        
        if($values)
            return $values;
        
        return false;
    }

    public static function getByUser($cd = ""){
        $db = new db("agenda_usuario");
        
        $values = $db->addJoin("INNER","agenda","agenda_usuario.id_agenda","agenda.id")
                     ->addJoin("INNER","empresa","agenda.id_empresa","empresa.id")
                     ->addFilter("agenda_usuario.id_usuario","=",$cd)
                     ->selectColumns(["id","agenda.nome","empresa.nome as emp_nome"]);

        if($values)
            return $values;
        
        return false;
    }

    public static function setAgendaUsuario($id_usuario,$id_agenda){
        $db = new db("agenda_usuario");

        $values = $db->getObject();

        $values->id_usuario = $id_usuario;
        $values->id_agenda = $id_agenda;

        if ($values)
            $retorno = $db->storeMutiPrimary($values);

        if ($retorno == true){
            mensagem::setSucesso(array("Agenda salvo com Sucesso"));
            return $db->getLastID();
        }
        else {
            $erros = ($db->getError());
            mensagem::setErro(array("Erro ao execultar a ação tente novamente"));
            mensagem::addErro($erros);
            return False;
        }
    }

    public static function set($nome,$id_empresa,$id=""){

        $db = new db("agenda");
        
        $values = $db->getObject();

        $values->id = $id;
        $values->id_empresa = $id_empresa;
        $values->nome = $nome;

        if ($values)
            $retorno = $db->store($values);

        if ($retorno == true){
            mensagem::setSucesso(array("Agenda salvo com Sucesso"));
            return $db->getLastID();
        }
        else {
            $erros = ($db->getError());
            mensagem::setErro(array("Erro ao execultar a ação tente novamente"));
            mensagem::addErro($erros);
            return False;
        }
    }

    public static function delete($cd){
        modelAbstract::delete("agenda",$cd);
    }

    public static function deleteAgendaUsuario($id_usuario,$id_agenda){
        $db = new db("agenda_usuario");

        $retorno =  $db->addFilter("agenda_usuario.id_usuario","=",$id_usuario)->addFilter("agenda_usuario.id_agenda","=",$id_agenda)->deleteByFilter();

        if ($retorno == true){
            mensagem::setSucesso(array("Excluido com Sucesso"));
            return True;
        }
        else {
            $erros = ($db->getError());
            mensagem::setErro(array("Erro ao execultar a ação tente novamente"));
            mensagem::addErro($erros);
            return False;
        }
    }

}