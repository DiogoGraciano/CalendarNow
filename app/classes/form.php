<?php

namespace app\classes;
use app\classes\pagina;
use app\classes\mensagem;
use app\classes\functions;

class form extends pagina{

    private $tplform;
    
    public function __construct($titulo,$action)
    {
        $this->tplform = $this->getTemplate("form_template.html");
        $this->tplform->caminho = functions::getUrlBase();
        if ($titulo = "LOGO"){
            $this->tplform->block("BLOCK_LOGO");
        }else{
            $this->tplform->titulo = $titulo;
            $this->tplform->block("BLOCK_TITULO");
        }
        $mensagem = new mensagem;
        $this->tplform->mensagem = $mensagem->show(false);
        $this->tplform->action = $action;
        $this->tplform->block("BLOCK_START");
    }

    public function setinputs($input){
        $tpl= $this->getTemplate("inputs_template.html");
        $tpl->block_um_input = $input;
        $tpl->block("BLOCK_INPUT");
        $this->tplform->input = $tpl->parse();
        $this->tplform->block("BLOCK_INPUT");
    }

    public function setCustomInputs(array $inputs_custom){
        $tpl= $this->getTemplate("inputs_template.html");
        foreach ($inputs_custom as $custom){ 
            foreach ($custom as $cus){
                $tpl->tamanho = $cus->tamanho;
                $tpl->block_um_input = base64_decode($cus->input);
                $tpl->block("BLOCK_DIV");
            }
            $tpl->block("BLOCK_CUSTOM");
        }
        $this->tplform->input = $tpl->parse();
        $this->tplform->block("BLOCK_INPUT");
    }

    public function getCustomInput($tamanho,$input){
        return json_decode('{
            "tamanho":"'.$tamanho.'",
            "input":"'.base64_encode($input).'"
         }');
    }

    public function setDoisInputs($input,$input2){
        $tpl=$this->getTemplate("inputs_template.html");
        $tpl->block_dois_input = $input;
        $tpl->block_dois_input_dois = $input2;
        $tpl->block("BLOCK_INPUT_DOIS");
        $this->tplform->input = $tpl->parse();
        $this->tplform->block("BLOCK_INPUT");
    }

    public function setHidden($nome,$valor){
        $this->tplform->nome = $nome;
        $this->tplform->cd_value = $valor;
        $this->tplform->block("BLOCK_INPUT_HIDDEN");
    }

    public function setTresInputs($input,$input2,$input3){
        $tpl= $this->getTemplate("inputs_template.html");
        $tpl->block_tres_input = $input;
        $tpl->block_tres_input_dois = $input2;
        $tpl->block_tres_input_tres = $input3;
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
        $this->tplform->block("BLOCK_END");
        $this->tplform->show();
    }
}
