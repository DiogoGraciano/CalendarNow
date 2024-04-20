<?php
namespace app\models\main;

use app\db\agendamentoItem;
use app\classes\mensagem;
use app\classes\modelAbstract;

/**
 * Classe agendamentoItemModel
 * 
 * Esta classe fornece métodos para interagir com os itens de agendamento.
 * Ela utiliza a classe agendamentoItem para realizar operações de consulta, inserção e exclusão no banco de dados.
 * 
 * @package app\models\main
 */
class agendamentoItemModel{

    /**
     * Obtém os itens de um agendamento pelo ID do agendamento.
     * 
     * @param string $id_agendamento O ID do agendamento.
     * @return array Retorna um array com os itens do agendamento especificado.
     */
    public static function getItens($id_agendamento){
        $db = new agendamentoItem;

        $result = $db->addJoin("INNER","servico","servico.id","agendamento_item.id_servico")
                    ->addFilter("id_agendamento","=",$id_agendamento)
                    ->selectAll();

        if ($db->getError()){
            return [];
        }
        
        return $result;
    }

    /**
     * Obtém um item de agendamento pelo ID do agendamento e ID do serviço.
     * 
     * @param string $id_agendamento O ID do agendamento.
     * @param string $id_servico O ID do serviço.
     * @return array|null Retorna os dados do item do agendamento ou null se não encontrado.
     */
    public static function getItemByServico($id_agendamento,$id_servico){
        $db = new agendamentoItem;

        $result = $db->addJoin("INNER","servico","servico.id","agendamento_item.id_servico")
                    ->addFilter("id_agendamento","=",$id_agendamento)
                    ->addFilter("id_servico","=",$id_servico)
                    ->addLimit(1)
                    ->selectColumns(["agendamento_item.id","id_agendamento","id_servico","qtd_item","tempo_item","total_item","nome","valor","tempo","id_empresa"]);

        if ($db->getError()){
            return [];
        }
        
        if ($result)
            return $result[0];
    }

    /**
     * Insere ou atualiza um item de agendamento.
     * 
     * @param int $qtd_item A quantidade do item.
     * @param float $tempo_item O tempo do item.
     * @param float $total_item O total do item.
     * @param string $id_agendamento O ID do agendamento.
     * @param string $id_servico O ID do serviço.
     * @param string $id O ID do item de agendamento (opcional).
     * @return bool Retorna true se a operação for bem-sucedida, caso contrário retorna false.
     */
    public static function set($qtd_item,$tempo_item,$total_item,$id_agendamento,$id_servico,$id=""){

        $db = new agendamentoItem;
        
        $values = $db->getObject();

        $values->id = $id;
        $values->id_agendamento = $id_agendamento;
        $values->id_servico = $id_servico;
        $values->total_item = $total_item;
        $values->tempo_item = $tempo_item;
        $values->qtd_item = $qtd_item;

        if ($values)
            $retorno = $db->store($values);

        if ($retorno == true){
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * Insere múltiplos itens de agendamento de uma vez.
     * 
     * @param array $array_items Um array contendo os itens de agendamento.
     * @param string $id_agendamento O ID do agendamento.
     * @return bool Retorna true se a operação for bem-sucedida, caso contrário retorna false.
     */
    public static function setMultiple($array_items,$id_agendamento){

        $db = new agendamentoItem;
        
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

    /**
     * Exclui um item de agendamento pelo ID.
     * 
     * @param string $id O ID do item de agendamento a ser excluído.
     * @return bool Retorna true se a operação for bem-sucedida, caso contrário retorna false.
     */
    public static function delete($id){
        return (new agendamentoItem)->delete($id);
    }

}
