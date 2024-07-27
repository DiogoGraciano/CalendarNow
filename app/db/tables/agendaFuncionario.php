<?php
namespace app\tables;

use app\db\abstract\tableAbstract;
use app\db\migrations\tableDb;
use app\db\migrations\columnDb;

class agendaFuncionario extends tableAbstract {
    public const table = "agenda_funcionario";

    public function __construct() {
        parent::__construct(self::table);
    }

    public static function table(){
        return (new tableDb("agenda_funcionario",comment:"Tabela de vinculo entre agendamentos e funcionarios"))
                ->addColumn((new columnDb("id_agenda","INT"))->isPrimary()->isForeingKey(agenda::table())->setComment("ID agenda"))
                ->addColumn((new columnDb("id_funcionario","INT"))->isPrimary()->isForeingKey(funcionario::table())->setComment("ID Funcionario"));
    }
}