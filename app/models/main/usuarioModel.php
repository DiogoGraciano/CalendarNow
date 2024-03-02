<?php 
namespace app\models\main;
use app\db\db;
use app\classes\mensagem;
use app\classes\functions;
use app\classes\modelAbstract;
use app\models\main\loginModel;

class usuarioModel{

    public static function get($cd){
        return modelAbstract::get("usuario",$cd);
    }

    public static function getLogged(){
        if (isset($_SESSION["user"]) && $_SESSION["user"])
            return $_SESSION["user"];

        loginModel::deslogar();
    }

    public static function getByCpfEmail($cpf_cnpj,$email){

        $db = new db("usuario");

        $usuario = $db->selectByValues(["cpf_cnpj","email"],[$cpf_cnpj,$email],True);

        if ($Mensagems = ($db->getError())){
            mensagem::setErro($Mensagems);
            return [];
        }

        return $usuario;
    }

    public static function getByCpfCnpj($cpf_cnpj){

        $db = new db("usuario");

        $usuario = $db->selectByValues(["cpf_cnpj"],[$cpf_cnpj]);

        if ($Mensagems = ($db->getError())){
            mensagem::setErro($Mensagems);
            return [];
        }

        return $usuario;
    }

    public static function getByEmail($email){

        $db = new db("usuario");

        $usuario = $db->selectByValues(["email"],[$email]);

        if ($Mensagems = ($db->getError())){
            mensagem::setErro($Mensagems);
            return [];
        }

        return $usuario;
    }

    public static function getByTipoUsuario($tipo_usuario){
        $db = new db("usuario");
        $usuarios = $db->addFilter("tipo_usuario","=",$tipo_usuario)->selectAll();

        if ($Mensagems = ($db->getError())){
            mensagem::setErro($Mensagems);
            return [];
        }

        return $usuarios;
    }

    public static function set($nome,$cpf_cnpj="",$email="",$telefone="",$senha="",$cd="",$tipo_usuario = 4,$id_empresa="null"){

        $db = new db("usuario");
    
        $values = $db->getObject();

        if ($values){
            $values->id = intval($cd);
            $values->id_empresa = intval($id_empresa);
            $values->cpf_cnpj = functions::onlynumber($cpf_cnpj);
            $values->nome = trim($nome);
            $values->email= trim($email);
            $values->senha = password_hash($senha,PASSWORD_DEFAULT);
            $values->telefone = functions::onlynumber($telefone);
            $values->tipo_usuario = intval($tipo_usuario);
            $retorno = $db->store($values);
        }
        if ($retorno == true){
            mensagem::setSucesso(array("Usuario salvo com Sucesso"));
            return $db->getLastID();
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