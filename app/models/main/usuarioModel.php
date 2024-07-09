<?php 
namespace app\models\main;
use app\db\usuario;
use app\classes\functions;
use app\classes\mensagem;
use app\models\main\loginModel;

/**
 * Classe usuarioModel
 * 
 * Esta classe fornece métodos para interagir com os dados de usuários.
 * Ela utiliza a classe usuario para realizar operações de consulta, inserção e exclusão no banco de dados.
 * 
 * @package app\models\main
*/
class usuarioModel{

    /**
     * Obtém um usuário pelo ID.
     * 
     * @param int|null|string $value O Valor do usuário a ser buscado.
     * @param int|null|string $column A Coluna do usuário a ser buscado.
     * @return object Retorna os dados do usuário ou objeto se não encontrado.
    */
    public static function get(int|null|string $value = null,string $column = "id"):object
    {
        return (new usuario)->get($value,$column);
    }

    /**
     * Obtém o usuário logado.
     * 
     * @return object|bool Retorna os dados do usuário logado ou null se não houver usuário logado.
    */
    public static function getLogged():object|bool
    {
        if (isset($_SESSION["user"]) && $_SESSION["user"])
            return $_SESSION["user"];

        loginModel::deslogar();
        return false;
    }

    /**
     * Obtém um usuário pelo CPF/CNPJ e e-mail.
     * 
     * @param string $cpf_cnpj O CPF ou CNPJ do usuário.
     * @param string $email O e-mail do usuário.
     * @return object|bool Retorna um array com os dados do usuário ou um array vazio se não encontrado.
    */
    public static function getByCpfEmail(string $cpf_cnpj,string $email):object|bool
    {
        $db = new usuario;

        $usuario = $db->addFilter("cpf_cnpj", "=", functions::onlynumber($cpf_cnpj))->addFilter("email", "=", $email)->addLimit(1)->selectAll();

        return $usuario[0] ?? false;
    }

    /**
     * Obtém um usuário pelo CPF/CNPJ.
     * 
     * @param string $cpf_cnpj O CPF ou CNPJ do usuário.
     * @return array Retorna um array com os dados do usuário ou um array vazio se não encontrado.
    */
    public static function getByCpfCnpj(string $cpf_cnpj):array
    {

        $db = new usuario;

        $usuario = $db->addFilter("cpf_cnpj", "=", $cpf_cnpj)->selectAll();

        return $usuario;
    }

    /**
     * Obtém um usuário pelo e-mail.
     * 
     * @param string $email O e-mail do usuário.
     * @return array Retorna um array com os dados do usuário ou um array vazio se não encontrado.
    */
    public static function getByEmail(string $email):array
    {

        $db = new usuario;

        $usuario = $db->addFilter("email", "=", $email)->selectAll();

        return $usuario;
    }

     /**
     * Obtém um usuário pelo id da empresa.
     * 
     * @param string $id_empresa O id da empresa.
     * @return array Retorna um array com os dados do usuário ou um array vazio se não encontrado.
    */
    public static function getByEmpresa(int $id_empresa):array
    {
        $db = new usuario;

        $usuario = $db->addFilter("id_empresa", "=", $id_empresa)->selectAll();

        return $usuario;
    }

    /**
     * Obtém usuários pelo tipo de usuário e ID da agenda.
     * 
     * @param int $tipo_usuario O tipo de usuário.
     * @param string $id_agenda O ID da agenda.
     * @return array Retorna um array de usuários.
    */
    public static function getByTipoUsuarioAgenda(int $tipo_usuario,string $id_agenda):array
    {
        $db = new usuario;
        $usuarios = $db->addJoin("agendamento","usuario.id","agendamento.id_usuario")
                        ->addFilter("tipo_usuario","=",$tipo_usuario)
                        ->addFilter("agendamento.id_agenda","=",$id_agenda)
                        ->addFilter("usuario.tipo_usuario","=",$tipo_usuario)
                        ->addGroup("usuario.id")
                        ->selectColumns('usuario.id','usuario.nome','usuario.cpf_cnpj','usuario.telefone','usuario.senha','usuario.email','usuario.tipo_usuario','usuario.id_empresa');
                        
        return $usuarios;
    }

    /**
     * Insere ou atualiza um usuário.
     * 
     * @param string $nome O nome do usuário.
     * @param string $cpf_cnpj O CPF ou CNPJ do usuário.
     * @param string $email O e-mail do usuário.
     * @param string $telefone O telefone do usuário.
     * @param string $senha A senha do usuário.
     * @param string $id O ID do usuário (opcional).
     * @param int $tipo_usuario O tipo de usuário (padrão é 3).
     * @param int $id_empresa O ID da empresa associada (opcional, padrão é "null").
     * @param bool $valid_fk valida outras tabelas vinculadas.
     * @return int|bool Retorna o ID do usuário inserido ou atualizado se a operação for bem-sucedida, caso contrário retorna false.
     */
    public static function set(string $nome,string $cpf_cnpj,string $email,string $telefone,string $senha,int|null $id = null,int $tipo_usuario = 3,int|null $id_empresa = null,bool $valid_fk = true):int|bool
    {

        $values = new usuario;

        $mensagens = [];

        if(!($values->nome = htmlspecialchars((trim($nome))))){
            $mensagens[] = "Nome é invalido";
        }

        if(!($values->cpf_cnpj = functions::onlynumber($cpf_cnpj)) || !functions::validaCpfCnpj($cpf_cnpj)){
            $mensagens[] = "CPF/CNPJ invalido";
        }

        if(!($values->email = htmlspecialchars(filter_var(trim($email), FILTER_VALIDATE_EMAIL)))){
            $mensagens[] = "E-mail Invalido";
        }

        if(!($values->telefone = functions::onlynumber($telefone)) || !functions::validaTelefone($telefone)){
            $mensagens[] = "Telefone Invalido";
        }

        if(!($values->tipo_usuario = $tipo_usuario) || $values->tipo_usuario  < 0 || $values->tipo_usuario  > 3){
            $mensagens[] = "Tipo de Usuario Invalido";
        }

        if(($values->tipo_usuario == 2 || $values->tipo_usuario == 1) && !$id_empresa){
            $mensagens[] = "Informar a empresa é obrigatorio para esse tipo de usuario";
        }

        if(($values->id_empresa = $id_empresa) && $valid_fk && !empresaModel::get($values->id_empresa)->id){
            $mensagens[] = "Empresa não existe";
        }

        if(($values->id = $id) && !self::get($values->id)->id){
            $mensagens[] = "Usuario não existe";
        }

        if($mensagens){
            mensagem::setErro(...$mensagens);
            return false;
        }

        $values->senha = password_hash(trim($senha),PASSWORD_DEFAULT);

        $retorno = $values->store();
        
        if ($retorno == true){
            mensagem::setSucesso("Salvo com sucesso");
            return $values->getLastID();
        }

        mensagem::setErro("Erro ao cadastrar usuario");
        return False;
    }

    /**
     * Exclui um registro de usuário.
     * 
     * @param int $id O ID do usuário a ser excluído.
     * @return bool Retorna true se a operação for bem-sucedida, caso contrário retorna false.
    */
    public static function delete(int $id):bool
    {
        return (new usuario)->delete($id);
    }

}