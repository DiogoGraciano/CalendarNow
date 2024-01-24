<?php
namespace app\classes;

abstract class controllerAbstract{

    public $url;

    public function __construct()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	    $domainName = $_SERVER['HTTP_HOST'];
	    $this->url = $protocol.$domainName."/";
    }

    public function setList($nome,$valor){
        $_SESSION[$nome] = $valor;
    }

    public function getList($nome){
        if (array_key_exists($nome,$_SESSION))
            return $_SESSION[$nome];
        else 
            return "";
    }

    public function getValue($var){
        if (isset($_POST[$var]))
            return $_POST[$var];
        elseif(isset($_GET[$var]))
            return $_GET[$var];
        elseif(isset($_SESSION[$var]))
            return $_SESSION[$var];
        else 
            return "";
    }

    public function go($caminho){
        echo '<meta http-equiv="refresh" content="0;url='.$this->url.$caminho.'">';
        exit;
    }
}

?>