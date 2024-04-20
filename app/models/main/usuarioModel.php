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
     * @param string $id O ID do usuário a ser buscado.
     * @return array|object Retorna os dados do usuário ou objeto se não encontrado.
    */
    public static function get(int $id){
        return (new usuario)->get($id);
    }

    /**
     * Obtém o usuário logado.
     * 
     * @return array|null Retorna os dados do usuário logado ou null se não houver usuário logado.
    */
    public static function getLogged(){
        if (isset($_SESSION["user"]) && $_SESSION["user"])
            return $_SESSION["user"];

        loginModel::deslogar();
    }

    /**
     * Obtém um usuário pelo CPF/CNPJ e e-mail.
     * 
     * @param string $cpf_cnpj O CPF ou CNPJ do usuário.
     * @param string $email O e-mail do usuário.
     * @return array Retorna um array com os dados do usuário ou um array vazio se não encontrado.
    */
    public static function getByCpfEmail(string $cpf_cnpj,string $email){

        $db = new usuario;

        $usuario = $db->selectByValues(["cpf_cnpj","email"],[$cpf_cnpj,$email],True);

        if ($db->getError()){
            return [];
        }

        return $usuario;
    }

    /**
     * Obtém um usuário pelo CPF/CNPJ.
     * 
     * @param string $cpf_cnpj O CPF ou CNPJ do usuário.
     * @return array Retorna um array com os dados do usuário ou um array vazio se não encontrado.
    */
    public static function getByCpfCnpj(string $cpf_cnpj){

        $db = new usuario;

        $usuario = $db->selectByValues(["cpf_cnpj"],[$cpf_cnpj]);

        if ($db->getError()){
            return [];
        }

        return $usuario;
    }

    /**
     * Obtém um usuário pelo e-mail.
     * 
     * @param string $email O e-mail do usuário.
     * @return array Retorna um array com os dados do usuário ou um array vazio se não encontrado.
    */
    public static function getByEmail(string $email){

        $db = new usuario;

        $usuario = $db->selectByValues(["email"],[$email]);

        if ($db->getError()){
            return [];
        }

        return $usuario;
    }

    /**
     * Obtém usuários pelo tipo de usuário e ID da agenda.
     * 
     * @param int $tipo_usuario O tipo de usuário.
     * @param string $id_agenda O ID da agenda.
     * @return array Retorna um array de usuários.
    */
    public static function getByTipoUsuarioAgenda(int $tipo_usuario,string $id_agenda){
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
     * @return string|bool Retorna o ID do usuário inserido ou atualizado se a operação for bem-sucedida, caso contrário retorna false.
     */
    public static function set(string $nome,string $cpf_cnpj,string $email,string $telefone,string $senha,int $id,int $tipo_usuario = 3,int $id_empresa = null){

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

    /**
     * Exclui um registro de usuário.
     * 
     * @param int $id O ID do usuário a ser excluído.
     * @return bool Retorna true se a operação for bem-sucedida, caso contrário retorna false.
    */
    public static function delete(int $id){
        return (new usuario)->delete($id);
    }

}