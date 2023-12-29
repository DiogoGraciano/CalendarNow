<?php
namespace app\classes;

class functions{
    public static function getRaiz(){
        return $_SERVER['DOCUMENT_ROOT'];
    }
    public static function getUrlBase(){
        return "http://".$_SERVER['HTTP_HOST']."/";
    }
    public static function utf8_urldecode($str) {
        return mb_convert_encoding(preg_replace("/%u([0-9a-f]{3,4})/i", "&#x\\1;", urldecode($str)),'UTF-8');
    }
}

?>