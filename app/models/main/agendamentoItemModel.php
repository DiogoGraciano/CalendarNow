<?php 
namespace app\models\main;
use app\db\db;
use app\classes\mensagem;
use app\classes\modelAbstract;

class agendamentoItemModel{

    public static function getItens($id_agendamento){
        $db = new db("agendamento_item");

        $result = $db->addJoin("INNER","servico","servico.id","agendamento_item.id_servico")
                    ->addFilter("id_agendamento","=",$id_agendamento)
                    ->selectAll();

         if ($db->getError()){
            return [];
        }
        
        return $result;
    }

    public static function getItemByServico($id_agendamento,$id_servico){
        $db = new db("agendamento_item");

        $result = $db->addJoin("INNER","servico","servico.id","agendamento_item.id_servico")
                    ->addFilter("id_agendamento","=",$id_agendamento)
                    ->addFilter("id_servico","=",$id_servico)
                    ->addLimit(1)
                    ->selectAll();

         if ($db->getError()){
            return [];
        }
        
        if ($result)
            return $result[0];
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

        $db = new db("agendamento_item");
        
        $db->transaction();
       
        foreach($array_items as $item){
            
            $servico = servicoModel::get($item->id_servico);
            $retorno = false;
            $qtd = intval($item->qtd_item);
            $total = floatval($item->total_item);
            if ($servico && ($servico->valor*$qtd) == $total){
                $values = $db->getObject();
                $values->id = intval($item->id);
                $values->id_servico = $servico->id;
                $values->id_agendamento = intval($id_agendamento);
                $values->qtd_item = $qtd;
                $values->total_item = $total;
                $values->tempo_item = $item->tempo_item; 

                $retorno = $db->store($values);
                if ($retorno == false){
                    $db->rollback();
                    return $retorno;
                }
            }
        }
        $db->commit();
        return true;
    }

    public static function delete($cd){
        modelAbstract::delete("tb_agendamento",$cd);
    }

}