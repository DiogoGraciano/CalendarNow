<?php

namespace app\classes;
use app\classes\pagina;

/**
 * Classe para gerenciar mensagens de erro, sucesso e informações para o usuário.
 * Esta classe estende a classe 'pagina' para herdar métodos relacionados ao template.
 */
class mensagem extends pagina{

    /**
     * Mostra as mensagens ao usuário.
     *
     * @param bool $show       Determina se deve exibir a mensagem imediatamente ou apenas retornar o HTML.
     * @param string $localizacao  Localização opcional para exibir uma mensagem com um botão específico.
     * @return mixed            Retorna o HTML da mensagem se $show for true ou o HTML parseado.
     */
    public function show(bool $show=true,string $localizacao="")
    {
        // Obtém o template das mensagens
        $this->getTemplate("../templates/mensagem_template.html");

        // Array para armazenar mensagens
        $mensagens = [];
    
        // Obtém as mensagens de erro, sucesso e informação
        $mensagens[] = self::getErro();
        $mensagens[] = self::getSucesso();
        $mensagens[] = self::getMensagem();

        $i = 0;
        
        // Loop através das mensagens e exibe-as com a classe de alerta apropriada
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
        
        // Adiciona botão se uma localização for fornecida
        if ($localizacao){
            $this->tpl->localizacao = $localizacao;
            $this->tpl->block("BLOCK_BOTAO");
        }

        // Limpa as sessões de mensagens após a exibição
        self::setErro("");
        self::setSucesso("");
        self::setMensagem("");

        // Retorna o HTML ou o HTML parseado com base no parâmetro $show
        if ($show) 
            return $this->tpl->show();
        else 
            return $this->tpl->parse();
    }

    /**
     * Obtém mensagens de erro da sessão.
     *
     * @return array    Retorna um array de mensagens de erro.
     */
    public static function getErro(){
        if (isset($_SESSION["Erros"]))
            return $_SESSION["Erros"];
         
        return [];
    }

    /**
     * Define mensagens de erro na sessão.
     *
     * @param mixed ...$erros   Mensagens de erro a serem definidas.
     */
    public static function setErro(...$erros){
        $_SESSION["Erros"] = $erros;
    }

    /**
     * Adiciona mensagens de erro à sessão.
     *
     * @param mixed ...$erro    Mensagens de erro a serem adicionadas.
     */
    public static function addErro(...$erro){
        if (array_key_exists(0,$_SESSION["Erros"]))
            $_SESSION["Erros"] = array_merge($_SESSION["Erros"],$erro);
        else 
            self::setErro($erro);
    }

    /**
     * Obtém mensagens informativas da sessão.
     *
     * @return array    Retorna um array de mensagens informativas.
     */
    public static function getMensagem(){
        if (isset($_SESSION["Mensagens"]))
            return $_SESSION["Mensagens"];
        
        return [];
    }

    /**
     * Define mensagens informativas na sessão.
     *
     * @param mixed ...$Mensagems   Mensagens informativas a serem definidas.
     */
    public static function setMensagem(...$Mensagems){
        $_SESSION["Mensagens"] = $Mensagems;
    }

    /**
     * Adiciona mensagens informativas à sessão.
     *
     * @param mixed ...$Mensagem    Mensagens informativas a serem adicionadas.
     */
    public static function addMensagem(...$Mensagem){
        if (array_key_exists(0,$_SESSION["Mensagens"]))
            $_SESSION["Mensagens"] = array_merge($_SESSION["Mensagens"],$Mensagem);
        else 
            self::setMensagem($Mensagem);
    }

    /**
     * Obtém mensagens de sucesso da sessão.
     *
     * @return array    Retorna um array de mensagens de sucesso.
     */
    public static function getSucesso(){
        if (isset($_SESSION["Sucessos"]))
            return $_SESSION["Sucessos"];
        else 
            return array();
    }

    /**
     * Define mensagens de sucesso na sessão.
     *
     * @param mixed ...$Sucessos   Mensagens de sucesso a serem definidas.
     */
    public static function setSucesso(...$Sucessos){
        $_SESSION["Sucessos"] = $Sucessos;
    }

    /**
     * Adiciona mensagens de sucesso à sessão.
     *
     * @param mixed ...$Sucesso    Mensagens de sucesso a serem adicionadas.
     */
    public static function addSucesso(...$Sucesso){
        if (array_key_exists(0,$_SESSION["Sucessos"]))
            $_SESSION["Sucessos"] = array_merge($_SESSION["Sucessos"],$Sucesso);
        else 
            self::setSucesso($Sucesso);
    }
}
?>
