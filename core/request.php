<?php

namespace core;

class request
{
    private readonly array $get;
    private readonly array $post;
    private readonly array $cookie;
    private readonly array $session;
    private readonly array $server;

    private function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->cookie = $_COOKIE;
        $this->session = $_SESSION;
        $this->server = $_SESSION;
    }

    public static function getValue($var){
        if (isset($_POST[$var]))
            return $_POST[$var];
        elseif (isset($_GET[$var]))
            return $_GET[$var];
        elseif (session::get($var))
            return session::get($var);
        elseif (isset($_COOKIE[$var]))
            return $_COOKIE[$var];
        elseif (isset($_SERVER[$var]))
            return $_SERVER[$var];
        else
            return null;
    }

    public function get(){
        return $this->get;
    }

    public function post(){
        return $this->post;
    }

    public function cookie(){
        return $this->cookie;
    }

    public function session(){
        return $this->session;
    }

    public function server(){
        return $this->server;
    }
}