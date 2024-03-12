<?php 
namespace app\models\main;
use app\db\endereco;
use app\classes\mensagem;
use app\classes\modelAbstract;
use app\classes\functions;

class enderecoModel{

    public static function get($id = ""){
        return (new endereco)->get($id);
    }

    public static function getbyIdUsuario($id_usuario = ""){
        $db = new endereco;

        $values = $db->selectByValues(["id_usuario"],[$id_usuario],true);

        if ($db->getError()){
            return [];
        }

        return $values;
    }

    public static function set($cep,$id_estado,$id_cidade,$bairro,$rua,$numero,$complemento="null",$id = "",$id_usuario="null",$id_empresa="null"){

        $db = new endereco;

        $values = $db->getObject();

        $values->id = $id;
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
    
    public static function delete($id){
       return (new endereco)->delete($id);
    }

}