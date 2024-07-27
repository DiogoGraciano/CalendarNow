<?php
namespace app\db\tables;

use app\db\abstract\tableAbstract;

class agendaServico extends tableAbstract {
    public const table = "agenda_servico";

    public function __construct() {
        parent::__construct(self::table);
    }
}