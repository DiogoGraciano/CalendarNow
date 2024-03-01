<?php 
namespace app\models\main;

use app\classes\functions;
use app\db\db;
use app\classes\mensagem;
use app\classes\modelAbstract;

class agendamentoItemModel{

    public static function get($cd = ""){
        return modelAbstract::get("agendamento",$cd);
    }

    public static function getItens($id_agendamento){
        $db = new db("agendamento");

        $result = $db->addJoin("INNER","servico","servico.id","agendamento.id_servico")
                    ->addFilter("id_agendamento","=",$id_agendamento)
                    ->selectAll();

        if ($Mensagems = ($db->getError())){
            mensagem::setErro($Mensagems);
            return [];
        }
        
        return $result;
    }

    public static function set($qtd_item,$tempo_item,$total_item,$id_agendamento,$id_servico){

        $db = new db("agendamentoItem");
        
        $values = $db->getObject();

        $values->id_agendamento = $id_agendamento;
        $values->id_servico = $id_servico;
        $values->total_item = $total_item;
        $values->tempo_item = $tempo_item;
        $values->qtd_item = $qtd_item;

        if ($values)
            $retorno = $db->storeMutiPrimary($values);

        if ($retorno == true){
            return True;
        }
        else {
            $erros = ($db->getError());
            mensagem::setErro(array("Erro ao execultar a ação tente novamente"));
            mensagem::addErro($erros);
            return False;
        }
    }

    public static function setMultiple($array_items,$id_agendamento){

        $db = new db("agendamentoItem");
        
        $db->transaction();

        foreach($array_items as $item){
            $values = $db->getObject();
            $values->id_agendamento = $id_agendamento;
            $values->id_servico = $item->id_servico;
            $values->total_item = $item->total_item;
            $values->tempo_item = $item->tempo_item;
            $values->qtd_item = $item->qtd_item;

            $retorno = $db->store($values);

            if ($retorno == false){
                $db->rollback();
                $erros = ($db->getError());
                mensagem::setErro(array("Erro ao execultar a ação tente novamente"));
                mensagem::addErro($erros);
                return False;
            }
        }
        $db->commit();
        return true;
    }

    public static function delete($cd){
        modelAbstract::delete("tb_agendamento",$cd);
    }

}