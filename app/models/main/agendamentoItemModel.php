<?php
namespace app\models\main;

use app\db\tables\agendamentoItem;
use app\helpers\mensagem;
use app\helpers\functions;

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

        $result = $db->addJoin("servico","servico.id","agendamento_item.id_servico")
                    ->addFilter("id_agendamento","=",$id_agendamento)
                    ->selectAll();
        
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

        $result = $db->addJoin("servico","servico.id","agendamento_item.id_servico")
                    ->addFilter("id_agendamento","=",$id_agendamento)
                    ->addFilter("id_servico","=",$id_servico)
                    ->addLimit(1)
                    ->selectColumns("agendamento_item.id","id_agendamento","id_servico","qtd_item","tempo_item","total_item","nome","valor","tempo","id_empresa");

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
    public static function set(int $qtd_item,int $id_agendamento,int $id_servico,NULL|int $id):int|bool
    {
        $values = new agendamentoItem;

        $mensagens = [];
        
        $servico = servicoModel::get($values->id_servico = $id_servico);

        if(!$servico->id){
            mensagem::setErro("Serviço não existe");
            return false;
        }

        if(!($values->qtd_item = $qtd_item) || $qtd_item <= 0){
            $mensagens[] = "Quantidade invalida";
        }

        if(!($values->total_item = ($servico->valor * $values->qtd_item))){
            $mensagens[] = "Total do item do agendamento invalido";
        }

        if(!($values->tempo_item = functions::multiplicarTempo($servico->tempo,$values->qtd_item))){
            $mensagens[] = "Tempo do item do agendamento invalido";
        }

        if(!($values->id_agendamento = $id_agendamento) || !agendamentoModel::get($values->id_agendamento)->id){
            $mensagens[] = "Agendamento não existe";
        }

        if(($values->id = $id) && !self::get($id)->id){
            $mensagens[] = "Item não existe";
        }

        if($mensagens){
            mensagem::setErro(...$mensagens);
            return false;
        }

        $retorno = $values->store();

        if ($retorno == true){
            mensagem::setSucesso("Item salvo com sucesso");
            return $values->id;
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

    /**
     * Exclui um item de agendamento pelo ID.
     * 
     * @param int $id_agendamento O ID do agendamento para o filtro do item a ser excluído.
     * @return bool Retorna true se a operação for bem-sucedida, caso contrário retorna false.
     */
    public static function deleteByIdAgendamento(int $id_agendamento){
        return (new agendamentoItem)->addFilter("id_agendamento","=",$id_agendamento)->deleteByFilter();
    }

}
