<?php
namespace app\classes;
use app\classes\pagina;

/**
 * Classe footer é responsável por exibir o rodapé de uma página usando um template HTML.
 */
class footer extends pagina
{
    /**
     * Mostra o rodapé renderizado.
     */
    public function show()
    {
        $this->getTemplate("footer_template.html");
        $this->tpl->ano = date("Y");
        $this->tpl->show();
    }
}
