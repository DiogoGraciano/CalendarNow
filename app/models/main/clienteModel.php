<?php 
namespace app\models\main;
use app\db\db;
use app\classes\modelAbstract;

class clienteModel{

    public static function get($cd){
        return modelAbstract::get("cliente",$cd);
    }

    public static function getByFuncionario($id_funcionario){
        $db = new db("cliente");
        $cliente = $db->addFilter("cliente.id_funcionario","=",$id_funcionario)
                        ->selectAll();

        if ($db->getError()){
            return [];
        }

        return $cliente;
    }

    public static function set($nome,$id_empresa,$cd=""){

        $db = new db("cliente");
    
        $values = $db->getObject();

        if ($values){
            $values->id = intval($cd);
            $values->id_empresa = intval($id_empresa);
            $values->nome = trim($nome);
            $retorno = $db->store($values);
        }
        if ($retorno == true)
            return $db->getLastID();
        else 
            return False;
        
        
    }

    public static function delete($cd=""){
        modelAbstract::delete("cliente",$cd);
    }

}