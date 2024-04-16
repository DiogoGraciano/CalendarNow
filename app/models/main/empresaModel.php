<?php 
namespace app\models\main;

use app\classes\functions;
use app\db\empresa;
use app\classes\modelAbstract;
use app\classes\mensagem;

class empresaModel{

    public static function get($id){
        return (new empresa)->get($id);
    }

    public static function getByAgenda($id_agenda){
        $db = new empresa;
        $db->addJoin("INNER","agenda","agenda.id_empresa","empresa.id");

    }

    public static function set($nome,$cpf_cnpj,$email,$telefone,$razao,$fantasia,$id=""){

        $db = new empresa;

        $mensagens = [];

        if(!filter_var($nome)){
            $mensagens[] = "Nome da Empresa é obrigatorio";
        }

        if(!filter_var($razao)){
            $mensagens[] = "Razão Social é obrigatorio";
        }

        if(!filter_var($fantasia)){
            $mensagens[] = "Nome da Fantasia é obrigatorio";
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

        if($mensagens){
            mensagem::setErro(...$mensagens);
            return false;
        }

        $values = $db->getObject();

        $values->id = $id;
        $values->nome = trim($nome);
        $values->cnpj = functions::onlynumber($cpf_cnpj);
        $values->email = trim($email);
        $values->telefone = functions::onlynumber($telefone);
        $values->razao = trim($razao);
        $values->fantasia = trim($fantasia);
        $retorno = $db->store($values);
        
        if ($retorno == true){
            mensagem::setSucesso("Empresa salva com sucesso");
            return $db->getLastID();
        }else{
            mensagem::setErro("Erro ao cadastrar a empresa");
            return False;
        }
        
    }
    
    public static function delete($id){
       return (new empresa)->delete($id);
    }

}