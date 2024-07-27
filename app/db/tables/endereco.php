<?php
namespace app\db\tables;

use app\db\abstract\tableAbstract;
use app\db\migrations\tableDb;
use app\db\migrations\columnDb;

class endereco extends tableAbstract {
    public const table = "endereco";

    public function __construct() {
        parent::__construct(self::table);
    }

    public static function table(){
        return (new tableDb("endereco",comment:"Tabela de endereços"))
            ->addColumn((new columnDb("id","INT"))->isPrimary()->setComment("ID do estado"))
            ->addColumn((new columnDb("id_usuario","INT"))->isForeingKey(usuario::table())->setComment("ID da tabela usuario"))
            ->addColumn((new columnDb("id_empresa","INT"))->isForeingKey(empresa::table())->setComment("ID da tabela empresa"))
            ->addColumn((new columnDb("cep","VARCHAR",8))->isNotNull()->setComment("CEP"))
            ->addColumn((new columnDb("id_cidade","INT"))->isForeingKey(cidade::table())->setComment("ID da tabela estado"))
            ->addColumn((new columnDb("id_estado","INT"))->isForeingKey(estado::table())->setComment("ID da tabela cidade"))
            ->addColumn((new columnDb("bairro","VARCHAR",300))->isNotNull()->setComment("Bairro"))
            ->addColumn((new columnDb("rua","VARCHAR",300))->isNotNull()->setComment("Rua"))
            ->addColumn((new columnDb("numero","INT"))->isNotNull()->setComment("Numero"))
            ->addColumn((new columnDb("complemento","VARCHAR",300))->setComment("Complemento do endereço"));
    }
} 