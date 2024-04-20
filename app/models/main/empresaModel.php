<?php
namespace app\models\main;

use app\classes\functions;
use app\db\empresa;
use app\classes\modelAbstract;
use app\classes\mensagem;

/**
 * Classe empresaModel
 * 
 * Esta classe fornece métodos para interagir com os dados de empresas.
 * Ela utiliza a classe empresa para realizar operações de consulta, inserção e exclusão no banco de dados.
 * 
 * @package app\models\main
 */
class empresaModel{

    /**
     * Obtém uma empresa pelo ID ou coluna específica.
     * 
     * @param int $id O ID da empresa a ser buscada.
     * @param string $coluna A coluna pela qual buscar a empresa (opcional, padrão é "id").
     * @return array|null Retorna os dados da empresa ou null se não encontrado.
     */
    public static function get(int $id,string $coluna = "id"){
        return (new empresa)->get($id, $coluna);
    }

    /**
     * Obtém empresas associadas a uma agenda.
     * 
     * @param int $id_agenda O ID da agenda para buscar empresas associadas.
     * @return array Retorna um array de empresas.
     */
    public static function getByAgenda(int $id_agenda){
        $db = new empresa;
        $db->addJoin("INNER","agenda","agenda.id_empresa","empresa.id");
        // O restante do código precisa ser implementado para completar a lógica desta função.
    }

    /**
     * Insere ou atualiza uma empresa.
     * 
     * @param string $nome O nome da empresa.
     * @param string $cpf_cnpj O CPF ou CNPJ da empresa.
     * @param string $email O e-mail da empresa.
     * @param string $telefone O telefone da empresa.
     * @param string $razao A razão social da empresa.
     * @param string $fantasia O nome fantasia da empresa.
     * @param string $id O ID da empresa (opcional).
     * @return string|bool Retorna o ID da empresa inserida ou atualizada se a operação for bem-sucedida, caso contrário retorna false.
     */
    public static function set(string $nome,string $cpf_cnpj,string $email,string $telefone,string $razao,string $fantasia,int $id = null){
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

        if(self::get($cpf_cnpj = functions::onlynumber($cpf_cnpj),"cpf_cnpj")->id){
            $mensagens[] = "CPF/CNPJ já cadastrado";
        }
  
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $mensagens[] = "E-mail Invalido";
        }

        if(self::get($email,"email")->id){
            $mensagens[] = "Email já cadastrado";
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
        $values->cnpj = $cpf_cnpj;
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

    /**
     * Exclui um registro de empresa.
     * 
     * @param string $id O ID da empresa a ser excluída.
     * @return bool Retorna true se a operação for bem-sucedida, caso contrário retorna false.
     */
    public static function delete(int $id){
       return (new empresa)->delete($id);
    }
}
