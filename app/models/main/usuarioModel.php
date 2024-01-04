<?php 
namespace app\models\main;
use app\db\db;
use app\classes\mensagem;
use app\classes\functions;
use app\classes\modelAbstract;
use app\models\main\enderecoModel;

class usuarioModel{

    public static function get($cd = ""){
        $usuario = modelAbstract::get("usuario",$cd);
        if ($usuario->id)
            $usuario->endereco = enderecoModel::getByIdUsuario($usuario->id);
        else 
            $usuario->endereco =  enderecoModel::get();

        return $usuario;
    }

    public static function getByCpfEmail($cpf_cnpj,$email){

        $db = new db("usuario");

        $columns = ["cpf_cnpj","email"];
        $values = [$cpf_cnpj,$email];

        $usuario = $db->selectByValues($columns,$values,True);

        return $usuario;
    }

    public static function existsCpfCnpj($cpf_cnpj){

        $db = new db("usuario");

        $columns = ["cpf_cnpj"];
        $values = [$cpf_cnpj];

        $usuario = $db->selectByValues($columns,$values);

        return $usuario;
    }

    public static function existsEmail($email){

        $db = new db("usuario");

        $columns = ["email"];
        $values = [$email];

        $usuario = $db->selectByValues($columns,$values);

        return $usuario;
    }

    public static function set($nome,$cpf_cnpj,$email,$telefone,$senha,$cd="",$tipo_usuario = 3){

        $db = new db("usuario");
    
        $values = $db->getObject();

        if ($values){
            $values->id = $cd;
            $values->cpf_cnpj = (int)functions::onlynumber($cpf_cnpj);
            $values->nome = $nome;
            $values->email= $email;
            $values->senha = password_hash($senha,PASSWORD_DEFAULT);
            $values->telefone = (int)functions::onlynumber($telefone);
            $values->tipo_usuario = $tipo_usuario;
            $retorno = $db->store($values);
        }
        if ($retorno == true){
            mensagem::setSucesso(array("Usuario salvo com Sucesso"));
            return $db->lastid;
        }
        else {
            $Mensagems = ($db->getError());
            mensagem::setErro(array("Erro ao execultar a ação tente novamente"));
            mensagem::addErro($Mensagems);
            return False;
        }
        
    }

    public static function delete($cd=""){
        modelAbstract::delete("usuario",$cd);
    }

}