<?php
namespace app\classes;
use app\classes\pagina;
use app\classes\mensagem;
use stdClass;

/**
 * Classe form é responsável por gerar um formulário dinâmico com base em um template HTML.
 */
class form extends pagina
{
    /**
     * @var object $tplform Objeto template para o formulário.
     */
    private $tplform;

    /**
     * @var array $inputs_custom Array para armazenar inputs personalizados.
     */
    private $inputs_custom = [];
    
    /**
     * Construtor da classe form.
     *
     * @param string $action URL para onde o formulário será enviado.
     */
    public function __construct(string $action)
    {
        $this->tplform = $this->getTemplate("form_template.html");
        $mensagem = new mensagem;
        $this->tplform->mensagem = $mensagem->show(false);
        $this->tplform->action = $action;
        $this->tplform->block("BLOCK_START");
    }

    /**
     * Define um único input no formulário.
     *
     * @param string $input Conteúdo do input.
     * @param string $nome Nome do input.
     */
    public function setinputs(string $input,string $nome = "")
    {
        $tpl = $this->getTemplate("inputs_template.html");
        $tpl->block_um_input = $input;
        $tpl->nome = $nome;
        $tpl->block("BLOCK_INPUT");
        $this->tplform->input = $tpl->parse();
        $this->tplform->block("BLOCK_INPUT");
    }

    /**
     * Define inputs personalizados no formulário.
     */
    public function setCustomInputs()
    {
        $tpl = $this->getTemplate("inputs_template.html");
        foreach ($this->inputs_custom as $custom) { 
            $tpl->tamanho = $custom->tamanho;
            $tpl->nome = $custom->nome;
            $tpl->block_um_input = base64_decode($custom->input);
            $tpl->block("BLOCK_DIV"); 
        }
        $tpl->block("BLOCK_CUSTOM");
        $this->tplform->input = $tpl->parse();
        $this->tplform->block("BLOCK_INPUT");
        $this->inputs_custom = [];
    }

    /**
     * Adiciona um input personalizado.
     *
     * @param int|string $tamanho Tamanho do input.
     * @param string $input Conteúdo do input (codificado em base64).
     * @param string $nome Nome do input.
     * @return $this
     */
    public function addCustomInput(int|string $tamanho,string $input,string $nome = "")
    {
        $custom = new stdClass;
        $custom->tamanho = $tamanho;
        $custom->nome = $nome;
        $custom->input = base64_encode($input);
        $this->inputs_custom[] = $custom;
        return $this;
    }

    /**
     * Define dois inputs no formulário.
     *
     * @param string $input Conteúdo do primeiro input.
     * @param string $input2 Conteúdo do segundo input.
     * @param array $nomes Nomes para os inputs.
     */
    public function setDoisInputs(string $input,string $input2, array $nomes = ["", ""])
    {
        $tpl = $this->getTemplate("inputs_template.html");
        $tpl->block_dois_input = $input;
        $tpl->nome_um = $nomes[0];
        $tpl->block_dois_input_dois = $input2;
        $tpl->nome_dois = $nomes[1];
        $tpl->block("BLOCK_INPUT_DOIS");
        $this->tplform->input = $tpl->parse();
        $this->tplform->block("BLOCK_INPUT");
    }

    /**
     * Define um input hidden no formulário.
     *
     * @param string $nome Nome do input hidden.
     * @param string $valor Valor do input hidden.
     */
    public function setHidden(string $nome,string $valor)
    {
        $this->tplform->nome = $nome;
        $this->tplform->cd_value = $valor;
        $this->tplform->block("BLOCK_INPUT_HIDDEN");
    }

    /**
     * Define três inputs no formulário.
     *
     * @param string $input Conteúdo do primeiro input.
     * @param string $input2 Conteúdo do segundo input.
     * @param string $input3 Conteúdo do terceiro input.
     * @param array $nomes Nomes para os inputs.
     */
    public function setTresInputs(string $input,string $input2,string $input3, array $nomes = ["", "", ""])
    {
        $tpl = $this->getTemplate("inputs_template.html");
        $tpl->block_tres_input = $input;
        $tpl->nome_um = $nomes[0];
        $tpl->block_tres_input_dois = $input2;
        $tpl->nome_dois = $nomes[1];
        $tpl->block_tres_input_tres = $input3;
        $tpl->nome_dois = $nomes[2];
        $tpl->block("BLOCK_INPUT_TRES");
        $this->tplform->input = $tpl->parse();
        $this->tplform->block("BLOCK_INPUT");
    }

    /**
     * Define um botão no formulário.
     *
     * @param string $button Conteúdo do botão.
     */
    public function setButton(string $button)
    {
        $this->tplform->button = $button;
        $this->tplform->block("BLOCK_BUTTONS");
    }

    /**
     * Define um botão fora do formulário.
     *
     * @param string $button Conteúdo do botão.
     */
    public function setButtonNoForm(string $button)
    {
        $this->tplform->button_no = $button;
        $this->tplform->block("BLOCK_BUTTONS_NO_FORM");
    }

    /**
     * Mostra o formulário renderizado.
     */
    public function show()
    {
        $this->tplform->block("BLOCK_END");
        $this->tplform->show();
    }
}
