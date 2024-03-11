<?php
namespace app\classes;
use app\db\db;

abstract class modelAbstract{

    public static function get($table,$id=""){

        $db = new db($table);

        if ($id)
            $retorno = $db->selectOne($id);
        else
            $retorno = $db->getObject();

        return $retorno;
    }

    public static function getAll($table){

        $db = new db($table);

        return $db->selectAll();
    }

    public static function delete($table,$id){

        $db = new db($table);

        $retorno = $db->delete($id);

        return $retorno;
    }

}