<?php 
namespace app\models\main;

use app\classes\functions;
use app\db\db;
use app\classes\mensagem;
use app\classes\modelAbstract;
use stdClass;

class agendamentoModel{

    public static function get($cd = ""){
        return modelAbstract::get("agendamento",$cd);
    }

    public static function getEvents($dt_inicio,$dt_fim,$id_agenda,$status=99){
        $db = new db("agendamento");
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
        $db = new db("agendamento");
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

    public static function getAgendamentos(){
        $db = new db("agendamento");

        $result = $db->addJoin("INNER","usuario","usuario.id","agendamento.id_usuario")
                    ->addJoin("INNER","agenda","agenda.id","agendamento.id_agenda")
                    ->addJoin("INNER","funcionario","funcionario.id","agendamento.id_funcionario")
                    ->selectColumns(["agendamento.id","cpf_cnpj","nome as usu_nome","email","telefone","agenda.nome as age_nome","funcionario.nome as fun_nome","dt_ini","dt_fim","status"]);

         if ($db->getError()){
            return [];
        }
        
        return $result;
    }

    public static function set($id_agenda,$id_usuario,$id_cliente,$id_funcionario,$titulo,$dt_ini,$dt_fim,$cor,$obs,$total,$status,$id=""){

        $db = new db("agendamento");
        
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

    public static function delete($cd){
        modelAbstract::delete("agendamento",$cd);
    }

}