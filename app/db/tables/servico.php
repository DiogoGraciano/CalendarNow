<?php
namespace app\tables;

use app\db\abstract\tableClassAbstract;
use app\db\migrations\tableDb;
use app\db\migrations\columnDb;
use app\db\db;


class servico extends tableClassAbstract {
    public const table = "servico";

    public function __construct() {
        parent::__construct(self::table);
    }

    public static function table(){
        return (new tableDb("servico", comment: "Tabela de serviços"))
                ->addColumn((new columnDb("id", "INT"))->isPrimary()->isNotNull()->setComment("ID do serviço"))
                ->addColumn((new columnDb("nome", "VARCHAR", 250))->isNotNull()->setComment("Nome do serviço"))
                ->addColumn((new columnDb("valor", "DECIMAL", "14,2"))->isNotNull()->setComment("Valor do serviço"))
                ->addColumn((new columnDb("tempo", "TIME"))->isNotNull()->setComment("Tempo do serviço"))
                ->addColumn((new columnDb("id_empresa", "INT"))->isNotNull()->setComment("ID da empresa"));
    }

    public static function seed(){
        $object = new db("status");
        if(!$object->addLimit(1)->selectColumns("id")){
                $object->nome = "Agendado";
                $object->store();
                $object->nome = "Finalizado";
                $object->store();
                $object->nome = "Não atendido";
                $object->store();
                $object->nome = "Cancelado";
                $object->store();
        }
    }
}