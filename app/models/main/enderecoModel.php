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

        return $db->selectByValues(["id_usuario"],[$id_usuario],true);
    }

    public static function set($cep,$id_estado,$id_cidade,$bairro,$rua,$numero,$complemento,$cd = "",$id_usuario="",$id_empresa=""){

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

        if ($retorno == true){
            mensagem::setSucesso(array("Endereço salvo com Sucesso"));
            return $db->lastid;
        }
        else {
            $Mensagems = ($db->getError());
            mensagem::setErro(array("Erro ao execultar a ação tente novamente"));
            mensagem::addErro($Mensagems);
            return False;
        }
    }
    
    public static function delete($cd){
        modelAbstract::delete("endereco",$cd);
    }

}