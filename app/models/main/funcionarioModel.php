<?php 
namespace app\models\main;

use app\classes\functions;
use app\db\db;
use app\classes\modelAbstract;

class funcionarioModel{

    public static function get($cd = ""){
        return modelAbstract::get("funcionario",$cd);
    }

    public static function getAll(){
        return modelAbstract::getAll("funcionario");
    }

    public static function getListFuncionariosByEmpresa($id_empresa){

        $db = new db("funcionario");

        $funcionarios = $db
                    ->addJoin("INNER","usuario","usuario.id","funcionario.id_usuario")
                    ->addFilter("usuario.id_empresa","=",$id_empresa)
                    ->selectColumns(["funcionario.id,funcionario.cpf_cnpj,funcionario.nome,funcionario.email,funcionario.telefone,hora_ini,hora_fim,hora_almoco_ini,hora_almoco_fim,dias"]);

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

        if ($db->getError()){
            return [];
        }
        
        return $funcionarioFinal;
    }

    public static function getListFuncionariosByGrupoFuncionario($id_grupo_funcionario){

        $db = new db("funcionario_grupo_funcionario");

        $funcionarios = $db
                    ->addJoin("INNER","funcionario","funcionario.id","funcionario_grupo_funcionario.id_funcionario")
                    ->addJoin("INNER","usuario","usuario.id","funcionario.id_usuario")
                    ->addFilter("funcionario_grupo_funcionario.id_grupo_funcionario","=",$id_grupo_funcionario)
                    ->selectColumns(["funcionario.id,funcionario.cpf_cnpj,funcionario.nome,funcionario.email,funcionario.telefone,hora_ini,hora_fim,hora_almoco_ini,hora_almoco_fim,dias"]);

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

        if ($db->getError()){
            return [];
        }
        
        return $funcionarioFinal;
    }

    public static function getByAgenda($id_agenda){
        $db = new db("agenda_funcionario");

        $values = $db->addJoin("INNER","agenda","agenda.id","agenda_funcionario.id_agenda")
                ->addJoin("INNER","funcionario","funcionario.id","agenda_funcionario.id_funcionario")
                ->addFilter("agenda_funcionario.id_agenda","=",$id_agenda)
                ->selectColumns(["funcionario.id","funcionario.nome","agenda.nome as age_nome","funcionario.cpf_cnpj","funcionario.email","funcionario.telefone","hora_ini","hora_fim","dias"]);

       if ($db->getError()){
            return [];
        }

        return $values;
    }

    public static function getByEmpresa($id_empresa){
        $db = new db("funcionario");

        $values = $db->addJoin("INNER","usuario","usuario.id","funcionario.id_usuario")
                ->addFilter("usuario.id_empresa","=",$id_empresa)
                ->selectColumns(["funcionario.id","funcionario.nome","funcionario.cpf_cnpj","funcionario.email","funcionario.telefone","hora_ini","hora_fim","dias"]);

        if ($db->getError()){
            return [];
        }

        return $values;
    }

    public static function set($id_usuario,$nome,$cpf_cnpj,$email,$telefone,$hora_ini,$hora_fim,$hora_almoco_ini,$hora_almoco_fim,$dias,$id=""){

        $db = new db("funcionario");

        $values = $db->getObject();

        $values->id = $id;
        $values->id_usuario = $id_usuario;
        $values->nome = $nome;
        $values->cpf_cnpj = functions::onlynumber($cpf_cnpj);
        $values->email = $email;
        $values->telefone = functions::onlynumber($telefone);
        $values->hora_ini = $hora_ini;
        $values->hora_fim = $hora_fim;
        $values->hora_almoco_ini = $hora_almoco_ini;
        $values->hora_almoco_fim = $hora_almoco_fim;
        $values->dias = $dias;
        $retorno = $db->store($values);
        
        if ($retorno == true){
            return $db->getLastID();
        }
        else {
            return False;
        }
    }

    public static function setAgendaFuncionario($id_funcionario,$id_agenda){
        $db = new db("agenda_funcionario");

        $result = $db->addFilter("id_agenda","=",$id_agenda)
                    ->addFilter("id_funcionario","=",$id_funcionario)
                    ->selectAll();

        if (!$result){
            $values = $db->getObject();

            $values->id_agenda = $id_agenda;
            $values->id_funcionario = $id_funcionario;

            if ($values)
                $retorno = $db->storeMutiPrimary($values);

            if ($retorno == true)
                return $db->getLastID();
            else 
                return False;
            
        }
        return True;
    }

    public static function setFuncionarioGrupoFuncionario($id_funcionario,$id_grupo_funcionario){
        $db = new db("funcionario_grupo_funcionario");

        $result = $db->addFilter("id_grupo_funcionario","=",$id_grupo_funcionario)
                    ->addFilter("id_funcionario","=",$id_funcionario)
                    ->selectAll();

        if (!$result){
            $values = $db->getObject();

            $values->id_grupo_funcionario = $id_grupo_funcionario;
            $values->id_funcionario = $id_funcionario;

            if ($values)
                $retorno = $db->storeMutiPrimary($values);

            if ($retorno == true){
                return $db->getLastID();
            }
            else {
                return False;
            }
        }
        return True;
    }
    
    public static function delete($cd){
        modelAbstract::delete("funcionario",$cd);
    }

}