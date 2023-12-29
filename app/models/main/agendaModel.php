<?php 
namespace app\models\main;
use app\db\db;
use app\classes\mensagem;
use app\classes\modelAbstract;

class agendaModel{

    public static function get($cd = ""){
        return modelAbstract::get("tb_agendamento",$cd);
    }

    public static function getEvents($dt_inicio,$dt_fim){
        $db = new db("tb_agendamento");
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

    public static function set($cd_cliente,$cd_funcionario,$titulo,$dt_inicio,$dt_fim,$cor,$obs,$cd_agenda){

        $db = new db("tb_agendamento");

        if($cd_agenda && $cd_cliente && $cd_funcionario && $titulo && $dt_inicio && $dt_fim){
        
            $values = $db->getObject();

            $values->cd_agenda = $cd_agenda;
            $values->cd_cliente = $cd_cliente;
            $values->titulo = $titulo;
            $values->cd_funcionario = $cd_funcionario;
            $values->dt_inicio= $dt_inicio;
            $values->dt_fim = $dt_fim;
            $values->cor = $cor;
            $values->obs= $obs;

            if ($values)
                $retorno = $db->store($values);

            if ($retorno == true){
                mensagem::setSucesso(array("Atualizado com Sucesso"));
                return True;
            }
            else {
                $erros = ($db->getError());
                mensagem::setErro(array("Erro ao execultar a ação tente novamente"));
                mensagem::addErro($erros);
                return False;
            }

        }
        elseif(!$cd_agenda && $cd_cliente && $cd_funcionario && $titulo && $dt_inicio && $dt_fim){
            $values = $db->getObject();

            $values->cd_cliente = $cd_cliente;
            $values->cd_funcionario = $cd_funcionario;
            $values->titulo = $titulo;
            $values->dt_inicio= $dt_inicio;
            $values->dt_fim = $dt_fim;
            $values->cor = $cor;
            $values->obs= $obs;

            if ($values)
                $retorno = $db->store($values);

            if ($retorno == true){
                mensagem::setSucesso(array("Criado com Sucesso"));
                return True;
            }
            else {
                $erros = ($db->getError());
                mensagem::setErro(array("Erro ao execultar a ação tente novamente"));
                mensagem::addErro($erros);
                return False;
            }
        }
        else{
            mensagem::setErro(array("Erro ao excultar ação tente novamente"));
            return False;
        }
    }

    public static function delete($cd){
        modelAbstract::delete("tb_agendamento",$cd);
    }

}