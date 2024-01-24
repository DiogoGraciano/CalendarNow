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
        modelAbstract::delete("agenda",$cd);
    }

}