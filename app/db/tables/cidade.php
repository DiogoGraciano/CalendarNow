<?php
namespace app\tables;

use app\db\abstract\tableClassAbstract;
use app\db\migrations\tableDb;
use app\db\migrations\columnDb;

class cidade extends tableClassAbstract {
    public const table = "cidade";

    public function __construct() {
        parent::__construct(self::table);
    }

    public static function table(){
        return (new tableDb("cidade",comment:"Tabela de cidades"))
                ->addColumn((new columnDb("id","INT"))->isPrimary()->setComment("ID da cidade"))
                ->addColumn((new columnDb("nome","VARCHAR",120))->isNotNull()->setComment("Nome da cidade"))
                ->addColumn((new columnDb("uf","INT"))->isNotNull()->isForeingKey(estado::table())->setComment("id da Uf da cidade"))
                ->addColumn((new columnDb("ibge","INT"))->setComment("id do IBJE da cidade"));
    }
}