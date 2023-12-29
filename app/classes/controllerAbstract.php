<?php
namespace app\classes;

abstract class controllerAbstract{

    public $url;

    public function __construct()
    {
        $this->url = "http://".$_SERVER['HTTP_HOST']."/";
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
        header("Location: ".$this->url.$caminho);
    }
}

?>