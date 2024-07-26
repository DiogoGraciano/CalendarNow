<?php
namespace app\tables;

use app\db\abstract\tableClassAbstract;
use app\db\migrations\tableDb;
use app\db\migrations\columnDb;

class usuario extends tableClassAbstract {
    public const table = "usuario";

    public function __construct() {
        parent::__construct(self::table);
    }

    public static function table(){
        return (new tableDb("usuario", comment: "Tabela de usuários"))
                ->addColumn((new columnDb("id", "INT"))->isPrimary()->isNotNull()->setComment("ID do usuário"))
                ->addColumn((new columnDb("nome", "VARCHAR", 500))->isNotNull()->setComment("Nome do usuário"))
                ->addColumn((new columnDb("cpf_cnpj", "VARCHAR", 14))->isUnique()->isNotNull()->setComment("CPF ou CNPJ do usuário"))
                ->addColumn((new columnDb("telefone", "VARCHAR", 11))->isNotNull()->setComment("Telefone do usuário"))
                ->addColumn((new columnDb("senha", "VARCHAR", 150))->isNotNull()->setComment("Senha do usuário"))
                ->addColumn((new columnDb("email", "VARCHAR", 200))->isUnique()->setComment("Email do usuário"))
                ->addColumn((new columnDb("tipo_usuario", "INT"))->isNotNull()->setComment("Tipo de usuário: 0 -> ADM, 1 -> empresa, 2 -> funcionario, 3 -> usuário, 4 -> cliente cadastrado"))
                ->addColumn((new columnDb("id_empresa", "INT"))->isForeingKey(empresa::table())->setComment("ID da empresa"));
    }
}