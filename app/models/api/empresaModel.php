<?php
namespace app\models\api;

use app\helpers\functions;
use app\db\tables\empresa;
use app\helpers\mensagem;
use stdClass;

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
     * @return object Retorna os dados da empresa ou null se não encontrado.
     */
    public static function get(string|null|int $value = null,string $coluna = "id"):object
    {
        return (new empresa)->get($value, $coluna);
    }

    /**
     * Obtém uma todas as empresas.
     * 
     * @return array Retorna os dados da empresa ou null se não encontrado.
     */
    public static function getAll():array
    {
        return (new empresa)->getAll();
    }

    /**
     * Obtém empresa por agenda.
     * 
     * @return object|bool Retorna os dados da empresa e caso não exista retorna false.
    */
    public static function getByAgenda($id_agenda):object|bool
    {
        $empresa = new empresa;

        $empresa = $empresa->addJoin("agenda","agenda.id",$id_agenda)->addLimit(1)->selectColumns("empresa.id,empresa.nome,empresa.email,empresa.telefone,empresa.cnpj,empresa.razao,empresa.fantasia");

        if(isset($empresa[0]) && $empresa[0]){
            $empresa = $empresa[0];

            $configuracoes = configuracoesModel::getByEmpresa($empresa->id);

            $empresa->configuracoes = new \stdClass;

            foreach ($configuracoes as $configuracao){
                $identificador = $configuracao->identificador;
                $empresa->configuracoes->$identificador = $configuracao->configuracao;
            }

            return $empresa;
        }
        
        return false;
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
     * @return int|bool Retorna o ID da empresa inserida ou atualizada se a operação for bem-sucedida, caso contrário retorna false.
     */
    public static function set(string $nome,string $cpf_cnpj,string $email,string $telefone,string $razao,string $fantasia,null|int $id = null):int|bool
    {
        $values = new empresa;

        $mensagens = [];

        if($id && !self::get($values->id = $id)->id){
            $mensagens[] = "Empresa não existe";
        }

        if(!($values->nome = htmlspecialchars(trim($nome)))){
            $mensagens[] = "Nome da Empresa é obrigatorio";
        }

        if(!($values->razao = htmlspecialchars(trim($razao)))){
            $mensagens[] = "Razão Social é obrigatorio";
        }

        if(!($values->fantasia = htmlspecialchars(trim($fantasia)))){
            $mensagens[] = "Nome da Fantasia é obrigatorio";
        }

        if(!functions::validaCpfCnpj($cpf_cnpj)){
            $mensagens[] = "CPF/CNPJ invalido";
        }

        if(self::get($values->cnpj = functions::onlynumber($cpf_cnpj),"cpf_cnpj")->id){
            $mensagens[] = "CPF/CNPJ já cadastrado";
        }
  
        if(!($values->email = htmlspecialchars(filter_var(trim($email), FILTER_VALIDATE_EMAIL)))){
            $mensagens[] = "E-mail Invalido";
        }

        if(!$id && self::get($values->email,"email")->id){
            $mensagens[] = "Email já cadastrado";
        }

        if(!($values->telefone = functions::onlynumber($telefone)) || !functions::validaTelefone($telefone)){
            $mensagens[] = "Telefone Invalido";
        }

        if($mensagens){
            mensagem::setErro(...$mensagens);
            return false;
        }

        if ($values->store()){
            mensagem::setSucesso("Empresa salva com sucesso");
            return $values->id;
        }

        mensagem::setErro("Erro ao cadastrar a empresa");
        return false;
    }

    /**
     * Exclui um registro de empresa.
     * 
     * @param string $id O ID da empresa a ser excluída.
     * @return bool Retorna true se a operação for bem-sucedida, caso contrário retorna false.
     */
    public static function delete(int $id):bool
    {
       return (new empresa)->delete($id);
    }
}
