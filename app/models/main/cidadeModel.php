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

        $cidade = $db->selectAll()->addFilter("nome","LIKE","%".$nome."%")->addFilter(1);

        return $cidade;
    }

    public static function getByNomeIdUf($nome,$id_uf){
        $db = new db("cidade");

        $cidade = $db->selectByValues(array("uf"),array($id_uf),true)->addFilter("nome","LIKE","%".$nome."%")->addFilter(1);

        return $cidade;
    }

    public static function getOptionsbyEstado($id_estado){
        $resultados = cidadeModel::getByEstado($id_estado);
        $elements = new elements;

        $options = [];
        foreach ($resultados as $resultado){
            $options[] = $elements->getObjectOption($resultado->id,$resultado->nome);
        }

        return $options;
        
    }

    public static function getByIbge($ibge){
        $db = new db("cidade");

        $cidade = $db->selectByValues(array("ibge"),array($ibge),true);

        return $cidade;
    }

    public static function getByEstado($id_estado){
        $db = new db("cidade");

        $cidade = $db->selectAll()->addFilter("uf","=",$id_estado);

        return $cidade;
    }

}