<?php

namespace app\helpers;
use app\view\layout\abstract\pagina;
use core\session;

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
    public function show(bool $show=true,string $localizacao=""):string|null
    {
        // Obtém o template das mensagens
        $this->getTemplate("mensagem.html");

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
    public static function getErro():array
    {
        return session::get("Erros")?:[];
    }

    /**
     * Define mensagens de erro na sessão.
     *
     * @param mixed ...$erros   Mensagens de erro a serem definidas.
     */
    public static function setErro(...$erros):void
    {
        session::set("Erros",$erros);
    }

    /**
     * Obtém mensagens informativas da sessão.
     *
     * @return array    Retorna um array de mensagens informativas.
     */
    public static function getMensagem():array
    {
        return session::get("Mensagens")?:[];
    }

    /**
     * Define mensagens informativas na sessão.
     *
     * @param mixed ...$Mensagems   Mensagens informativas a serem definidas.
     */
    public static function setMensagem(...$Mensagens):void
    {
        session::set("Mensagens",$Mensagens);
    }

    /**
     * Obtém mensagens de sucesso da sessão.
     *
     * @return array    Retorna um array de mensagens de sucesso.
     */
    public static function getSucesso():array
    {
        return session::get("Sucessos")?:[];
    }

    /**
     * Define mensagens de sucesso na sessão.
     *
     * @param mixed ...$Sucessos   Mensagens de sucesso a serem definidas.
     */
    public static function setSucesso(...$Sucessos):void
    {
        session::set("Sucessos",$Sucessos);
    }
}
?>
