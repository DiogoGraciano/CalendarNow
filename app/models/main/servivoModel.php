<?php 
namespace app\models\main;
use app\db\db;
use app\classes\mensagem;
use app\classes\modelAbstract;

class servicoModel{

    public static function get($cd = ""){
        return modelAbstract::get("servico",$cd);
    }

    public static function getByEmpresa($cd = ""){
        $db = new db("servico");
        
        $values = $db->addJoin("INNER","agenda_servico","agenda_servico.id_servico","servico.id")
                     ->addJoin("INNER","agenda","agenda_servico.id_agenda","agenda.id")
                     ->addFilter("agenda.id_empresa","=",$cd)
                     ->selectColumns(["servico.id","agenda.nome as age_nome","empresa.nome as emp_nome","servico.nome as ser_nome","servico.valor","servico.tempo"]);
        
        if($values)
            return $values;
        
        return false;
    }

    public static function getByUser($cd = ""){
        $db = new db("servico");
        
        $values = $db->addJoin("INNER","agenda_servico","agenda_servico.id_servico","servico.id")
                    ->addJoin("INNER","agenda","agenda_servico.id_agenda","agenda.id")
                    ->addJoin("INNER","agenda_usuario","agenda_usuario.id_agenda","agenda.id")
                    ->addFilter("agenda_usuario.id_usuario","=",$cd)
                    ->selectColumns(["servico.id","agenda.nome as age_nome","empresa.nome as emp_nome","servico.nome as ser_nome","servico.valor","servico.tempo"]);

        if($values)
            return $values;
        
        return false;
    }

    public static function setAgendaServico($id_usuario,$id_servico){
        $db = new db("agenda_servico");

        $values = $db->getObject();

        $values->id_usuario = $id_usuario;
        $values->id_servico = $id_servico;

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

    public static function set($nome,$valor,$tempo,$id_grupo="",$id=""){

        $db = new db("servico");
        
        $values = $db->getObject();

        $values->id = $id;
        $values->valor = $valor;
        $values->tempo = $tempo;
        $values->id_grupo = $id_grupo;
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
        modelAbstract::delete("servico",$cd);
    }

    public static function deleteAgendaServico($id_servico,$id_agenda){
        $db = new db("agenda_servico");

        $retorno =  $db->addFilter("agenda_servico.id_servico","=",$id_servico)->addFilter("agenda_servico.id_agenda","=",$id_agenda)->deleteByFilter();

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