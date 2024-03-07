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

    public static function getAll($table){

        $db = new db($table);

        return $db->selectAll();
    }

    public static function delete($table,$cd){

        $db = new db($table);

        $retorno = $db->delete($cd);

        return $retorno;
    }

}