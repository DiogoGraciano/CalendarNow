<?php 
namespace app\models\main;
use app\db\db;
use app\classes\mensagem;
use app\classes\functions;
use app\classes\modelAbstract;
use app\models\main\enderecoModel;
use app\models\main\loginModel;
use stdClass;

class usuarioModel{

    public static function get($cd,$tipo_usuario){
        $db = new db("usuario");

        $usuario = "";

        if ($tipo_usuario == 3 || $tipo_usuario == 2){
            $usuario = $db->selectByValues(["id"],[$cd],true)
            ->addJoin("INNER","endereco","endereco.id_usuario","usuario.id");
        }
        if ($tipo_usuario == 1 || $tipo_usuario == 0){
            $usuario = $db->selectByValues(["id"],[$cd],true)
            ->addJoin("INNER","empresa","usuario.id_empresa","empresa.id")
            ->addJoin("INNER","endereco","endereco.id_empresa","usuario.id");
        }

        if (!$usuario){
            $usuario = $db->getObject();
            $usuario = (object)array_merge((array)$usuario,(array)enderecoModel::get());
            return $usuario;
        }

        return $usuario[0];
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

    public static function set($nome,$cpf_cnpj,$email,$telefone,$senha,$cd="",$tipo_usuario = 3,$id_empresa="null"){

        $db = new db("usuario");
    
        $values = new stdClass;

        if ($values){
            $values->id = $cd;
            $values->id_empresa = $id_empresa;
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