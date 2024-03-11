<?php 
namespace app\models\main;
use app\db\db;
use app\classes\modelAbstract;

class clienteModel{

    public static function get($id){
        return cliente::selectOne($id);
    }

    public static function getByFuncionario($id_funcionario){
        $db = new cliente;
        $cliente = $db->addFilter("cliente.id_funcionario","=",$id_funcionario)
                        ->selectAll();

        if ($db->getError()){
            return [];
        }

        return $cliente;
    }

    public static function set($nome,$id_empresa,$id=""){

        $db = new cliente;
    
        $values = $db->getObject();

        if ($values){
            $values->id = intval($id);
            $values->id_empresa = intval($id_empresa);
            $values->nome = trim($nome);
            $retorno = $db->store($values);
        }
        if ($retorno == true)
            return $db->getLastID();
        else 
            return False;
        
        
    }

    public static function delete($id){
        cliente::delete($id);
    }

}