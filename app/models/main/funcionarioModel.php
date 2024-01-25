<?php 
namespace app\models\main;
use app\db\db;
use app\classes\mensagem;
use app\classes\modelAbstract;

class funcionarioModel{

    public static function get($cd = ""){
        return modelAbstract::get("funcionario",$cd);
    }

    public static function getListFuncionariosByEmpresa($id_empresa){

        $db = new db("funcionario");

        $usuario = $db->addJoin("INNER","usuario","usuario.id","funcionario.id_usuario")
                    ->addJoin("INNER","grupo_funcionario","grupo_funcionario.id","funcionario.id_grupo_funcionario")
                    ->addJoin("INNER","grupo_servico","grupo_servico.id","funcionario.id_grupo_servico")
                    ->addFilter("usuario.id_empresa","=",$id_empresa)
                    ->selectColumns(["funcionario.id,cpf_cnpj,email,telefone,grupo_servico.nome as grupo_servico_nome,grupo_funcionario.nome as grupo_funcionario_nome,hora_ini,hora_fim,dias"]);

        return $usuario;
    }

    public static function set($id_usuario,$id_grupo_funcionario,$id_grupo_servico,$hora_ini,$hora_fim,$dias,$id=""){

        $db = new db("funcionario");

        $values = $db->getObject();

        $values->id = $id;
        $values->id_usuario = $id_usuario;
        $values->id_grupo_funcionario = $id_grupo_funcionario;
        $values->id_grupo_servico = $id_grupo_servico;
        $values->hora_ini = $hora_ini;
        $values->hora_fim = $hora_fim;
        $values->dias = $dias;
        $retorno = $db->store($values);
        
        if ($retorno == true){
            mensagem::setSucesso(array("Funcionario salvo com Sucesso"));
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
        modelAbstract::delete("funcionario",$cd);
    }

}