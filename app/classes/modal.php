<?php

namespace app\classes;
use app\classes\pagina;
use app\classes\mensagem;
use stdClass;

/**
 * Classe para gerar e manipular modais.
 * Esta classe estende a classe 'pagina' para herdar métodos relacionados ao template.
 */
class modal extends pagina{

    /**
     * Template do modal modal.
     *
     * @var object
     */
    private $tplform;

    /**
     * Inputs customizados adicionados ao modal.
     *
     * @var array
     */
    private $inputs_custom = [];

    /**
     * Indica se o modal do modal deve ser submetido.
     *
     * @var boolean
     */
    private $submit_this_form;
    
    /**
     * Construtor da classe modal.
     *
     * @param string $action            Ação a ser executada ao submeter o modal.
     * @param string $nome              Nome do modal.
     * @param bool $submit_this_form Indica se o modal do modal deve ser submetido.
     */
    public function __construct(string $action,$nome = "modal",bool $submit_this_form = false)
    {
        $this->submit_this_form = $submit_this_form;
        $this->tplform = $this->getTemplate("modal_template.html");
        $mensagem = new mensagem;
        $this->tplform->mensagem = $mensagem->show(false);
        $this->tplform->nome_modal = $nome;
        if ($this->submit_this_form){
            $this->tplform->action = $action;
            $this->tplform->block("BLOCK_FORM");
        }
        else{
            $this->setHidden("actionConsulta".$nome,$action);
        }
        $this->tplform->block("BLOCK_START");
    }

    /**
     * Define um input para o modal.
     *
     * @param string $input Input HTML.
     * @param string $nome  Nome do input.
     */
    public function setinputs(string $input,string $nome=""){
        $tpl= $this->getTemplate("inputs_template.html");
        $tpl->block_um_input = $input;
        $tpl->nome = $nome;
        $tpl->block("BLOCK_INPUT");
        $this->tplform->input = $tpl->parse();
        $this->tplform->block("BLOCK_INPUT");
    }

     /**
     * Define inputs personalizados no modal.
     */
    public function setCustomInputs(){
        $tpl= $this->getTemplate("inputs_template.html");
        foreach ($this->inputs_custom as $custom){ 
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
    public function addCustomInput(int|string $tamanho,string $input,string $nome=""){

        $custom = new stdClass;
        $custom->tamanho = $tamanho;
        $custom->nome = $nome;
        $custom->input = base64_encode($input);

        $this->inputs_custom[] = $custom;
    }

    public function setDoisInputs(string $input,string $input2,array $nomes = ["",""]){
        $tpl=$this->getTemplate("inputs_template.html");
        $tpl->block_dois_input = $input;
        $tpl->nome_um = $nomes[0];
        $tpl->block_dois_input_dois = $input2;
        $tpl->nome_dois = $nomes[1];
        $tpl->block("BLOCK_INPUT_DOIS");
        $this->tplform->input = $tpl->parse();
        $this->tplform->block("BLOCK_INPUT");
    }

    public function setHidden(string $nome,$valor){
        $this->tplform->nome = $nome;
        $this->tplform->cd_value = $valor;
        $this->tplform->block("BLOCK_INPUT_HIDDEN");
    }

    /**
     * Define três inputs no modal.
     *
     * @param string $input Conteúdo do primeiro input.
     * @param string $input2 Conteúdo do segundo input.
     * @param string $input3 Conteúdo do terceiro input.
     * @param array $nomes Nomes para os inputs.
     */
    public function setTresInputs(string $input,string $input2,string $input3,array $nomes = array("","","")){
        $tpl= $this->getTemplate("inputs_template.html");
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
     * Define um botão no modal.
     *
     * @param string $button Conteúdo do botão.
     */
    public function setButton(string $button){
        $this->tplform->button = $button;
        $this->tplform->block("BLOCK_BUTTONS");
    }

    /**
     * Define um botão fora do form modal.
     *
     * @param string $button Conteúdo do botão.
     */
    public function setButtonNoForm(string $button){
        $this->tplform->button_no = $button;
        $this->tplform->block("BLOCK_BUTTONS_NO_FORM");
    }

    /**
     * Mostra o modal renderizado.
     */
    public function show(){
        if ($this->submit_this_form)
            $this->tplform->block("BLOCK_END_FORM");
        $this->tplform->block("BLOCK_END");
        $this->tplform->show();
    }
}
