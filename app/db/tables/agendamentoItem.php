<?php
namespace app\tables;

use app\db\abstract\tableClassAbstract;
use app\db\migrations\tableDb;
use app\db\migrations\columnDb;

class agendamentoItem extends tableClassAbstract {
    public const table = "agendamento_item";

    public function __construct() {
        parent::__construct(self::table);
    }

    public static function table(){
        return (new tableDb("agendamento_item",comment:"Tabela de itens agendamentos"))
                ->addColumn((new columnDb("id","INT"))->isPrimary()->setComment("ID do item"))
                ->addColumn((new columnDb("id_agendamento","INT"))->isNotNull()->isForeingKey(agendamento::table())->setComment("ID agendamento"))
                ->addColumn((new columnDb("id_servico","INT"))->isNotNull()->isForeingKey(servico::table())->setComment("ID serviço"))
                ->addColumn((new columnDb("qtd_item","INT"))->isNotNull()->setComment("QTD de serviços"))
                ->addColumn((new columnDb("tempo_item","TIME"))->isNotNull()->setComment("Tempo total do serviço"))
                ->addColumn((new columnDb("total_item","DECIMAL","10,2"))->isNotNull()->setComment("Valor do serviço"));
    }
}