<?php
namespace app\db\tables;

use app\db\abstract\tableAbstract;
use app\db\migrations\tableDb;
use app\db\migrations\columnDb;
use app\db\db;

class pais extends tableAbstract {
    public const table = "pais";

    public function __construct() {
        parent::__construct(self::table);
    }

    public static function table(){
        return (new tableDb("pais",comment:"Tabela de paises"))
                ->addColumn((new columnDb("id","INT"))->isPrimary()->setComment("ID da pais"))
                ->addColumn((new columnDb("nome","VARCHAR",250))->isNotNull()->setComment("Nome do pais"))
                ->addColumn((new columnDb("nome_internacial","VARCHAR",250))->isNotNull()->setComment("nome internacial do pais"));
    }

    public static function seed(){
        $object = new db("pais");
        if(!$object->addLimit(1)->selectColumns("id")){
            $object->nome = "Brasil";
            $object->nome_internacial = "Brazil";
            $object->store();
        }
    }
}