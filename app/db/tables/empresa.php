<?php
namespace app\tables;

use app\db\abstract\tableClassAbstract;
use app\db\migrations\tableDb;
use app\db\migrations\columnDb;

class empresa extends tableClassAbstract {
    public const table = "empresa";

    public function __construct() {
        parent::__construct(self::table);
    }

    public static function table(){
        return (new tableDb("empresa",comment:"Tabela de empresas"))
                ->addColumn((new columnDb("id","INT"))->isPrimary()->setComment("ID do cliente"))
                ->addColumn((new columnDb("nome","VARCHAR",300))->isNotNull()->isUnique()->setComment("Nome da empresa"))
                ->addColumn((new columnDb("email","VARCHAR",300))->isNotNull()->setComment("Email da empresa"))
                ->addColumn((new columnDb("telefone","VARCHAR",13))->isNotNull()->setComment("Telefone da empresa"))
                ->addColumn((new columnDb("cnpj","VARCHAR",14))->isNotNull()->setComment("CNPJ da empresa"))
                ->addColumn((new columnDb("razao","VARCHAR",300))->isNotNull()->isUnique()->setComment("Razão social da empresa"))
                ->addColumn((new columnDb("fantasia","VARCHAR",300))->isNotNull()->setComment("Nome fantasia da empresa"));
    }
}