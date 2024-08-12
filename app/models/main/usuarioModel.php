<?php 
namespace app\models\main;
use app\db\tables\usuario;
use app\db\tables\usuarioBloqueio;
use app\helpers\functions;
use app\helpers\mensagem;
use app\models\abstract\model;
use app\models\main\loginModel;
use core\session;

/**
 * Classe usuarioModel
 * 
 * Esta classe fornece métodos para interagir com os dados de usuários.
 * Ela utiliza a classe usuario para realizar operações de consulta, inserção e exclusão no banco de dados.
 * 
 * @package app\models\main
*/
final class usuarioModel extends model{

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
        if($user = session::get("user"))
            return $user;

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
     * @param int $id_empresa O id da empresa.
     * @param int $tipo_usuario O id da empresa.
     * @param int $limit limit da query (opcional).
     * @param int $offset offset da query(opcional).
     * @return array Retorna um array com os dados do usuário ou um array vazio se não encontrado.
    */
    public static function getByEmpresa(int $id_empresa,?string $nome = null,?int $id_funcionario = null,?int $tipo_usuario = null,?int $limit = null,?int $offset = null):array
    {
        $db = new usuario;

        $db->addFilter("id_empresa", "=", $id_empresa);

        if($nome){
            $db->addFilter("nome","LIKE","%".$nome."%");
        }

        if($id_funcionario){
            $db->addJoin("cliente","cliente.id_funcionario",$id_funcionario);
        }

        if($tipo_usuario !== null){
            $db->addFilter("tipo_usuario", "=", $tipo_usuario);
        }

        if($limit && $offset){
            self::setLastCount($db);
            $db->addLimit($limit);
            $db->addOffset($offset);
        }
        elseif($limit){
            self::setLastCount($db);
            $db->addLimit($limit);
        }

        return $db->selectColumns('usuario.id','usuario.nome','usuario.cpf_cnpj','usuario.telefone','usuario.senha','usuario.email','usuario.tipo_usuario','usuario.id_empresa');
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
     * Bloqueia um usuario em uma agenda.
     * 
     * @param int $id_usuario O id do usuario.
     * @param int $id_agenda O id da agenda.
     * @return bool true caso sucesso.
    */
    public static function setBloqueio(int $id_usuario,int $id_agenda):bool
    {
        $db = new usuarioBloqueio;

        if(!($db->id_usuario = self::get($id_usuario)->id)){
            $mensagens[] = "Usuario não existe";
        }
        if(!($db->id_agenda = agendaModel::get($id_agenda)->id)){
            $mensagens[] = "Agenda não existe";
        }

        if($db->store()){
            return true;
        }

        return false;
    }

    /**
     * Bloqueia um usuario em uma agenda.
     * 
     * @param int $id_usuario O id do usuario.
     * @param int $id_agenda O id da agenda.
     * @return bool true caso sucesso.
    */
    public static function deleteBloqueio(int $id_usuario,int $id_agenda):bool
    {
        $db = new usuarioBloqueio;

        $db->addFilter($id_usuario,"=",$id_usuario);

        $db->addFilter($id_agenda,"=",$id_agenda);

        $usuarioBloqueio = $db->selectAll();

        if(isset($usuarioBloqueio[0]->id) && $usuarioBloqueio[0]->id){
            return $db->delete($usuarioBloqueio[0]->id);
        }

        return false;
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

        $usuario = self::get($values->id);
        if(($values->id = $id) && !$usuario->id){
            $mensagens[] = "Usuario da Api não existe";
        }

        if(!$values->id && !$senha){
            $mensagens[] = "Senha obrigatoria para usuario não cadastrados";
        }

        if($mensagens){
            mensagem::setErro(...$mensagens);
            return false;
        }

        $values->senha = $senha ? password_hash(trim($senha),PASSWORD_DEFAULT) : $usuario->senha;

        $retorno = $values->store();
        
        if ($retorno == true){
            mensagem::setSucesso("Salvo com sucesso");
            return $values->id;
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