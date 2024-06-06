<?php
namespace app\models\main;

use app\db\agendamentoItem;
use app\classes\mensagem;
use app\classes\functions;
use Exception;

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
     * Obtém um Agendamento Item pelo ID.
     * 
     * @param int $id O ID do serviço.
     * @return object Retorna o objeto do Agendamento Item.
    */
    public static function get(string|null $value = null,string $columns = "id",int $limit = 1):object
    {
        return (new agendamentoItem)->get($value,$columns,$limit);
    }
    /**
     * Obtém os itens de um agendamento pelo ID do agendamento.
     * 
     * @param int $id_agendamento O ID do agendamento.
     * @return array Retorna um array com os itens do agendamento especificado.
     */
    public static function getItens(int $id_agendamento):array
    {
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
     * @param int $id_agendamento O ID do agendamento.
     * @param int $id_servico O ID do serviço.
     * @return object|bool Retorna os dados do item do agendamento ou false se não encontrado.
     */
    public static function getItemByServico(int $id_agendamento,int $id_servico):object|bool
    {
        $db = new agendamentoItem;

        $result = $db->addJoin("INNER","servico","servico.id","agendamento_item.id_servico")
                    ->addFilter("id_agendamento","=",$id_agendamento)
                    ->addFilter("id_servico","=",$id_servico)
                    ->addLimit(1)
                    ->selectColumns("agendamento_item.id","id_agendamento","id_servico","qtd_item","tempo_item","total_item","nome","valor","tempo","id_empresa");

        if ($db->getError()){
            return false;
        }
        
        if ($result)
            return $result[0];
        
        return false;
    }

    /**
     * Insere ou atualiza um item de agendamento.
     * 
     * @param int $qtd_item A quantidade do item.
     * @param string $tempo_item O tempo do item.
     * @param float $total_item O total do item.
     * @param string $id_agendamento O ID do agendamento.
     * @param string $id_servico O ID do serviço.
     * @param string $id O ID do item de agendamento (opcional).
     * @return int|bool Retorna true se a operação for bem-sucedida, caso contrário retorna false.
     */
    public static function set(int $qtd_item,int $id_agendamento,int $id_servico,int $id):int|bool
    {
        $db = new agendamentoItem;
        
        $values = $db->getObject();

        $servico = servicoModel::get($values->id_servico = $id_servico);

        if(!$servico->id){
            mensagem::setErro("Serviço não existe");
            return false;
        }

        if($values->qtd_item = $qtd_item <= 0){
            $mensagens[] = "Quantidade invalida";
        }

        if(!$values->total_item = ($servico->valor * $values->qtd_item)){
            $mensagens[] = "Total do item do agendamento invalido";
        }

        if(!$values->tempo_item = functions::multiplicarTempo($servico->tempo,$values->qtd_item)){
            $mensagens[] = "Tempo do item do agendamento invalido";
        }

        if(!$values->id_agendamento = $id_agendamento || !agendamentoModel::get($values->id_agendamento)->id){
            $mensagens[] = "Agendamento não existe";
        }

        if($values->id = $id && !self::get($values->id)){
            $mensagens[] = "Serviço não existe";
        }

        if($mensagens){
            mensagem::setErro(...$mensagens);
            return false;
        }

        $retorno = $db->store($values);

        if ($retorno == true){
            mensagem::setSucesso("Item salvo com sucesso");
            return $db->getLastID();
        }
        else {
            return false;
        }
    }

    /**
     * Exclui um item de agendamento pelo ID.
     * 
     * @param int $id O ID do item de agendamento a ser excluído.
     * @return bool Retorna true se a operação for bem-sucedida, caso contrário retorna false.
     */
    public static function delete(int $id){
        return (new agendamentoItem)->delete($id);
    }

}
