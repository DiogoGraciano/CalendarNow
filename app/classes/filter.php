<?php
namespace app\classes;
use app\classes\pagina;

/**
 * Classe filter é responsável por gerar um formulário de filtro dinâmico com base em um template HTML.
 */
class filter extends pagina
{
    /**
     * @var object $tplform Objeto template para o formulário de filtro.
     */
    private $tplform;
    
    /**
     * Construtor da classe filter.
     *
     * @param string $action URL para onde o formulário será enviado.
     */
    public function __construct($action)
    {
        $this->tplform = $this->getTemplate("filter_template.html");
        $this->tplform->action = $action;
    }

    /**
     * Adiciona uma nova linha ao formulário.
     *
     * @return $this
     */
    public function addLinha()
    {
        $this->tplform->block("BLOCK_LINHA_FILTER");
        
        return $this;
    }

    /**
     * Adiciona um campo de filtro ao formulário.
     *
     * @param string $tamanho Tamanho do campo (por exemplo, "col-md-6").
     * @param string $input Nome do campo de filtro.
     * @return $this
     */
    public function addFilter($tamanho, $input)
    {
        $this->tplform->tamanho = $tamanho;
        $this->tplform->filter = $input;
        $this->tplform->block("BLOCK_INPUT");
        $this->tplform->block("BLOCK_FILTER");

        return $this;
    }

    /**
     * Adiciona um botão ao formulário.
     *
     * @param string $button Texto ou HTML do botão.
     * @return $this
     */
    public function addbutton($button)
    {
        $this->tplform->button = $button;
        $this->tplform->block("BLOCK_BUTTON");

        return $this;
    }

    /**
     * Mostra o formulário de filtro renderizado.
     */
    public function show()
    {
        $this->addLinha();
        $this->tplform->show();
    }
}
