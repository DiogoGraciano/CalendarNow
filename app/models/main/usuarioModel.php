<?php 
namespace app\models\main;
use app\db\usuario;
use app\classes\functions;
use app\classes\mensagem;
use app\models\main\loginModel;

class usuarioModel{

    public static function get($id){
        return (new usuario)->get($id);
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
                        ->selectColumns('usuario.id','usuario.nome','usuario.cpf_cnpj','usuario.telefone','usuario.senha','usuario.email','usuario.tipo_usuario','usuario.id_empresa');

        if ($db->getError()){
            return [];
        }

        return $usuarios;
    }

    public static function set($nome,$cpf_cnpj,$email,$telefone,$senha,$id,$tipo_usuario = 3,$id_empresa="null"){

        $db = new usuario;

        $mensagens = [];

        if(!filter_var($nome)){
            $mensagens[] = "Nome da Empresa é obrigatorio";
        }

        if(!functions::validaCpfCnpj($cpf_cnpj)){
            $mensagens[] = "CPF/CNPJ invalido";
        }

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $mensagens[] = "E-mail Invalido";
        }

        if(!functions::validaTelefone($telefone)){
            $mensagens[] = "Telefone Invalido";
        }

        if($tipo_usuario < 0 || $tipo_usuario > 3){
            $mensagens[] = "Tipo de Usuario Invalido";
        }

        if(($tipo_usuario == 2 || $tipo_usuario == 1) && !$id_empresa){
            $mensagens[] = "Informar a empresa é obrigatorio para esse tipo de usuario";
        }

        if($mensagens){
            mensagem::setErro(...$mensagens);
            return false;
        }
    
        $values = $db->getObject();

        if ($values){
            $values->id = intval($id);
            $values->id_empresa = intval($id_empresa);
            $values->cpf_cnpj = functions::onlynumber($cpf_cnpj);
            $values->nome = trim($nome);
            $values->email= trim($email);
            $values->senha = password_hash(trim($senha),PASSWORD_DEFAULT);
            $values->telefone = functions::onlynumber($telefone);
            $values->tipo_usuario = intval($tipo_usuario);
            $retorno = $db->store($values);
        }
        if ($retorno == true){
            mensagem::setSucesso("Usuario salvo com sucesso");
            return $db->getLastID();
        }else{
            mensagem::setErro("Erro ao cadastrar usuario");
            return False;
        }
    }

    public static function delete($id){
        return (new usuario)->delete($id);
    }

}