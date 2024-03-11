<?php 
namespace app\models\main;
use app\db\db;
use app\classes\mensagem;
use app\classes\modelAbstract;

class grupoFuncionarioModel{

    public static function get($id = ""){
        return funcionario::selectOne($id);
    }

    public static function getAll(){
        return funcionario::selectAll();
    }

    public static function getByEmpresa($id_empresa){
        $db = new grupoFuncionario;

        $values = $db->addFilter("id_empresa","=",$id_empresa)->selectAll();

        if ($Mensagems = ($db->getError())){
            mensagem::setErro($Mensagems);
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

    public static function delete($id){
        funcionario::delete($id);
    }

}