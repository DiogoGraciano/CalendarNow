<?php 
namespace app\models\main;
use app\db\db;
use app\classes\mensagem;
use app\classes\modelAbstract;
use app\classes\functions;

class enderecoModel{

    public static function get($cd = ""){
        return modelAbstract::get("endereco",$cd);
    }

    public static function getbyIdUsuario($id_usuario = ""){
        $db = new db("endereco");

        $values = $db->selectByValues(["id_usuario"],[$id_usuario],true);

        if ($db->getError()){
            return [];
        }

        return $values;
    }

    public static function set($cep,$id_estado,$id_cidade,$bairro,$rua,$numero,$complemento="null",$cd = "",$id_usuario="null",$id_empresa="null"){

        $db = new db("endereco");

        $values = $db->getObject();

        $values->id = $cd;
        $values->id_usuario = $id_usuario;
        $values->id_empresa = $id_empresa;
        $values->cep = (int)functions::onlynumber($cep);
        $values->id_estado = $id_estado;
        $values->id_cidade = $id_cidade;
        $values->bairro = $bairro;
        $values->rua = $rua;
        $values->numero = $numero;
        $values->complemento = $complemento;
        $retorno = $db->store($values);

        if ($retorno == true)
            return $db->getLastID();
        else 
            return False;
        
    }
    
    public static function delete($cd){
        modelAbstract::delete("endereco",$cd);
    }

}