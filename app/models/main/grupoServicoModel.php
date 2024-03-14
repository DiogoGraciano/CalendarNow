<?php 
namespace app\models\main;
use app\db\grupoServico;
use app\classes\mensagem;
use app\classes\modelAbstract;

class grupoServicoModel{

    public static function get($id = ""){
        return (new grupoServico)->get($id);
    }

    public static function getAll(){
        return (new grupoServico)->getAll($id);
    }

    public static function set($nome,$id){

        $db = new grupoServico;
        
        $values = $db->getObject();

        $values->id = $id;
        $values->nome = $nome;

        if ($values)
            $retorno = $db->store($values);

        if ($retorno == true){
            return True;
        }
        else {
            return False;
        }
    }

    public static function getByEmpresa($id_empresa){
        $db = new grupoServico;

        $values = $db->addFilter("id_empresa","=",$id_empresa)->selectAll();

        if ($Mensagems = ($db->getError())){
            return [];
        }

        return $values;
    }

    public static function delete($id){
        return (new grupoServico)->delete($id);
    }

}