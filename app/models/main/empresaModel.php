<?php 
namespace app\models\main;
use app\db\db;
use app\classes\mensagem;
use app\classes\modelAbstract;

class empresaModel{

    public static function get($cd = ""){
        return modelAbstract::get("empresa",$cd);
    }

    public static function set($nome,$cpf_cnpj,$razao,$fantasia){

        $db = new db("empresa");

        $values = $db->getObject();

        $values->nome = $nome;
        $values->cpf_cnpj = $cpf_cnpj;
        $values->razao = $razao;
        $values->fantasia = $fantasia;
        $retorno = $db->store($values);
        
        if ($retorno == true){
            mensagem::setSucesso(array("Empresa salvo com Sucesso"));
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
        modelAbstract::delete("empresa",$cd);
    }

}