<?php 
namespace app\models\main;

use app\classes\functions;
use app\db\agendamento;
use app\classes\mensagem;
use app\classes\modelAbstract;
use stdClass;

class agendamentoModel{

    public static function get($id = ""){
        return (new agendamento)->get($id);
    }

    public static function getEvents($dt_inicio,$dt_fim,$id_agenda,$status=99){
        $db = new agendamento;
        $results = $db->addFilter("dt_ini",">=",$dt_inicio)
                      ->addFilter("dt_fim","<=",$dt_fim)
                      ->addFilter("id_agenda","=",intval($id_agenda))
                      ->addFilter("status","!=",$status)
                      ->selectAll();

        $retorn = [];

        if ($results){
            foreach ($results as $result){
                $retorn[] = [
                    'id' => functions::encrypt($result->id),
                    'title' => $result->titulo,
                    'color' => $result->cor,
                    'start' => $result->dt_ini,
                    'end' => $result->dt_fim,
                ];
            }
        }
        return $retorn;
    }

    public static function getEventsbyFuncionario($dt_inicio,$dt_fim,$id_agenda,$id_funcionario,$status=99){
        $db = new agendamento;
        $results = $db->addFilter("dt_ini",">=",$dt_inicio)
                      ->addFilter("dt_fim","<=",$dt_fim)
                      ->addFilter("id_agenda","=",intval($id_agenda))
                      ->addFilter("id_funcionario ","=",intval($id_funcionario))
                      ->addFilter("status","!=",$status)
                      ->selectAll();

        $retorn = [];

        $user = usuarioModel::getLogged();

        if ($results){
            foreach ($results as $result){
                if ($user->tipo_usuario != 3){
                    $retorn[] = [
                        'id' => functions::encrypt($result->id),
                        'title' => $result->titulo,
                        'color' => $result->cor,
                        'start' => $result->dt_ini,
                        'end' => $result->dt_fim,
                    ];
                }
                elseif ($user->id == $result->id_usuario){
                    $retorn[] = [
                        'id' => functions::encrypt($result->id),
                        'title' => $result->titulo,
                        'color' => $result->cor,
                        'start' => $result->dt_ini,
                        'end' => $result->dt_fim,
                    ];
                }
                else{
                    $retorn[] = [
                        'title' => "Outro agendamento",
                        'color' => "#9099ad",
                        'start' => $result->dt_ini,
                        'end' => $result->dt_fim,
                    ];
                }
            }
        }
        return $retorn;
    }

    public static function getAgendamentosByEmpresa($id_empresa){
        $db = new agendamento;

        $result = $db->addJoin("LEFT","usuario","usuario.id","agendamento.id_usuario")
                    ->addJoin("INNER","agenda","agenda.id","agendamento.id_agenda")
                    ->addJoin("LEFT","cliente","cliente.id","agendamento.id_cliente")
                    ->addJoin("INNER","funcionario","funcionario.id","agendamento.id_funcionario")
                    ->addFilter("agenda.id_empresa","=",$id_empresa)
                    ->selectColumns("agendamento.id","usuario.cpf_cnpj","cliente.nome as cli_nome","usuario.nome as usu_nome","usuario.email","usuario.telefone","agenda.nome as age_nome","funcionario.nome as fun_nome","dt_ini","dt_fim");

        if ($db->getError()){
            return [];
        }

        var_dump($result);
        
        return $result;
    }

    public static function getAgendamentosByUsuario($id_usuario){
        $db = new agendamento;

        $result = $db->addJoin("LEFT","usuario","usuario.id","agendamento.id_usuario")
                    ->addJoin("INNER","agenda","agenda.id","agendamento.id_agenda")
                    ->addJoin("INNER","funcionario","funcionario.id","agendamento.id_funcionario")
                    ->addFilter("usuario.id","=",$id_usuario)
                    ->selectColumns("agendamento.id","usuario.cpf_cnpj","usuario.nome as usu_nome","usuario.email","usuario.telefone","agenda.nome as age_nome","funcionario.nome as fun_nome","dt_ini","dt_fim");

        if ($db->getError()){
            return [];
        }

        var_dump("hhee");
        var_dump($result);
        
        return $result;
    }

    public static function set($id_agenda,$id_usuario,$id_cliente,$id_funcionario,$titulo,$dt_ini,$dt_fim,$cor,$obs,$total,$status,$id=""){

        $db = new agendamento;
        
        $values = new stdClass;

        $values->id = intval($id);
        $values->id_agenda = intval($id_agenda);
        if ($id_usuario)
            $values->id_usuario = intval($id_usuario);
        if ($id_cliente)
            $values->id_cliente = intval($id_cliente);
        $values->id_funcionario = intval($id_funcionario);
        $values->titulo = ucwords(strtolower($titulo));
        $values->dt_ini= functions::dateTimeBd($dt_ini);
        $values->dt_fim = functions::dateTimeBd($dt_fim);
        $values->cor = $cor;
        $values->obs = trim($obs);
        $values->total = $total;
        $values->status = intval($status);

        if ($values)
            $retorno = $db->store($values);

        if ($retorno == true)
            return $db->getLastID();
        else 
            return False;
        
    }

    public static function delete($id){
        return (new agendamento)->delete($id);
    }

}