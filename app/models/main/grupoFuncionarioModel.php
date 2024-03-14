<?php 
namespace app\models\main;
use app\db\funcionario;
use app\db\grupoFuncionario;
use app\classes\mensagem;
use app\classes\modelAbstract;

class grupoFuncionarioModel{

    public static function get($id = ""){
        return (new grupoFuncionario)->get($id);
    }

    public static function getAll(){
        return (new grupoFuncionario)->selectAll();
    }

    public static function getByEmpresa($id_empresa){
        $db = new grupoFuncionario;

        $values = $db->addFilter("id_empresa","=",$id_empresa)->selectAll();

        if ($Mensagems = ($db->getError())){
            return [];
        }

        return $values;
    }

    public static function set($nome,$id){

        $db = new grupoFuncionario;
        
        $values = $db->getObject();

        $values->id = $id;
        $values->nome = $nome;

        if ($values)
            $retorno = $db->store($values);

        if ($retorno == true){
            return True;
        }
        
        return False;
    }

    public static function delete($id){
        return (new grupoFuncionario)->delete($id);
    }

}