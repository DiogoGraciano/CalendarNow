<?php
namespace app\tables;

use app\db\abstract\tableClassAbstract;
use app\db\migrations\tableDb;
use app\db\migrations\columnDb;

class cliente extends tableClassAbstract {
    public const table = "cliente";

    public function __construct() {
        parent::__construct(self::table);
    }

    public static function table(){
        return (new tableDb("cliente",comment:"Tabela de clientes"))
                ->addColumn((new columnDb("id","INT"))->isPrimary()->setComment("ID do cliente"))
                ->addColumn((new columnDb("nome","VARCHAR",300))->isNotNull()->setComment("Nome do cliente"))
                ->addColumn((new columnDb("id_funcionario","INT"))->isForeingKey(funcionario::table())->setComment("id funcionario"));
    }
}