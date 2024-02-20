<?php

namespace app\classes;
use app\classes\pagina;
use app\classes\mensagem;
use stdClass;

class modal extends pagina{

    private $tplform;

    private $inputs_custom = [];

    private $submit_this_form;
    
    public function __construct($action,$nome = "modal",$submit_this_form = false)
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

    public function setinputs($input,$nome=""){
        $tpl= $this->getTemplate("inputs_template.html");
        $tpl->block_um_input = $input;
        $tpl->nome = $nome;
        $tpl->block("BLOCK_INPUT");
        $this->tplform->input = $tpl->parse();
        $this->tplform->block("BLOCK_INPUT");
    }

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

    public function addCustomInput($tamanho,$input,$nome=""){

        $custom = new stdClass;
        $custom->tamanho = $tamanho;
        $custom->nome = $nome;
        $custom->input = base64_encode($input);

        $this->inputs_custom[] = $custom;
    }

    public function setDoisInputs($input,$input2,array $nomes = array("","")){
        $tpl=$this->getTemplate("inputs_template.html");
        $tpl->block_dois_input = $input;
        $tpl->nome_um = $nomes[0];
        $tpl->block_dois_input_dois = $input2;
        $tpl->nome_dois = $nomes[1];
        $tpl->block("BLOCK_INPUT_DOIS");
        $this->tplform->input = $tpl->parse();
        $this->tplform->block("BLOCK_INPUT");
    }

    public function setHidden($nome,$valor){
        $this->tplform->nome = $nome;
        $this->tplform->cd_value = $valor;
        $this->tplform->block("BLOCK_INPUT_HIDDEN");
    }

    public function setTresInputs($input,$input2,$input3,array $nomes = array("","","")){
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

    public function setButton($button){
        $this->tplform->button = $button;
        $this->tplform->block("BLOCK_BUTTONS");
    }

    public function setButtonNoForm($button){
        $this->tplform->button_no = $button;
        $this->tplform->block("BLOCK_BUTTONS_NO_FORM");
    }

    public function show(){
        if ($this->submit_this_form)
            $this->tplform->block("BLOCK_END_FORM");
        $this->tplform->block("BLOCK_END");
        $this->tplform->show();
    }
}
