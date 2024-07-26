<?php
namespace app\tables;

use app\db\abstract\tableClassAbstract;
use app\db\migrations\tableDb;
use app\db\migrations\columnDb;

class grupoServico extends tableClassAbstract {
    public const table = "grupo_servico";

    public function __construct() {
        parent::__construct(self::table);
    }

    public static function table(){
        return (new tableDb("grupo_servico", comment: "Tabela de grupos de serviços"))
                ->addColumn((new columnDb("id", "INT"))->isPrimary()->isNotNull()->setComment("ID do grupo de serviços"))
                ->addColumn((new columnDb("id_empresa", "INT"))->isForeingKey(empresa::table())->isNotNull()->setComment("ID da empresa"))
                ->addColumn((new columnDb("nome", "VARCHAR", 250))->isNotNull()->setComment("Nome do grupo de serviços"));
    }
}