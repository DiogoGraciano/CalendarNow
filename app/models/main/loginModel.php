<?php 
namespace app\models\main;
use app\classes\mensagem;
use app\classes\functions;
use app\db\usuario;

class loginModel{

    public static function login($cpf_cnpj,$senha){
        $db = new usuario;
        $login = $db->selectByValues(array("cpf_cnpj"),array(functions::onlynumber($cpf_cnpj)),true);
        if ($login){
            if (password_verify($senha,$login[0]->senha)){
                $login[0]->senha = $senha;
                $_SESSION["user"] = $login[0];
                return True;
            }
        }
        mensagem::setErro("Usuario ou Senha invalido");
        return False;
    }
    public static function deslogar(){
        session_destroy();
    }

}