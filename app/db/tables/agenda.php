<?php
namespace app\tables;

use app\db\abstract\tableClassAbstract;
use app\db\migrations\tableDb;
use app\db\migrations\columnDb;


class agenda extends tableClassAbstract {
    public const table = "agenda";

    public function __construct() {
        parent::__construct(self::table);
    }

    public static function table(){
        return (new tableDb("agenda",comment:"Tabela de agendas"))
                ->addColumn((new columnDb("id","INT"))->isPrimary()->setComment("ID agenda"))
                ->addColumn((new columnDb("id_empresa","INT"))->isNotNull()->isForeingKey(empresa::table())->setComment("ID da tabela empresa"))
                ->addColumn((new columnDb("nome","VARCHAR",250))->isNotNull()->setComment("Nome da agenda"))
                ->addColumn((new columnDb("codigo","VARCHAR",7))->isNotNull()->setComment("Codigo da agenda"));
    }
}