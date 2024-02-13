<?php 
namespace app\models\main;
use app\db\db;
use app\classes\mensagem;
use app\classes\modelAbstract;

class empresaModel{

    public static function get($cd = ""){
        return modelAbstract::get("empresa",$cd);
    }

    public static function getByAgenda($id_agenda){
        $db = new db("empresa");
        $db->addJoin("INNER","agenda","agenda.id_empresa","empresa.id");

    }

    public static function set($nome,$cpf_cnpj,$email,$telefone,$razao,$fantasia,$id=""){

        $db = new db("empresa");

        $values = $db->getObject();

        $values->id = $id;
        $values->nome = $nome;
        $values->cnpj = $cpf_cnpj;
        $values->email = $email;
        $values->telefone = $telefone;
        $values->razao = $razao;
        $values->fantasia = $fantasia;
        $retorno = $db->store($values);
        
        if ($retorno == true){
            mensagem::setSucesso(array("Empresa salvo com Sucesso"));
            return $db->getLastID();
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