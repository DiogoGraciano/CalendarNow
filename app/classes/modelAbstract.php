<?php
namespace app\classes;
use app\db\db;

abstract class modelAbstract{

    public static function get($table,$cd=""){

        $db = new db($table);

        if ($cd)
            $retorno = $db->selectOne($cd);
        else
            $retorno = $db->getObject();

        return $retorno;
    }

    public static function delete($table,$cd){

        $db = new db($table);

        $retorno = $db->delete($cd);

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