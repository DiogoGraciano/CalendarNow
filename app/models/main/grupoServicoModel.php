<?php 
namespace app\models\main;
use app\db\db;
use app\classes\mensagem;
use app\classes\modelAbstract;

class grupoServicoModel{

    public static function get($cd = ""){
        return modelAbstract::get("grupo_servico",$cd);
    }

    public static function getAll(){
        return modelAbstract::getAll("grupo_servico");
    }

    public static function set($nome,$id){

        $db = new db("grupo_servico");
        
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

    public static function delete($cd){
        modelAbstract::delete("grupo_servico",$cd);
    }

}