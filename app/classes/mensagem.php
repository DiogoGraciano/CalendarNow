<?php

namespace app\classes;
use app\classes\pagina;

class mensagem extends pagina{

    public function show($show=true,$localizacao="")
    {
        $this->getTemplate("../templates/mensagem_template.html");

        $mensagens = [];
    
        $mensagens[] = self::getErro();
        $mensagens[] = self::getSucesso();
        $mensagens[] = self::getMensagem();

        $i = 0;
      
        
        foreach ($mensagens as $mensagem){
            foreach ($mensagem as $text){
                if($text){
                    if ($i == 0){
                        $this->tpl->alert = "alert-danger";
                    }elseif ($i == 1){
                        $this->tpl->alert = "alert-success";
                    }else{
                        $this->tpl->alert = "alert-warning";
                    }   
                    $this->tpl->mensagem = $text;
                    $this->tpl->block("BLOCK_MENSAGEM");
                }
            }
            $i++;
        }
        
        if ($localizacao){
            $this->tpl->localizacao = $localizacao;
            $this->tpl->block("BLOCK_BOTAO");
        }

        self::setErro("");
        self::setSucesso("");
        self::setMensagem("");

        if ($show) 
            return $this->tpl->show();
        else 
            return $this->tpl->parse();
    }
    public static function getErro(){
        if (isset($_SESSION["Erros"]))
            return $_SESSION["Erros"];
         
        return [];
    }
    public static function setErro(...$erros){
        $_SESSION["Erros"] = $erros;
    }
    public static function addErro(...$erro){
        if (array_key_exists(0,$_SESSION["Erros"]))
            $_SESSION["Erros"] = array_merge($_SESSION["Erros"],$erro);
        else 
            self::setErro($erro);
    }
    public static function getMensagem(){
        if (isset($_SESSION["Mensagens"]))
            return $_SESSION["Mensagens"];
        
        return [];
    }
    public static function setMensagem(...$Mensagems){
        $_SESSION["Mensagens"] = $Mensagems;
    }
    public static function addMensagem(...$Mensagem){
        if (array_key_exists(0,$_SESSION["Mensagens"]))
            $_SESSION["Mensagens"] = array_merge($_SESSION["Mensagens"],$Mensagem);
        else 
            self::setMensagem($Mensagem);
    }
    public static function getSucesso(){
        if (isset($_SESSION["Sucessos"]))
            return $_SESSION["Sucessos"];
        else 
            return array();
    }
    public static function setSucesso(...$Sucessos){
        $_SESSION["Sucessos"] = $Sucessos;
    }
    public static function addSucesso(...$Sucesso){
        if (array_key_exists(0,$_SESSION["Sucessos"]))
            $_SESSION["Sucessos"] = array_merge($_SESSION["Sucessos"],$Sucesso);
        else 
            self::setSucesso($Sucesso);
    }
}
?>