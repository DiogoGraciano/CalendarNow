<?php
namespace app\tables;

use app\db\abstract\tableClassAbstract;
use app\db\migrations\tableDb;
use app\db\migrations\columnDb;

class usuarioBloqueio extends tableClassAbstract {
    public const table = "usuario_bloqueio";

    public function __construct() {
        parent::__construct(self::table);
    }

    public static function table($recreate){
        return (new tableDb("usuario_bloqueio", comment: "Tabela de usuários"))
                ->addColumn((new columnDb("id_usuario", "INT"))->isPrimary()->isForeingKey(usuario::table())->isNotNull()->setComment("ID do usuário"))
                ->addColumn((new columnDb("id_empresa", "INT"))->isPrimary()->isForeingKey(empresa::table())->setComment("ID da empresa"))
                ->execute();
    }
}