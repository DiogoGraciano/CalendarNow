<?php
namespace app\db\tables;

use app\db\abstract\tableAbstract;
use app\db\migrations\tableDb;
use app\db\migrations\columnDb;

class servicoGrupoServico extends tableAbstract {
    public const table = "servico_grupo_servico";

    public function __construct() {
        parent::__construct(self::table);
    }

    public static function table(){
        return (new tableDb("servico_grupo_servico", comment: "Tabela de relacionamento entre grupos de serviços e serviços"))
                ->addColumn((new columnDb("id_grupo_servico", "INT"))->isPrimary()->isNotNull()->setComment("ID do grupo de serviço")->isForeingKey(grupoServico::table()))
                ->addColumn((new columnDb("id_servico", "INT"))->isPrimary()->isNotNull()->setComment("ID do serviço")->isForeingKey(servico::table()));
    }
}