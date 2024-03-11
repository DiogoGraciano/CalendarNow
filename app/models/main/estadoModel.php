<?php 
namespace app\models\main;
use app\db\estado;
use app\classes\modelAbstract;
use app\classes\mensagem;

class estadoModel{

    public static function get($id){
        return estado::selectOne($id);
    }

    public static function getByUf($uf){
        $db = new estado;

        $estado = $db->selectByValues(["uf"],[$uf],true);

        if ($Mensagems = ($db->getError())){
            mensagem::setErro($Mensagems);
            return [];
        }

        return $estado;
    }

}