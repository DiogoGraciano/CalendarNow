<?php

namespace app\classes;
use app\classes\pagina;
use app\classes\functions;
use app\classes\mensagem;

class login extends pagina{

    public function show($usuario="",$senha=""){
        $this->getTemplate("../templates/login.html");
        $this->tpl->caminho = Functions::getUrlBase();
        $this->tpl->action_login = "login/action";
        $mensagem = new mensagem;
        $this->tpl->mensagem = $mensagem->show(false);
        $this->tpl->usuario = $usuario;
        $this->tpl->senha = $senha;
        $this->tpl->action_esqueci = "login/esqueci";
        $this->tpl->action_cadastro = "login/cadastro";
        $this->tpl->show();
    }
}