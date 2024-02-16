<?php 
namespace app\models\main;
use app\db\db;
use app\classes\modelAbstract;
use app\classes\mensagem;

class estadoModel{

    public static function get($cd = ""){
        return modelAbstract::get("estado",$cd);
    }

    public static function getByUf($uf){
        $db = new db("estado");

        $estado = $db->selectByValues(["uf"],[$uf],true);

        if ($Mensagems = ($db->getError())){
            mensagem::setErro($Mensagems);
            return [];
        }

        return $estado;
    }

}