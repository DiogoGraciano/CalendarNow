<?php 
namespace app\models\main;

use app\classes\functions;
use app\db\servico;
use app\db\servicoFuncionario;
use app\db\ServicoGrupoServico;
use app\classes\mensagem;
use app\classes\modelAbstract;

class servicoModel{

    public static function get($id){
        return (new servico)->get($id);
    }

    public static function getByEmpresa($id_empresa){
        $db = new servico;
        
        $values = $db->addFilter("servico.id_empresa","=",$id_empresa)
                     ->selectColumns(["servico.id","servico.nome","servico.tempo","servico.valor"]);

        $valuesFinal = [];

        if ($Mensagems = ($db->getError())){
            mensagem::setErro($Mensagems);
            return [];
        }

        if ($values){
            foreach ($values as $value){
                if ($value->valor){
                    $value->valor = functions::formatCurrency($value->valor);
                }
                $valuesFinal[] = $value;
            }

            return $values;
        }
    }

    public static function getByFuncionario($id_Funcionario){
        $db = new servicoFuncionario;
        
        $values = $db->addjoin("INNER","servico","servico.id","servico_funcionario.id_servico")
                     ->addFilter("servico_funcionario.id_funcionario","=",$id_Funcionario)
                     ->selectColumns(["servico.id","servico.nome","servico.tempo","servico.valor"]);

        

        if ($Mensagems = ($db->getError())){
            mensagem::setErro($Mensagems);
            return [];
        }

        

        return $values;
        
    }

    public static function getByEmpresaAndId($id_servico,$id_empresa){
        $db = new servico;
        
        $values = $db->addFilter("servico.id_empresa","=",$id_empresa)
                     ->addFilter("servico.id","=",$id_servico)
                     ->selectColumns(["servico.id","servico.nome","servico.tempo","servico.valor"]);
        
        if ($Mensagems = ($db->getError())){
            mensagem::setErro($Mensagems);
            return [];
        }
        
        return $values;
    }


    public static function getByUser($id = ""){
        $db = new servico;
        
        $values = $db->addJoin("INNER","servico_funcionario","servico_funcionario.id_servico","servico.id")
                    ->addJoin("INNER","funcionario","servico_funcionario.id_funcionario","funcionario.id")
                    ->addFilter("funcionario.id_usuario","=",$id)
                    ->selectColumns(["servico.id","funcionario.nome as funcionario_nome","servico.nome as ser_nome","servico.tempo","servico.valor"]);

        if ($Mensagems = ($db->getError())){
            mensagem::setErro($Mensagems);
            return [];
        }
                    
        return $values;
    }

    public static function getByServicoGrupoServico($id_grupo_servico = ""){
        $db = new servicoGrupoServico;
        
        $values = $db->addJoin("INNER","servico","servico_funcionario.id_servico","servico.id")
                    ->addFilter("servico_grupo_servico.id_grupo_servico","=",$id_grupo_servico)
                    ->selectColumns(["servico.id","servico.nome","servico.id_empresa","servico.tempo","servico.valor"]);

        if ($Mensagems = ($db->getError())){
            mensagem::setErro($Mensagems);
            return [];
        }
                    
        return $values;
    }

    public static function setServicoGrupoServico($id_servico,$id_grupo_servico){
        $db = new servicoGrupoServico;

        $result = $db->addFilter("id_grupo_servico","=",$id_grupo_servico)
                    ->addFilter("id_servico","=",$id_servico)
                    ->selectAll();

        if (!$result){
            $values = $db->getObject();

            $values->id_grupo_servico = $id_grupo_servico;
            $values->id_servico = $id_servico;

            if ($values)
                $retorno = $db->storeMutiPrimary($values);

            if ($retorno == true){
                mensagem::setSucesso(array("Agenda salvo com Sucesso"));
                return $db->getLastID();
            }
            else {
                $erros = ($db->getError());
                mensagem::setErro(array("Erro ao execultar a ação tente novamente"));
                mensagem::addErro($erros);
                return False;
            }
        }
        mensagem::setErro(array("Já existe vinculo entre esse grupo e serviço"));
        return True;
    }

    public static function setServicoFuncionario($id_servico,$id_funcionario){
        $db = new servicoFuncionario;

        $result = $db->addFilter("id_funcionario","=",$id_funcionario)
                    ->addFilter("id_servico","=",$id_servico)
                    ->selectAll();

        if (!$result){
            $values = $db->getObject();

            $values->id_funcionario = $id_funcionario;
            $values->id_servico = $id_servico;

            if ($values)
                $retorno = $db->storeMutiPrimary($values);

            if ($retorno == true){
                mensagem::setSucesso(array("Vinculados com Sucesso"));
                return $db->getLastID();
            }
            else {
                $erros = ($db->getError());
                mensagem::setErro(array("Erro ao execultar a ação tente novamente"));
                mensagem::addErro($erros);
                return False;
            }
        }
        mensagem::setErro(array("Já existe vinculo entre esse funcionario e serviço"));
        return True;
    }

    public static function set($nome,$valor,$tempo,$id_empresa="",$id=""){

        $db = new servico;
        
        $values = $db->getObject();

        $values->id = $id;
        $values->valor = $valor;
        $values->tempo = $tempo;
        $values->id_empresa = $id_empresa;
        $values->nome = $nome;

        if ($values)
            $retorno = $db->store($values);

        if ($retorno == true){
            mensagem::setSucesso(array("Serviço salvo com Sucesso"));
            return $db->getLastID();
        }
        else {
            $erros = ($db->getError());
            mensagem::setErro(array("Erro ao execultar a ação tente novamente"));
            mensagem::addErro($erros);
            return False;
        }
    }

    public static function delete($id){
        return (new servico)->delete($id);
    }

    public static function deleteAgendaServico($id_servico,$id_agenda){
        $db = new db("agenda_servico");

        $retorno =  $db->addFilter("agenda_servico.id_servico","=",$id_servico)->addFilter("agenda_servico.id_agenda","=",$id_agenda)->deleteByFilter();

        if ($retorno == true){
            mensagem::setSucesso(array("Excluido com Sucesso"));
            return True;
        }
        else {
            $erros = ($db->getError());
            mensagem::setErro(array("Erro ao execultar a ação tente novamente"));
            mensagem::addErro($erros);
            return False;
        }
    }

}