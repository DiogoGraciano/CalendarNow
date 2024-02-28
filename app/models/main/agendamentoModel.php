<?php 
namespace app\models\main;

use app\classes\functions;
use app\db\db;
use app\classes\mensagem;
use app\classes\modelAbstract;

class agendamentoModel{

    public static function get($cd = ""){
        return modelAbstract::get("agendamento",$cd);
    }

    public static function getEvents($dt_inicio,$dt_fim,$id_agenda){
        $db = new db("agendamento");
        $results = $db->addFilter("dt_inicio",">=",$dt_inicio)
                      ->addFilter("dt_fim","<=",$dt_fim)
                      ->addFilter("id_agenda","=",$id_agenda)
                      ->addFilter("status","!=",99)
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
        return json_encode($retorn);
    }

    public static function getAgendamentos(){
        $db = new db("agendamento");

        $result = $db->addJoin("INNER","usuario","usuario.id","agendamento.id_usuario")
                    ->addJoin("INNER","agenda","agenda.id","agendamento.id_agenda")
                    ->addJoin("INNER","funcionario","funcionario.id","agendamento.id_funcionario")
                    ->selectColumns(["agendamento.id","cpf_cnpj","nome as usu_nome","email","telefone","agenda.nome as age_nome","funcionario.nome as fun_nome","dt_ini","dt_fim","status"]);

        if ($Mensagems = ($db->getError())){
            mensagem::setErro($Mensagems);
            return [];
        }
        
        return $result;
    }

    public static function set($id_agenda,$id_usuario,$id_funcionario,$titulo,$dt_ini,$dt_fim,$cor,$obs,$total,$id=""){

        $db = new db("agendamento");
        
        $values = $db->getObject();

        $values->id = $id;
        $values->id_agenda = $id_agenda;
        $values->id_usuario = $id_usuario;
        $values->id_usuario = $id_funcionario;
        $values->titulo = $titulo;
        $values->dt_ini= $dt_ini;
        $values->dt_fim = $dt_fim;
        $values->cor = $cor;
        $values->obs= $obs;
        $values->total = $total;

        if ($values)
            $retorno = $db->store($values);

        if ($retorno == true){
            mensagem::setSucesso(array("Agendamento salvo com Sucesso"));
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
        modelAbstract::delete("tb_agendamento",$cd);
    }

}