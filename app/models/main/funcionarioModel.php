<?php 
namespace app\models\main;

use app\classes\functions;
use app\db\db;
use app\classes\mensagem;
use app\classes\modelAbstract;

class funcionarioModel{

    public static function get($cd = ""){
        return modelAbstract::get("funcionario",$cd);
    }

    public static function getListFuncionariosByEmpresa($id_empresa){

        $db = new db("funcionario");

        $funcionarios = $db
                    ->addJoin("LEFT","grupo_funcionario","grupo_funcionario.id","funcionario.id_grupo_funcionario")
                    ->addJoin("LEFT","grupo_servico","grupo_servico.id","funcionario.id_grupo_servico")
                    ->addFilter("usuario.id_empresa","=",$id_empresa)
                    ->selectColumns(["funcionario.id,cpf_cnpj,usuario.nome,email,telefone,grupo_servico.nome as grupo_servico_nome,grupo_funcionario.nome as grupo_funcionario_nome,hora_ini,hora_fim,hora_almoco_ini,hora_almoco_fim,dias"]);

        $funcionarioFinal = [];
        if ($funcionarios){
            foreach ($funcionarios as $funcionario){
                if ($funcionario->cpf_cnpj){
                    $funcionario->cpf_cnpj = functions::formatCnpjCpf($funcionario->cpf_cnpj);
                }
                if ($funcionario->telefone){
                    $funcionario->telefone = functions::formatPhone($funcionario->telefone);
                }
                if ($funcionario->dias){
                    $funcionario->dias = functions::formatDias($funcionario->dias);
                }
                $funcionarioFinal[] = $funcionario;
            }
        }
        
        return $funcionarioFinal;
    }

    public static function set($id_usuario,$id_grupo_funcionario,$id_grupo_servico,$nome,$cpf_cnpj,$email,$telefone,$hora_ini,$hora_fim,$hora_almoco_ini,$hora_almoco_fim,$dias,$id=""){

        $db = new db("funcionario");

        $values = $db->getObject();

        $values->id = $id;
        $values->id_usuario = $id_usuario;
        $values->id_grupo_funcionario = $id_grupo_funcionario;
        $values->id_grupo_servico = $id_grupo_servico;
        $values->nome = $nome;
        $values->cpf_cnpj = $cpf_cnpj;
        $values->email = $email;
        $values->telefone = $telefone;
        $values->hora_ini = $hora_ini;
        $values->hora_fim = $hora_fim;
        $values->hora_almoco_ini = $hora_almoco_ini;
        $values->hora_almoco_fim = $hora_almoco_fim;
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