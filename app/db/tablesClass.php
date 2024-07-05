<?php
namespace app\db;

class agenda extends tableClassAbstract {
    public const table = "agenda";

    public function __construct() {
        parent::__construct(self::table);
    }
}

class agendaServico extends tableClassAbstract {
    public const table = "agenda_servico";

    public function __construct() {
        parent::__construct(self::table);
    }
}

class agendamento extends tableClassAbstract {
    public const table = "agendamento";

    public function __construct() {
        parent::__construct(self::table);
    }
}

class agendamentoItem extends tableClassAbstract {
    public const table = "agendamento_item";

    public function __construct() {
        parent::__construct(self::table);
    }
}

class agendaFuncionario extends tableClassAbstract {
    public const table = "agenda_funcionario";

    public function __construct() {
        parent::__construct(self::table);
    }
}

class agendaUsuario extends tableClassAbstract {
    public const table = "agenda_usuario";

    public function __construct() {
        parent::__construct(self::table);
    }
}

class cidade extends tableClassAbstract {
    public const table = "cidade";

    public function __construct() {
        parent::__construct(self::table);
    }
}

class cliente extends tableClassAbstract {
    public const table = "cliente";

    public function __construct() {
        parent::__construct(self::table);
    }
}

class config extends tableClassAbstract {
    public const table = "config";

    public function __construct() {
        parent::__construct(self::table);
    }
}

class empresa extends tableClassAbstract {
    public const table = "empresa";

    public function __construct() {
        parent::__construct(self::table);
    }
}

class endereco extends tableClassAbstract {
    public const table = "endereco";

    public function __construct() {
        parent::__construct(self::table);
    }
}

class estado extends tableClassAbstract {
    public const table = "estado";

    public function __construct() {
        parent::__construct(self::table);
    }
}

class funcionario extends tableClassAbstract {
    public const table = "funcionario";

    public function __construct() {
        parent::__construct(self::table);
    }
}

class funcionarioGrupoFuncionario extends tableClassAbstract{
    public const table = "funcionario_grupo_funcionario";

    public function __construct(){
        parent::__construct(self::table);
    }
}

class grupoFuncionario extends tableClassAbstract {
    public const table = "grupo_funcionario";

    public function __construct() {
        parent::__construct(self::table);
    }
}

class grupoServico extends tableClassAbstract {
    public const table = "grupo_servico";

    public function __construct() {
        parent::__construct(self::table);
    }
}

class servico extends tableClassAbstract {
    public const table = "servico";

    public function __construct() {
        parent::__construct(self::table);
    }
}

class status extends tableClassAbstract {
    public const table = "status";

    public function __construct() {
        parent::__construct(self::table);
    }
}

class servicoFuncionario extends tableClassAbstract {
    public const table = "servico_funcionario";

    public function __construct() {
        parent::__construct(self::table);
    }
}

class servicoGrupoServico extends tableClassAbstract {
    public const table = "servico_grupo_servico";

    public function __construct() {
        parent::__construct(self::table);
    }
}

class usuario extends tableClassAbstract {
    public const table = "usuario";

    public function __construct() {
        parent::__construct(self::table);
    }
}
?>
