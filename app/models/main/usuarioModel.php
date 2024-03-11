<?php 
namespace app\models\main;
use app\db\usuario;
use app\classes\functions;
use app\models\main\loginModel;

class usuarioModel{

    public static function get($id){
        return usuario::selectOne($id);
    }

    public static function getLogged(){
        if (isset($_SESSION["user"]) && $_SESSION["user"])
            return $_SESSION["user"];

        loginModel::deslogar();
    }

    public static function getByCpfEmail($cpf_cnpj,$email){

        $db = new usuario;

        $usuario = $db->selectByValues(["cpf_cnpj","email"],[$cpf_cnpj,$email],True);

        if ($db->getError()){
            return [];
        }

        return $usuario;
    }

    public static function getByCpfCnpj($cpf_cnpj){

        $db = new usuario;

        $usuario = $db->selectByValues(["cpf_cnpj"],[$cpf_cnpj]);

        if ($db->getError()){
            return [];
        }

        return $usuario;
    }

    public static function getByEmail($email){

        $db = new usuario;

        $usuario = $db->selectByValues(["email"],[$email]);

        if ($db->getError()){
            return [];
        }

        return $usuario;
    }

    public static function getByTipoUsuarioAgenda($tipo_usuario,$id_agenda){
        $db = new usuario;
        $usuarios = $db->addJoin("INNER","agendamento","usuario.id","agendamento.id_usuario")
                        ->addFilter("tipo_usuario","=",$tipo_usuario)
                        ->addFilter("agendamento.id_agenda","=",$id_agenda)
                        ->addFilter("usuario.tipo_usuario","=",$tipo_usuario)
                        ->addGroup("usuario.id")
                        ->selectColumns(['usuario.id','usuario.nome','usuario.cpf_cnpj','usuario.telefone','usuario.senha','usuario.email','usuario.tipo_usuario','usuario.id_empresa']);

        if ($db->getError()){
            return [];
        }

        return $usuarios;
    }

    public static function set($nome,$cpf_cnpj,$email,$telefone,$senha,$id,$tipo_usuario = 3,$id_empresa="null"){

        $db = new usuario;
    
        $values = $db->getObject();

        if ($values){
            $values->id = intval($id);
            $values->id_empresa = intval($id_empresa);
            $values->cpf_cnpj = functions::onlynumber($cpf_cnpj);
            $values->nome = trim($nome);
            $values->email= trim($email);
            $values->senha = password_hash($senha,PASSWORD_DEFAULT);
            $values->telefone = functions::onlynumber($telefone);
            $values->tipo_usuario = intval($tipo_usuario);
            $retorno = $db->store($values);
        }
        if ($retorno == true)
            return $db->getLastID();
        else 
            return False;
        
        
    }

    public static function delete($id){
        return usuario::delete($id);
    }

}