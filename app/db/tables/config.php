<?php
namespace app\tables;

use app\db\abstract\tableClassAbstract;
use app\db\migrations\tableDb;
use app\db\migrations\columnDb;

class config extends tableClassAbstract {
    public const table = "config";

    public function __construct() {
        parent::__construct(self::table);
    }

    public static function table(){
        return (new tableDb("config",comment:"Tabela de configurações"))
                ->addColumn((new columnDb("id","INT"))->isPrimary()->setComment("ID Config"))
                ->addColumn((new columnDb("id_empresa","INT"))->isNotNull()->isForeingKey(empresa::table(),"id")->setComment("ID da tabela empresa"))
                ->addColumn((new columnDb("identificador","VARCHAR",30))->isNotNull()->isUnique()->setComment("Identificador da configuração"))
                ->addColumn((new columnDb("configuracao","BLOB"))->isNotNull()->setComment("Configuração"));
    }
}