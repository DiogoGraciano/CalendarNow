<?php 
namespace app\models\main;
use app\db\endereco;
use app\classes\mensagem;
use app\classes\modelAbstract;
use app\classes\functions;
use app\classes\estadoModel;

class enderecoModel{

    public static function get($id = ""){
        return (new endereco)->get($id);
    }

    public static function getbyIdUsuario($id_usuario = ""){
        $db = new endereco;

        $values = $db->selectByValues(["id_usuario"],[$id_usuario],true);

        if ($db->getError()){
            return [];
        }

        return $values;
    }

    public static function set($cep,$id_estado,$id_cidade,$bairro,$rua,$numero,$complemento="null",$id = "",$id_usuario="null",$id_empresa="null"){

        $db = new endereco;

        $mensagens = [];

        if(!functions::validaCep($cep)){
            $mensagens[] = "CEP é invalido";
        }

        if(!estadoModel::get($id_estado)->id){
            $mensagens[] = "Estado é invalido";
        }

        if(!cidadeModel::get($id_cidade)->id){
            $mensagens[] = "Cidade é invalida";
        }

        if(!filter_var($bairro)){
            $mensagens[] = "Bairro é Invalido";
        }

        if(!filter_var($rua)){
            $mensagens[] = "Rua é Invalido";
        }

        if(!filter_var($numero)){
            $mensagens[] = "Numero é Invalido";
        }

        if(!filter_var($complemento)){
            $mensagens[] = "Complemento é Invalido";
        }

        if(!$id_usuario && !$id_empresa){
            $mensagens[] = "Usuario ou Empresa precisa ser informado para cadastro";
        }

        if($mensagens){
            mensagem::setErro(...$mensagens);
            return false;
        }

        $values = $db->getObject();

        $values->id = intval($id);
        $values->id_usuario = intval($id_usuario);
        $values->id_empresa = intval($id_empresa);
        $values->cep = (int)functions::onlynumber($cep);
        $values->id_estado = intval($id_estado);
        $values->id_cidade = intval($id_cidade);
        $values->bairro = trim($bairro);
        $values->rua = trim($rua);
        $values->numero = trim($numero);
        $values->complemento = trim($complemento);
        $retorno = $db->store($values);

        if ($retorno == true){
            mensagem::setSucesso("Endereço salva com sucesso");
            return $db->getLastID();
        }else{
            mensagem::setErro("Erro ao cadastrar a endereço");
            return False;
        }
        
    }
    
    public static function delete($id){
       return (new endereco)->delete($id);
    }

}