<?php 
namespace app\models\main;
use app\db\db;
use app\classes\mensagem;
use app\classes\functions;

class loginModel{

    public static function login($cpf_cnpj,$senha){
        $db = new db("usuario");
        $login = $db->selectByValues(array("cpf_cnpj"),array(functions::onlynumber($cpf_cnpj)),true);
        if ($login){
            if (password_verify($senha,$login[0]->senha)){
                $_SESSION["user"] = $login[0];
                $_SESSION["nome"] = $login[0]->nome;
                return True;
            }
        }
        mensagem::setErro(array("Usuario ou Senha invalido"));
        mensagem::addErro(array($db->getError()));
        return False;
        
    }
    public static function deslogar(){
        session_destroy();
    }

}