<?php

namespace app\view\layout;
use app\db\db;
use app\view\layout\abstract\pagina;

/**
 * Classe elements é responsável por gerar diversos elementos HTML, como botões, labels, checkboxes, inputs, textareas, selects e datalists.
 */
class elements extends pagina{

    /**
     * @var array $options Lista de opções para elementos select e datalist.
     */
    private $options = []; 

    /**
     * Gera um botão HTML.
     *
     * @param string $button_nome Nome do botão.
     * @param string $nm_input Nome do input.
     * @param string $type_input Tipo do input (default é "submit").
     * @param string $class_input Classes CSS do input.
     * @param string $button_action Ação do botão.
     * @param string $extra_input Atributos extras do input.
     * @return string HTML do botão.
     */
    public function button(string $button_nome,string $nm_input,string $type_input="submit",string $class_input="btn btn-primary w-100 pt-2 btn-block",string $button_action="",string $extra_input=""){

        $tpl= $this->getTemplate("elements.html");

        $tpl->type_input = $type_input;
        $tpl->nm_input = $nm_input;
        $tpl->class_input = $class_input;
        $tpl->button_action = $button_action;
        $tpl->extra_input = $extra_input;
        $tpl->nm_input = $nm_input;
        $tpl->button_nome = $button_nome;

        $tpl->block("BLOCK_BUTTON");   

        return $tpl->parse();
    }

    /**
     * Gera um botão HTML.
     *
     * @param string $button_nome Nome do botão.
     * @param string $nm_input Nome do input.
     * @param string $type_input Tipo do input (default é "submit").
     * @param string $class_input Classes CSS do input.
     * @param string $button_action Ação do botão.
     * @param string $extra_input Atributos extras do input.
     * @return string HTML do botão.
     */
    public function buttonMassation(string $button_nome,string $nm_input,string $massaction,string $class_input="btn btn-primary",string $extra_input=""){

        $tpl= $this->getTemplate("elements.html");

        $tpl->nm_input = $nm_input;
        $tpl->class_input = $class_input;
        $tpl->massaction = $massaction;
        $tpl->extra_input = $extra_input;
        $tpl->nm_input = $nm_input;
        $tpl->button_nome = $button_nome;

        $tpl->block("BLOCK_BUTTON_MASSATION");   

        return $tpl->parse();
    }

    /**
     * Gera uma label HTML.
     *
     * @param string $titulo Título da label.
     * @return string HTML da label.
     */
    public function label(string $titulo){
        $tpl= $this->getTemplate("elements.html");

        $tpl->titulo = $titulo;
    
        $tpl->block("BLOCK_LABEL");  
        
        return $tpl->parse();
    }

     /**
     * Gera um checkbox HTML.
     *
     * @param string $nm_input Nome do input.
     * @param string $nm_label Label do checkbox.
     * @param bool $required Indica se é obrigatório.
     * @param bool $checked Indica se está marcado.
     * @param bool $readonly Indica se é somente leitura.
     * @param string $value Valor do checkbox.
     * @param string $type_input Tipo do input (default é "checkbox").
     * @param string $class_input Classes CSS do input.
     * @param string $extra_input Atributos extras do input.
     * @return string HTML do checkbox.
     */
    public function checkbox(string $nm_input,string $nm_label="",bool $required=false,bool $checked=false,bool $readonly=false,$value="on",string $type_input="checkbox",string $class_input="form-check-input",string $extra_input=""){

        $tpl= $this->getTemplate("elements.html");

        $tpl->type_input = $type_input;
        $tpl->nm_input = $nm_input;
        if ($nm_label){
            $tpl->nm_label = $nm_label;
            $tpl->block("BLOCK_LABEL_CHECKBOX");  
        }
        
        $tpl->class_input = $class_input;
        if ($required)
            $extra_input = $extra_input.' required';
        if ($checked) 
            $extra_input = $extra_input.' checked';
        if ($readonly) 
            $extra_input = $extra_input.' onclick="return false;"';
        if ($value) 
            $extra_input = $extra_input.' value="'.$value.'"';

        $tpl->extra_input = $extra_input;

        $tpl->block("BLOCK_CHECKBOX");  
        
        return $tpl->parse();
    }
    
    /**
     * Gera um input HTML.
     *
     * @param string $nm_input Nome do input.
     * @param string $nm_label Label do input.
     * @param string $vl_input Valor do input.
     * @param bool $required Indica se é obrigatório.
     * @param bool $readonly Indica se é somente leitura.
     * @param string $placeholder Placeholder do input.
     * @param string $type_input Tipo do input (default é "text").
     * @param string $class_input Classes CSS do input.
     * @param string $extra_input Atributos extras do input.
     * @return string HTML do input.
     */
    public function input(string $nm_input,string $nm_label,$vl_input="",bool $required=false,bool $readonly=false,string $placeholder="",string $type_input="text",string $class_input="form-control",string $extra_input=""){

        $tpl= $this->getTemplate("elements.html");

        $tpl->type_input = $type_input;
        $tpl->placeholder = $placeholder;
        if ($nm_label){
            $tpl->nm_label = $nm_label;
            $tpl->block("BLOCK_LABEL_INPUT");  
        }
        $tpl->nm_input = $nm_input;

        if ($vl_input)
            $tpl->vl_input = $vl_input;
        elseif($required)
            $class_input .= " is-invalid";
        
        $tpl->class_input = $class_input;

        if ($required == true)
            $extra_input = $extra_input." required";
        if ($readonly == true)
            $extra_input = $extra_input." readonly";
        
        $tpl->extra_input = $extra_input;

        $tpl->block("BLOCK_INPUT");   

        return $tpl->parse();
    }

