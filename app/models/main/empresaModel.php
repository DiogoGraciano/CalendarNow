<?php 
namespace app\models\main;

use app\classes\functions;
use app\db\empresa;
use app\classes\modelAbstract;

class empresaModel{

    public static function get($id){
        return new empresa($id);
    }

    public static function getByAgenda($id_agenda){
        $db = new empresa;
        $db->addJoin("INNER","agenda","agenda.id_empresa","empresa.id");

    }

    public static function set($nome,$cpf_cnpj,$email,$telefone,$razao,$fantasia,$id=""){

        $db = new empresa;

        $values = $db->getObject();

        $values->id = $id;
        $values->nome = $nome;
        $values->cnpj = functions::onlynumber($cpf_cnpj);
        $values->email = $email;
        $values->telefone = functions::onlynumber($telefone);
        $values->razao = $razao;
        $values->fantasia = $fantasia;
        $retorno = $db->store($values);
        
        if ($retorno == true)
            return $db->getLastID();
        else 
            return False;
        
    }
    
    public static function delete($id){
        empresa::delete($id);
    }

}