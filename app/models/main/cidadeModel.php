<?php 
namespace app\models\main;
use app\db\db;
use app\classes\modelAbstract;
use app\classes\elements;

class cidadeModel{

    public static function get($cd = ""){
        return modelAbstract::get("cidade",$cd);
    }

    public static function getByNome($nome){
        $db = new db("cidade");

        $cidade = $db->addFilter("nome","LIKE","%".$nome."%")->addLimit(1)->selectAll();

        return $cidade;
    }

    public static function getByNomeIdUf($nome,$id_uf){
        $db = new db("cidade");

        $cidade = $db->addFilter("nome","LIKE","%".$nome."%")->addLimit(1)->selectByValues(array("uf"),array($id_uf),true);

        return $cidade;
    }

    public static function getOptionsbyEstado($id_estado){
        $resultados = cidadeModel::getByEstado($id_estado);
        $elements = new elements;

        $options = [];
        foreach ($resultados as $resultado){
            $elements->addOption($resultado->id,$resultado->nome);
        }   
    }

    public static function getByIbge($ibge){
        $db = new db("cidade");

        $cidade = $db->selectByValues(array("ibge"),array($ibge),true);

        return $cidade;
    }

    public static function getByEstado($id_estado){
        $db = new db("cidade");

        $cidade = $db->addFilter("uf","=",$id_estado)->selectAll();

        return $cidade;
    }

}