<?php 
namespace app\models\main;
use app\db\db;
use app\classes\mensagem;
use app\classes\modelAbstract;

class grupoFuncionarioModel{

    public static function get($cd = ""){
        return modelAbstract::get("grupo_funcionario",$cd);
    }

    public static function set($nome,$id){

        $db = new db("grupo_funcionario");
        
        $values = $db->getObject();

        $values->id = $id;
        $values->nome = $nome;

        if ($values)
            $retorno = $db->store($values);

        if ($retorno == true){
            mensagem::setSucesso(array("Grupo Funcionario salvo com Sucesso"));
            return True;
        }
        else {
            $erros = ($db->getError());
            mensagem::setErro(array("Erro ao execultar a ação tente novamente"));
            mensagem::addErro($erros);
            return False;
        }

       
    }

    public static function delete($cd){
        modelAbstract::delete("grupo_funcionario",$cd);
    }

}