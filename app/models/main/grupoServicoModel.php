<?php 
namespace app\models\main;
use app\db\grupoServico;
use app\classes\mensagem;
use app\classes\modelAbstract;

class grupoServicoModel{

    public static function get($id = ""){
        return grupoServico::selectOne($id);
    }

    public static function getAll(){
        return grupoServico::getAll();
    }

    public static function set($nome,$id){

        $db = new grupoServico;
        
        $values = $db->getObject();

        $values->id = $id;
        $values->nome = $nome;

        if ($values)
            $retorno = $db->store($values);

        if ($retorno == true){
            mensagem::setSucesso(array("Grupo Serviço salvo com Sucesso"));
            return True;
        }
        else {
            $erros = ($db->getError());
            mensagem::setErro(array("Erro ao execultar a ação tente novamente"));
            mensagem::addErro($erros);
            return False;
        }
    }

    public static function getByEmpresa($id_empresa){
        $db = new grupoServico;

        $values = $db->addFilter("id_empresa","=",$id_empresa)->selectAll();

        if ($Mensagems = ($db->getError())){
            mensagem::setErro($Mensagems);
            return [];
        }

        return $values;
    }

    public static function delete($id){
        grupoServico::delete($id);
    }

}