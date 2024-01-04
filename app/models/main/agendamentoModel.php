<?php 
namespace app\models\main;
use app\db\db;
use app\classes\mensagem;
use app\classes\modelAbstract;

class agendaModel{

    public static function get($cd = ""){
        return modelAbstract::get("agendamento",$cd);
    }

    public static function getEvents($dt_inicio,$dt_fim){
        $db = new db("agendamento");
        $results = $db->selectAll(array(
            $db->getFilter("dt_inicio",">=",$dt_inicio),
            $db->getFilter("dt_fim","<=",$dt_fim))
        );

        $retorn = [];

        if ($results){
            foreach ($results as $result){
                $retorn[] = [
                    'id' => $result->cd_agenda,
                    'title' => $result->titulo,
                    'color' => $result->cor,
                    'start' => $result->dt_inicio,
                    'end' => $result->dt_fim,
                ];
            }
        }
        return json_encode($retorn);
    }

    public static function set($id_agenda,$id_usuario,$titulo,$dt_inicio,$dt_fim,$cor,$obs,$cd_agenda){

        $db = new db("agendamento");
        
        $values = $db->getObject();

        $values->cd_agenda = $cd_agenda;
        $values->id_agenda = $id_agenda;
        $values->id_usuario = $id_usuario;
        $values->titulo = $titulo;
        $values->dt_inicio= $dt_inicio;
        $values->dt_fim = $dt_fim;
        $values->cor = $cor;
        $values->obs= $obs;

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