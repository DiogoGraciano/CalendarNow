<?php
namespace app\db\tables;

use app\db\abstract\tableAbstract;
use app\db\migrations\tableDb;
use app\db\migrations\columnDb;

class agendaUsuario extends tableAbstract {
    public const table = "agenda_usuario";

    public function __construct() {
        parent::__construct(self::table);
    }

    public static function table(){
        return (new tableDb("agenda_usuario",comment:"Tabela de vinculo entre agendamentos e usuarios"))
                ->addColumn((new columnDb("id_agenda","INT"))->isPrimary()->isForeingKey(agenda::table())->setComment("ID agenda"))
                ->addColumn((new columnDb("id_usuario","INT"))->isPrimary()->isForeingKey(usuario::table())->setComment("ID Usuario"));
    }

}