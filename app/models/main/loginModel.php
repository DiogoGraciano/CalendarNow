<?php
namespace app\models\main;

use app\classes\mensagem;
use app\classes\functions;
use app\db\usuario;

/**
 * Classe loginModel
 * 
 * Esta classe fornece métodos para autenticação de usuários.
 * Ela utiliza a classe usuario para realizar operações de consulta no banco de dados e a classe session para gerenciar a sessão do usuário.
 * 
 * @package app\models\main
 */
class loginModel{

    /**
     * Realiza o login do usuário com base no CPF/CNPJ e senha fornecidos.
     * 
     * @param string $cpf_cnpj O CPF/CNPJ do usuário.
     * @param string $senha A senha do usuário.
     * @return bool Retorna true se o login for bem-sucedido, caso contrário retorna false.
     */
    public static function login($cpf_cnpj, $senha):bool
    {
        $db = new usuario;
        $login = $db->selectByValues(["cpf_cnpj"], [functions::onlynumber($cpf_cnpj)], true);

        if ($login){
            if (password_verify($senha, $login[0]->senha)){
                $login[0]->senha = $senha;
                $_SESSION["user"] = $login[0];
                return true;
            }
        }

        mensagem::setErro("Usuário ou senha inválidos");
        return false;
    }

    /**
     * Desloga o usuário, destruindo a sessão.
     */
    public static function deslogar():bool
    {
        return session_destroy();
    }

}
