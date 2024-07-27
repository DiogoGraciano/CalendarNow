<?php
namespace app\db\tables;

use app\db\abstract\tableAbstract;
use app\db\migrations\tableDb;
use app\db\migrations\columnDb;

class status extends tableAbstract {
    public const table = "status";

    public function __construct() {
        parent::__construct(self::table);
    }

    public static function table(){
        return (new tableDb("status",comment:"Tabela de status"))
                ->addColumn((new columnDb("id","INT"))->isPrimary()->setComment("ID agenda"))
                ->addColumn((new columnDb("nome","VARCHAR",250))->isNotNull()->setComment("Status do agendamento"));
    }
}