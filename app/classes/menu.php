<?php

namespace app\classes;
use app\classes\pagina;

/**
 * Classe para gerenciar e exibir menus.
 * Esta classe estende a classe 'pagina' para herdar métodos relacionados ao template.
 */
class menu extends pagina{

    /**
     * Array para armazenar os itens do menu.
     *
     * @var array
     */
    private $buttons = [];

     /**
     * Exibe o menu na página.
     *
     * @param string $titulo   Título do menu.
     */
    public function __construct()
    {
        $this->getTemplate("../templates/menu.html");
    }

    /**
     * Adiciona os botões ao menu.
     *
     * @return menu                  Retorna a instância do objeto menu para permitir chamadas encadeadas.
    */
    public function setLista(){
        $mensagem = new mensagem;
        $this->tpl->mensagem = $mensagem->show(false);
        foreach ($this->buttons as $objeto){
            $this->tpl->button = $objeto;
            $this->tpl->block("BLOCK_MENU");
        }  
        $this->buttons = [];

        return $this;
    }

    /**
     * Adiciona um novo botão.
     *
     * @param string $button         Botão para adicionar.
     * @return menu                  Retorna a instância do objeto menu para permitir chamadas encadeadas.
    */
    public function addButton($button){
        $this->buttons[] = $button;

        return $this;
    }

    /**
     * Exibe o menu na página.
     *
     * @param string $titulo   Título do menu.
    */
    public function show(){
        $this->tpl->show();
    }
   
}
