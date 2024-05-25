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
     * @return object Retorna os dados da empresa ou null se não encontrado.
     */
    public static function get(string|null|int $value = null,string $coluna = "id"):object
    {
        return (new empresa)->get($value, $coluna);
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
        $db = new empresa;

        $mensagens = [];

        $values = $db->getObject();

        if($id && !self::get($values->id = $id)->id){
            $mensagens[] = "Empresa não existe";
        }

        if(!($values->nome = filter_var(trim($nome)))){
            $mensagens[] = "Nome da Empresa é obrigatorio";
        }

        if(!($values->razao = filter_var(trim($razao)))){
            $mensagens[] = "Razão Social é obrigatorio";
        }

        if(!($values->fantasia = filter_var(trim($fantasia)))){
            $mensagens[] = "Nome da Fantasia é obrigatorio";
        }

        if(!functions::validaCpfCnpj($cpf_cnpj)){
            $mensagens[] = "CPF/CNPJ invalido";
        }

        if(self::get($values->cnpj = functions::onlynumber($cpf_cnpj),"cpf_cnpj")->id){
            $mensagens[] = "CPF/CNPJ já cadastrado";
        }
  
        if(!($values->email = filter_var(trim($email), FILTER_VALIDATE_EMAIL))){
            $mensagens[] = "E-mail Invalido";
        }

        if(self::get($values->email,"email")->id){
            $mensagens[] = "Email já cadastrado";
        }

        if(!($values->telefone = functions::onlynumber($telefone)) || !functions::validaTelefone($telefone)){
            $mensagens[] = "Telefone Invalido";
        }

        if($mensagens){
            mensagem::setErro(...$mensagens);
            return false;
        }

        $retorno = $db->store($values);

        if ($retorno == true){
            mensagem::setSucesso("Empresa salva com sucesso");
            return $db->getLastID();
        }

        mensagem::setErro("Erro ao cadastrar a empresa");
        return false;
    }

    /**
     * Converte uma empresa para um usuario.
     * 
     * @param int $id_empresa O ID da empresa.
     * @param string $senha O senha do usuario da empresa (opcional).
     * @param bool $remove_empresa_on_error Remove empresa caso não consiga cadastrar o usuario.
     * @return int|bool Retorna o ID da empresa inserida ou atualizada se a operação for bem-sucedida, caso contrário retorna false.
    */
    public static function convertEmpresaToUsuario(int $id_empresa,string $senha = "", bool $remove_empresa_on_error = false):int|bool
    {

        $empresa = self::get($id_empresa);

        if($empresa->id){
            try{
                $senha?:$senha = $empresa->cnpj;

                if($id_usuario = usuarioModel::set($empresa->nome,$empresa->cnpj,$empresa->email,$empresa->telefone,$senha,null,1,$empresa->id)){
                    mensagem::setSucesso("Usuario convertido com sucesso");
                    return $id_usuario; 
                
                }
                else{
                    if($remove_empresa_on_error)
                        self::delete($empresa->id);

                    mensagem::setErro("Erro ao converter usuario");
                    return false;
                }
            }
            catch(Exception $e){
                if($remove_empresa_on_error)
                    self::delete($empresa->id);

                mensagem::setErro("Erro ao converter usuario");
                return false;
            }
        }
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
