<?php
namespace app\db;
use app\db\configDB;
use app\classes\Logger;

class agenda extends db{
    public function __construct(){
        parent::__construct("agenda");
    }
}
class agendamento extends db{
    public function __construct(){
        parent::__construct("agendamento");
    }
}
class agendamentoItem extends db{
    public function __construct(){
        parent::__construct("agendamento_item");
    }
}
class agendaFuncionario extends db{
    public function __construct(){
        parent::__construct("agenda_funcionario");
    }
}
class agendaUsuario extends db{
    public function __construct(){
        parent::__construct("agenda_usuario");
    }
}
class cidade extends db{
    public function __construct(){
        parent::__construct("cidade");
    }
}
class cliente extends db{
    public function __construct(){
        parent::__construct("cliente");
    }
}
class empresa extends db{
    public function __construct(){
        parent::__construct("empresa");
    }
}
class endereco extends db{
    public function __construct(){
        parent::__construct("endereco");
    }
}
class estado extends db{
    public function __construct(){
        parent::__construct("estado");
    }
}
class funcionario extends db{
    public function __construct(){
        parent::__construct("funcionario");
    }
}
class funcionarioGrupoFuncionario extends db{
    public function __construct(){
        parent::__construct("funcionario_grupo_funcionario");
    }
}
class grupoFuncionario extends db{
    public function __construct(){
        parent::__construct("grupo_funcionario");
    }
}
class grupoServico extends db{
    public function __construct(){
        parent::__construct("grupo_servico");
    }
}
class servico extends db{
    public function __construct(){
        parent::__construct("servico");
    }
}
class servicoFuncionario extends db{
    public function __construct(){
        parent::__construct("servico_funcionario");
    }
}
class servicoGrupoServico extends db{
    public function __construct(){
        parent::__construct("servico_grupo_servico");
    }
}
class usuario extends db{
    public function __construct(){
        parent::__construct("usuario");
    }
}