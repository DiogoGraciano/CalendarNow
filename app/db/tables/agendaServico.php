<?php
namespace app\tables;

use app\db\abstract\tableClassAbstract;

class agendaServico extends tableClassAbstract {
    public const table = "agenda_servico";

    public function __construct() {
        parent::__construct(self::table);
    }
}