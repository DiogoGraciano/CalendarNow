<?php
namespace app\layout;
use app\layout\abstract\pagina;

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
        $this->getTemplate("footer.html");
        $this->tpl->ano = date("Y");
        $this->tpl->show();
    }
}
