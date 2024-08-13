<?php
namespace app\db\tables;

use app\db\abstract\model;
use app\db\migrations\table;
use app\db\migrations\column;

class endereco extends model {
    public const table = "endereco";

    public function __construct() {
        parent::__construct(self::table,get_class($this));
    }

    public static function table(){
        return (new table(self::table,comment:"Tabela de endereços"))
            ->addColumn((new column("id","INTEGER"))->isPrimary()->setComment("ID do estado"))
            ->addColumn((new column("id_usuario","INTEGER"))->isForeingKey(usuario::table())->setComment("ID da tabela usuario"))
            ->addColumn((new column("id_empresa","INTEGER"))->isForeingKey(empresa::table())->setComment("ID da tabela empresa"))
            ->addColumn((new column("cep","VARCHAR",8))->isNotNull()->setComment("CEP"))
            ->addColumn((new column("id_cidade","INTEGER"))->isForeingKey(cidade::table())->setComment("ID da tabela estado"))
            ->addColumn((new column("id_estado","INTEGER"))->isForeingKey(estado::table())->setComment("ID da tabela cidade"))
            ->addColumn((new column("bairro","VARCHAR",300))->isNotNull()->setComment("Bairro"))
            ->addColumn((new column("rua","VARCHAR",300))->isNotNull()->setComment("Rua"))
            ->addColumn((new column("numero","INTEGER"))->isNotNull()->setComment("Numero"))
            ->addColumn((new column("complemento","VARCHAR",300))->setComment("Complemento do endereço"));
    }
} 