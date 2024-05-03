<?php 
namespace app\models\main;

use app\classes\functions;
use app\db\servico;
use app\db\servicoFuncionario;
use app\db\ServicoGrupoServico;
use app\db\agendaServico;
use app\classes\mensagem;
use app\classes\modelAbstract;

class servicoModel{

    public static function get($id){
        return (new servico)->get($id);
    }

    public static function getByEmpresa(int $id_empresa,string $nome = null,int $id_funcionario = null,int $id_grupo_servico = null){
        $db = new servico;

        $db->addFilter("servico.id_empresa","=",$id_empresa);

        if($nome){
            $db->addFilter("servico.nome","like","%".$nome."%");
        }

        if($id_funcionario){
            $db->addJoin("INNER","servico_funcionario","servico_funcionario.id_servico","servico.id");
            $db->addFilter("servico_funcionario.id_funcionario","=",$id_funcionario);
        }

        if($id_grupo_servico){
            $db->addJoin("INNER","servico_grupo_servico","servico_grupo_servico.id_servico","servico.id");
            $db->addFilter("servico_grupo_servico.id_grupo_servico","=",$id_grupo_servico);
        }

        $db->addGroup("servico.id");
        
        $values = $db->selectColumns("servico.id","servico.nome","servico.tempo","servico.valor");

        $valuesFinal = [];

        if ($Mensagems = ($db->getError())){
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
                return $db->getLastID();
            }
            return False;
        }
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
                return $db->getLastID();
            }
            return False;
        }
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
            return $db->getLastID();
        }
        return False;
    }

    public static function delete($id){
        return (new servico)->delete($id);
    }

    public static function deleteAgendaServico($id_servico,$id_agenda){
        $db = new agendaServico;

        $retorno =  $db->addFilter("agenda_servico.id_servico","=",$id_servico)->addFilter("agenda_servico.id_agenda","=",$id_agenda)->deleteByFilter();

        if ($retorno == true){
           
            return True;
        }
        return False;
    }

}