    /**
     * Gera um textarea HTML.
     *
     * @param string $nm_input Nome do input.
     * @param string $nm_label Label do textarea.
     * @param string $vl_input Valor do textarea.
     * @param bool $required Indica se é obrigatório.
     * @param bool $readonly Indica se é somente leitura.
     * @param string $placeholder Placeholder do textarea.
     * @param string $rows_input Número de linhas.
     * @param string $cols_input Número de colunas.
     * @param string $class_input Classes CSS do textarea.
     * @param string $extra_input Atributos extras do textarea.
     * @return string HTML do textarea.
     */
    public function textarea(string $nm_input,string $nm_label,$vl_input,bool $required=false,bool $readonly=false,string $placeholder="",$rows_input="",$cols_input="",string $class_input="form-control",string $extra_input=""){

        $tpl= $this->getTemplate("elements.html");

        if ($nm_label){
            $tpl->nm_label = $nm_label;
            $tpl->block("BLOCK_LABEL_TEXTAREA");  
        }
        $tpl->placeholder = $placeholder;
        $tpl->nm_input = $nm_input;
        $tpl->class_input = $class_input;
        if ($vl_input)
            $tpl->vl_input = $vl_input;
        elseif($required)
            $class_input .= " is-invalid";
        if ($required == true)
            $extra_input = $extra_input." required";
        if ($readonly == true)
            $extra_input = $extra_input." readonly";
        $tpl->extra_input = $extra_input;
        $tpl->rows_input = $rows_input;
        $tpl->cols_input = $cols_input;

        $tpl->block("BLOCK_TEXTAREA"); 
        
        return $tpl->parse();
    }

    /**
     * Gera um select HTML.
     *
     * @param string $nm_label Label do select.
     * @param string $nm_input Nome do input.
     * @param mixed $vl_option Valor da opção selecionada.
     * @param bool $required Indica se é obrigatório.
     * @param string $class_input Classes CSS do select.
     * @param string $extra_input Atributos extras do select.
     * @return string HTML do select.
     */
    public function select(string $nm_label,string $nm_input,$vl_option="",bool $required=false,string $class_input="form-select",string $extra_input=""){

        $tpl= $this->getTemplate("elements.html");

        if ($nm_label){
            $tpl->nm_label = $nm_label;
            $tpl->block("BLOCK_LABEL_SELECT");  
        }
        $tpl->nm_input = $nm_input;
        if($required && !$vl_option)
            $class_input .= " is-invalid";
        $tpl->class_input = $class_input;
        if ($required == true)
            $tpl->extra_input = $extra_input." required";

        foreach ($this->options as $option){
            if(isset($option->vl_option) && isset($option->nm_option)){
                $tpl->vl_option = $option->vl_option;
                if ($vl_option == $option->vl_option)
                    $tpl->extra_option = "selected";
                $tpl->nm_option = $option->nm_option;
                $tpl->block("BLOCK_OPTION_SELECT");
                $tpl->extra_option = "";
            }
        }
        
        $tpl->block("BLOCK_SELECT"); 

        $this->options = [];
        
        return $tpl->parse();
    }

    /**
     * Gera um datalist HTML.
     *
     * @param string $nm_label Label do datalist.
     * @param string $nm_input Nome do input.
     * @param mixed $vl_option Valor do datalist.
     * @param bool $required Indica se é obrigatório.
     * @param string $class_input Classes CSS do datalist.
     * @param string $extra_input Atributos extras do datalist.
     * @return string HTML do datalist.
     */
    public function datalist(string $nm_label,string $nm_input,$vl_option="",bool $required=false,string $class_input="form-control",string $extra_input=""){

        $tpl= $this->getTemplate("elements.html");

        if ($nm_label){
            $tpl->nm_label = $nm_label;
            $tpl->block("BLOCK_LABEL_DATALIST");  
        }
        $tpl->nm_input = $nm_input;
        $tpl->vl_input = $vl_option;
        if($required && !$vl_option)
            $class_input .= " is-invalid";
        $tpl->class_input = $class_input;
        if ($required == true)
            $tpl->extra_input = $extra_input." required";

        foreach ($this->options as $option){
            if(isset($option->vl_option) && isset($option->nm_option)){
                $tpl->vl_option = $option->vl_option;
                $tpl->nm_option = $option->nm_option;
                $tpl->extra_option = $option->extra_option;
                $tpl->block("BLOCK_OPTION_DATALIST");
            }
        }
        
        $tpl->block("BLOCK_DATALIST");  

        $this->options = [];

        return $tpl->parse();
    }

    /**
     * Define as opções para elementos select e datalist.
     *
     * @param db $class Classe da tabela do banco de dados.
     * @param string $coluna_vl Nome da coluna com o valor.
     * @param string $coluna_nm Nome da coluna com o nome.
     */
    public function setOptions(db $class,$coluna_vl,$coluna_nm){
        $db = $class;
        $dados = $db->selectColumns($coluna_vl,$coluna_nm);

        if ($dados){
            foreach ($dados as $dado){
                $this->addOption($dado->$coluna_vl,$dado->$coluna_nm);
            }
        }
    }

    /**
     * Adiciona uma opção para elementos select e datalist.
     *
     * @param mixed $vl_option Valor da opção.
     * @param string $nm_option Nome da opção.
     * @param string $extra_option Atributo extra da opção.
     * @return $this
     */
    public function addOption($vl_option,string $nm_option,string $extra_option=""){
        if (is_int($vl_option) || is_float($vl_option))
            $this->options[] = json_decode('{"vl_option":'.$vl_option.',"nm_option":"'.$nm_option.'","extra_option":"'.$extra_option.'"}');
        else
            $this->options[] = json_decode('{"vl_option":"'.$vl_option.'","nm_option":"'.$nm_option.'","extra_option":"'.$extra_option.'"}');

        return $this;
    }


}


