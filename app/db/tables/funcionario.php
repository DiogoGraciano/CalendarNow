<?php
namespace app\db\tables;

use app\db\abstract\tableAbstract;
use app\db\migrations\tableDb;
use app\db\migrations\columnDb;

class funcionario extends tableAbstract {
    public const table = "funcionario";

    public function __construct() {
        parent::__construct(self::table);
    }

    public static function table(){
        return $funcionarioTb = new tableDb("funcionario", comment: "Tabela de funcionarios");
                $funcionarioTb->addColumn((new columnDb("id", "INT"))->isPrimary()->isNotNull()->setComment("ID do funcionario"))
                    ->addColumn((new columnDb("id_usuario", "INT"))->isNotNull()->isForeingKey(usuario::table())->setComment("ID da tabela usuario"))
                    ->addColumn((new columnDb("nome", "VARCHAR", 200))->isNotNull()->setComment("Nome do funcionario"))
                    ->addColumn((new columnDb("cpf_cnpj", "VARCHAR", 14))->isNotNull()->setComment("CPF ou CNPJ do funcionario"))
                    ->addColumn((new columnDb("email", "VARCHAR", 200))->isNotNull()->setComment("Email do funcionario"))
                    ->addColumn((new columnDb("telefone", "VARCHAR", 13))->isNotNull()->setComment("Telefone do funcionario"))
                    ->addColumn((new columnDb("hora_ini", "TIME"))->isNotNull()->setComment("Horario inicial de atendimento"))
                    ->addColumn((new columnDb("hora_fim", "TIME"))->isNotNull()->setComment("Horario final de atendimento"))
                    ->addColumn((new columnDb("hora_almoco_ini", "TIME"))->isNotNull()->setComment("Horario inicial do almoco"))
                    ->addColumn((new columnDb("hora_almoco_fim", "TIME"))->isNotNull()->setComment("Horario final do almoco"))
                    ->addColumn((new columnDb("dias", "VARCHAR", 27))->isNotNull()->setComment("Dias de trabalho: dom,seg,ter,qua,qui,sex,sab"));
    }
}