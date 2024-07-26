<?php
namespace app\tables;

use app\db\abstract\tableClassAbstract;
use app\db\migrations\tableDb;
use app\db\migrations\columnDb;

class funcionarioGrupoFuncionario extends tableClassAbstract {
    public const table = "funcionario_grupo_funcionario";

    public function __construct() {
        parent::__construct(self::table);
    }

    public static function table($recreate){
        $funcionarioGrupoFuncionarioTb = new tableDb("funcionario_grupo_funcionario", comment: "Tabela de relacionamento entre funcionarios e grupos de funcionarios");
        $funcionarioGrupoFuncionarioTb->addColumn((new columnDb("id_funcionario", "INT"))->isNotNull()->setComment("ID do funcionario")->isForeingKey($funcionarioTb))
                              ->addColumn((new columnDb("id_grupo_funcionario", "INT"))->isNotNull()->setComment("ID do grupo de funcionarios")->isForeingKey($grupoFuncionarioTb))
                              ->execute($recreate);
    }
}