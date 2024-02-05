<?php 
namespace app\models\main;
use app\db\db;
use app\classes\mensagem;
use app\classes\functions;
use app\classes\modelAbstract;
use app\models\main\loginModel;
use stdClass;

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

        return $usuario;
    }

    public static function getByCpfCnpj($cpf_cnpj){

        $db = new db("usuario");

        $usuario = $db->selectByValues(["cpf_cnpj"],[$cpf_cnpj]);

        return $usuario;
    }

    public static function getByEmail($email){

        $db = new db("usuario");

        $usuario = $db->selectByValues(["email"],[$email]);

        return $usuario;
    }

    public static function getByTipoUsuario(){

        
    }

    public static function set($nome,$cpf_cnpj,$email,$telefone,$senha,$cd="",$tipo_usuario = 3,$id_empresa="null"){

        $db = new db("usuario");
    
        $values = $db->getObject();

        if ($values){
            $values->id = $cd;
            $values->id_empresa = $id_empresa;
            $values->cpf_cnpj = functions::onlynumber($cpf_cnpj);
            $values->nome = $nome;
            $values->email= $email;
            $values->senha = password_hash($senha,PASSWORD_DEFAULT);
            $values->telefone = functions::onlynumber($telefone);
            $values->tipo_usuario = $tipo_usuario;
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