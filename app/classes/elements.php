<?php
namespace app\classes;
use app\db\Db;

class elements extends pagina{

    private $options = []; 

    public function button($button_nome,$nm_input,$type_input="submit",$class_input="btn btn-primary w-100 pt-2 btn-block",$button_action="",$extra_input=""){

        $tpl= $this->getTemplate("elements_template.html");

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

    public function label($titulo){
        $tpl= $this->getTemplate("elements_template.html");

        $tpl->titulo = $titulo;
    
        $tpl->block("BLOCK_LABEL");  
        
        return $tpl->parse();
    }

    public function checkbox($nm_input,$nm_label,$required=false,$checked=false,$readonly=false,$value="on",$type_input="checkbox",$class_input="form-check-input",$extra_input=""){

        $tpl= $this->getTemplate("elements_template.html");

        $tpl->type_input = $type_input;
        $tpl->nm_input = $nm_input;
        $tpl->nm_label = $nm_label;
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
    
    public function input($nm_input,$nm_label,$vl_input="",$required=false,$readonly=false,$placeholder="",$type_input="text",$class_input="form-control",$extra_input=""){

        $tpl= $this->getTemplate("elements_template.html");

        $tpl->type_input = $type_input;
        $tpl->placeholder = $placeholder;
        $tpl->nm_label = $nm_label;
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

    public function textarea($nm_input,$nm_label,$vl_input,$required=false,$readonly=false,$placeholder="",$rows_input="",$cols_input="",$class_input="form-control",$extra_input=""){

        $tpl= $this->getTemplate("elements_template.html");

        $tpl->nm_label = $nm_label;
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

    public function select($nm_label,$nm_input,$vl_option="",$required=false,$class_input="form-select",$extra_input=""){

        $tpl= $this->getTemplate("elements_template.html");

        $tpl->nm_label = $nm_label;
        $tpl->nm_input = $nm_input;
        if($required && !$vl_option)
            $class_input .= " is-invalid";
        $tpl->class_input = $class_input;
        if ($required == true)
            $tpl->extra_input = $extra_input." required";

        foreach ($this->options as $option){
            $tpl->vl_option = $option->vl_option;
            if ($vl_option == $option->vl_option)
                $tpl->extra_option = "selected";
            $tpl->nm_option = $option->nm_option;
            $tpl->block("BLOCK_OPTION_SELECT");
            $tpl->extra_option = "";
        }
        
        $tpl->block("BLOCK_SELECT"); 

        $this->options = [];
        
        return $tpl->parse();
    }

    public function datalist($nm_label,$nm_input,$vl_option="",$required=false,$class_input="form-control",$extra_input=""){

        $tpl= $this->getTemplate("elements_template.html");

        $tpl->nm_label = $nm_label;
        $tpl->nm_input = $nm_input;
        if($required && !$vl_option)
            $class_input .= " is-invalid";
        $tpl->class_input = $class_input;
        if ($required == true)
            $tpl->extra_input = $extra_input." required";

        foreach ($this->options as $option){
            $tpl->vl_option = $option->vl_option;
            $tpl->nm_option = $option->nm_option;
            $tpl->extra_option = $option->extra_option;
            $tpl->block("BLOCK_OPTION_DATALIST");
        }
        
        $tpl->block("BLOCK_DATALIST");  

        $this->options = [];

        return $tpl->parse();
    }

    public function setOptions($tb,$coluna_vl,$coluna_nm){
        $db = new db($tb);
        $dados = $db->selectColumns(array($coluna_vl,$coluna_nm));

        foreach ($dados as $dado){
            $this->addObjectOption($dado->$coluna_vl,$dado->$coluna_nm);
        }
    }

    public function addObjectOption($vl_option,$nm_option,$extra_option=""){
        $this->options[] = json_decode('{"vl_option":"'.$vl_option.'","nm_option":"'.$nm_option.'","extra_option":"'.$extra_option.'"}');

        return $this;
    }


}


