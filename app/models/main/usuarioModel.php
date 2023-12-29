<?php 
namespace app\models\main;
use app\db\db;
use app\classes\mensagem;
use app\classes\modelAbstract;
use app\models\main\enderecoModel;

class usuarioModel{

    public static function get($cd = ""){
        $usuario = modelAbstract::get("usuario",$cd);
        if ($usuario->id_endereco)
            $usuario->endereco = modelAbstract::get("endereco",$usuario->id_endereco);
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

    public static function set($nome,$cpf_cnpj,$email,$senha,$cep,$id_estado,$id_cidade,$rua,$numero,$complemento,$id_endereco,$cd="",$tipo_usuario = 3){

        $db = new db("usuario");
    
        $values = $db->getObject();

        if ($values){
            $values->id = $cd;
            $values->cpf_cnpj = $cpf_cnpj;
            $values->nome = $nome;
            $values->email= $email;
            $values->senha = $senha;
            $values->tipo_usuario = $tipo_usuario;
            $retorno = $db->store($values);
        }
        if ($retorno == true){
            $retorno = enderecoModel::set($cep,$id_estado,$id_cidade,$rua,$numero,$complemento,$id_endereco);
            if ($retorno == true){
                mensagem::setSucesso(array("Usuario salvo com Sucesso"));
                return True;
            }
            else {
                usuarioModel::delete($db->getlastId());
                mensagem::setErro(array("Erro ao execultar a ação tente novamente"));
                return False;
            }
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