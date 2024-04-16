<?php

namespace app\classes;
use app\classes\pagina;
use app\classes\functions;
use app\classes\mensagem;

/**
 * Classe para gerar a página de login.
 * Esta classe estende a classe 'pagina' para herdar métodos relacionados ao template.
 */
class login extends pagina{

    /**
     * Exibe o template da página de login.
    */
    public function show($usuario="",$senha=""){
        $this->getTemplate("../templates/login.html");
        $this->tpl->caminho = Functions::getUrlBase();
        $this->tpl->action_login = "login/action";
        $mensagem = new mensagem;
        $this->tpl->mensagem = $mensagem->show(false);
        $this->tpl->usuario = $usuario;
        $this->tpl->senha = $senha;
        $this->tpl->action_esqueci = "login/esqueci";
        $this->tpl->action_cadastro_empresa = "login/cadastro/".functions::encrypt(1);
        $this->tpl->action_cadastro_usuario = "login/cadastro/".functions::encrypt(3);
        $this->tpl->show();
    }
}