<?php 
namespace app\models\main;
use app\db\db;
use app\classes\mensagem;
use app\classes\modelAbstract;

class enderecoModel{

    public static function get($cd = ""){
        return modelAbstract::get("endereco",$cd);
    }

    public static function set($cep,$id_estado,$id_cidade,$rua,$numero,$complemento,$cd = ""){

        $db = new db("endereco");

        $values = $db->getObject();

        $values->id = $cd;
        $values->cep = $cep;
        $values->id_estado = $id_estado;
        $values->id_cidade = $id_cidade;
        $values->rua = $rua;
        $values->numero = $numero;
        $values->complemento = $complemento;
        $retorno = $db->store($values);

        if ($retorno == true){
            mensagem::setSucesso(array("Endereço salvo com Sucesso"));
            return True;
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