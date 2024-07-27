<?php
namespace app\tables;

use app\db\abstract\tableAbstract;
use app\db\migrations\tableDb;
use app\db\migrations\columnDb;

class agendamento extends tableAbstract {
    public const table = "agendamento";

    public function __construct() {
        parent::__construct(self::table);
    }

    public static function table(){
        return (new tableDb("agendamento",comment:"Tabela de agendamentos"))
                ->addColumn((new columnDb("id","INT"))->isPrimary()->setComment("ID agendamento"))
                ->addColumn((new columnDb("id_agenda","INT"))->isNotNull()->isForeingKey(agenda::table())->setComment("ID da tabela agenda"))
                ->addColumn((new columnDb("id_usuario","INT"))->isForeingKey(usuario::table())->setComment("ID da tabela usuario"))
                ->addColumn((new columnDb("id_cliente","INT"))->isForeingKey(cliente::table())->setComment("ID da tabela cliente"))
                ->addColumn((new columnDb("id_funcionario","INT"))->isForeingKey(funcionario::table())->setComment("ID da tabela funcionario"))
                ->addColumn((new columnDb("titulo","VARCHAR",150))->isNotNull()->setComment("titulo do agendamento"))
                ->addColumn((new columnDb("dt_ini","DATETIME"))->isNotNull()->setComment("Data inicial de agendamento"))
                ->addColumn((new columnDb("dt_fim","DATETIME"))->isNotNull()->setComment("Data final de agendamento"))
                ->addColumn((new columnDb("cor","VARCHAR",7))->setDefaut("#4267b2")->isNotNull()->setComment("Cor do agendamento"))
                ->addColumn((new columnDb("total","DECIMAL","10,2"))->isNotNull()->setComment("Total do agendamento"))
                ->addColumn((new columnDb("id_status","INT"))->isForeingKey(status::table())->isNotNull()->setComment("id do Status do agendamento"))
                ->addColumn((new columnDb("obs","VARCHAR",400))->setComment("Observações do agendamento"))
                ->addIndex("getEventsbyFuncionario",["dt_ini","dt_fim","id_agenda","id_funcionario"]);
    }
}