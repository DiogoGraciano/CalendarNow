<?php 
namespace app\models\main;
use app\db\db;
use app\classes\mensagem;
use app\classes\modelAbstract;

class agendaServicoModel{

    public static function get($id_agenda,$id_servico){
        $db = new db("agenda_servico");

        return $db->selectByValues(["id_agenda","id_servico"],[$id_agenda,$id_servico]);
    }

    public static function set($nome,$id){

        $db = new db("agenda_servico");
        
        $values = $db->getObject();

        $values->id = $id;
        $values->nome = $nome;

        if ($values)
            $retorno = $db->store($values);

        if ($retorno == true){
            mensagem::setSucesso(array("Grupo Funcionario salvo com Sucesso"));
            return True;
        }
        else {
            $erros = ($db->getError());
            mensagem::setErro(array("Erro ao execultar a ação tente novamente"));
            mensagem::addErro($erros);
            return False;
        }

       
    }

    public static function delete($cd){
        modelAbstract::delete("agenda_servico",$cd);
    }

}