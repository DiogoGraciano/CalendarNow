<?php
namespace app\db\tables;

use app\db\abstract\tableAbstract;
use app\db\migrations\tableDb;
use app\db\migrations\columnDb;

class servicoFuncionario extends tableAbstract {
    public const table = "servico_funcionario";

    public function __construct() {
        parent::__construct(self::table);
    }

    public static function table(){
        return (new tableDb("servico_funcionario", comment: "Tabela de relacionamento entre serviços e funcionários"))
                ->addColumn((new columnDb("id_funcionario", "INT"))->isPrimary()->isNotNull()->setComment("ID do funcionário")->isForeingKey(funcionario::table()))
                ->addColumn((new columnDb("id_servico", "INT"))->isPrimary()->isNotNull()->setComment("ID do serviço")->isForeingKey(servico::table()));
    }
}