<?php 
namespace app\models\main;
use app\db\estado;
use app\classes\modelAbstract;
use app\classes\mensagem;

class estadoModel{

    public static function get($id){
        return (new estado)->get($id);
    }

    public static function getByUf($uf){
        $db = new estado;

        $estado = $db->selectByValues(["uf"],[$uf],true);

        if ($Mensagems = ($db->getError())){
            return [];
        }

        return $estado;
    }

}