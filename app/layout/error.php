<?php
namespace app\layout;
use app\layout\abstract\pagina;

/**
 * Classe footer é responsável por exibir o rodapé de uma página usando um template HTML.
 */
class error extends pagina
{
    /**
     * Mostra o rodapé renderizado.
     * 
     * @param int $code codigo de erro html
     * @param string $message mensagem do erro html
     */
    public function show(int $code = 404,string $message="A Pagina que está procurando não existe")
    {
        $this->getTemplate("error.html");
        $this->tpl->code = $code;
        $this->tpl->message = $message;
        $this->tpl->show();
    }
}
