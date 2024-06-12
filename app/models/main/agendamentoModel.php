<?php 
namespace app\models\main;

use app\classes\functions;
use app\db\agendamento;
use app\classes\mensagem;
use stdClass;

/**
 * Classe agendamentoModel
 * 
 * Esta classe fornece métodos para interagir com os agendamentos.
 * Ela utiliza a classe agendamento para realizar operações de consulta, inserção, atualização e exclusão no banco de dados.
 * 
 * @package app\models\main
*/
class agendamentoModel{

    /**
     * Obtém um agendamento pelo ID.
     * 
     * @param string $id O ID do agendamento.
     * @return object Retorna o objeto do agendamento ou null se não encontrado.
    */
    public static function get($id = ""):object
    {
        return (new agendamento)->get($id);
    }

    /**
     * Obtém os eventos de agendamento dentro de um intervalo de datas para uma determinada agenda.
     * 
     * @param string $dt_inicio A data de início do intervalo.
     * @param string $dt_fim A data de término do intervalo.
     * @param string $id_agenda O ID da agenda.
     * @param string $status O status do agendamento (opcional, padrão é 99).
     * @return array Retorna um array com os eventos de agendamento dentro do intervalo de datas especificado.
    */
    public static function getEvents($dt_inicio,$dt_fim,$id_agenda,$isnotstatus=99):array
    {
        $db = new agendamento;
        $results = $db->addFilter("dt_ini",">=",$dt_inicio)
                      ->addFilter("dt_fim","<=",$dt_fim)
                      ->addFilter("id_agenda","=",intval($id_agenda))
                      ->addFilter("status","!=",$isnotstatus)
                      ->selectAll();

        $retorn = [];

        if ($results){
            foreach ($results as $result){
                $retorn[] = [
                    'id' => functions::encrypt($result->id),
                    'title' => $result->titulo,
                    'color' => $result->cor,
                    'start' => $result->dt_ini,
                    'end' => $result->dt_fim,
                ];
            }
        }
        return $retorn;
    }

    /**
     * Obtém os eventos de agendamento dentro de um intervalo de datas para um funcionário específico em uma determinada agenda.
     * 
     * @param string $dt_inicio A data de início do intervalo.
     * @param string $dt_fim A data de término do intervalo.
     * @param string $id_agenda O ID da agenda.
     * @param string $id_funcionario O ID do funcionário.
     * @param string $status O status do agendamento (opcional, padrão é 99).
     * @return array Retorna um array com os eventos de agendamento dentro do intervalo de datas especificado para o funcionário.
    */
    public static function getEventsbyFuncionario($dt_inicio,$dt_fim,$id_agenda,$id_funcionario,$status=99):array
    {
        $db = new agendamento;
        $results = $db->addFilter("dt_ini",">=",$dt_inicio)
                      ->addFilter("dt_fim","<=",$dt_fim)
                      ->addFilter("id_agenda","=",intval($id_agenda))
                      ->addFilter("id_funcionario ","=",intval($id_funcionario))
                      ->addFilter("status","!=",$status)
                      ->selectAll();

        $retorn = [];

        $user = usuarioModel::getLogged();

        if ($results){
            foreach ($results as $result){
                if ($user->tipo_usuario != 3){
                    $retorn[] = [
                        'id' => functions::encrypt($result->id),
                        'title' => $result->titulo,
                        'color' => $result->cor,
                        'start' => $result->dt_ini,
                        'end' => $result->dt_fim,
                    ];
                }
                elseif ($user->id == $result->id_usuario){
                    $retorn[] = [
                        'id' => functions::encrypt($result->id),
                        'title' => $result->titulo,
                        'color' => $result->cor,
                        'start' => $result->dt_ini,
                        'end' => $result->dt_fim,
                    ];
                }
                else{
                    $retorn[] = [
                        'title' => "Outro agendamento",
                        'color' => "#9099ad",
                        'start' => $result->dt_ini,
                        'end' => $result->dt_fim,
                    ];
                }
            }
        }
        return $retorn;
    }

    /**
     * Obtém os agendamentos associados a uma empresa.
     * 
     * @param string $id_empresa O ID da empresa.
     * @return array Retorna um array com os agendamentos associados à empresa.
    */
    public static function getAgendamentosByEmpresa($id_empresa):array
    {
        $db = new agendamento;

        $result = $db->addJoin("LEFT","usuario","usuario.id","agendamento.id_usuario")
                    ->addJoin("INNER","agenda","agenda.id","agendamento.id_agenda")
                    ->addJoin("LEFT","cliente","cliente.id","agendamento.id_cliente")
                    ->addJoin("INNER","funcionario","funcionario.id","agendamento.id_funcionario")
                    ->addFilter("agenda.id_empresa","=",$id_empresa)
                    ->selectColumns("agendamento.id","usuario.cpf_cnpj","cliente.nome as cli_nome","usuario.nome as usu_nome","usuario.email","usuario.telefone","agenda.nome as age_nome","funcionario.nome as fun_nome","dt_ini","dt_fim");

        return $result;
    }

    /**
     * Obtém os agendamentos associados a um usuário.
     * 
     * @param string $id_usuario O ID do usuário.
     * @return array Retorna um array com os agendamentos associados ao usuário.
    */
    public static function getAgendamentosByUsuario($id_usuario):array
    {
        $db = new agendamento;

        $result = $db->addJoin("LEFT","usuario","usuario.id","agendamento.id_usuario")
                    ->addJoin("INNER","agenda","agenda.id","agendamento.id_agenda")
                    ->addJoin("INNER","funcionario","funcionario.id","agendamento.id_funcionario")
                    ->addFilter("usuario.id","=",$id_usuario)
                    ->selectColumns("agendamento.id","usuario.cpf_cnpj","usuario.nome as usu_nome","usuario.email","usuario.telefone","agenda.nome as age_nome","funcionario.nome as fun_nome","dt_ini","dt_fim");
                    
        return $result;
    }

     /**
     * Insere ou atualiza um agendamento.
     * 
     * @param int $id_agenda O ID da agenda.
     * @param int $id_funcionario O ID do funcionário associado ao agendamento.
     * @param string $titulo O título do agendamento.
     * @param string $dt_ini A data e hora de início do agendamento.
     * @param string $dt_fim A data e hora de término do agendamento.
     * @param string $cor A cor do agendamento.
     * @param float $total O total do agendamento.
     * @param int $status O status do agendamento.
     * @param string $obs Observações do agendamento.
     * @param int $id_usuario O ID do usuário associado ao agendamento.
     * @param int $id_cliente O ID do cliente associado ao agendamento.
     * @param int $id O ID do agendamento (opcional).
     * @return string|bool Retorna o ID do agendamento se a operação for bem-sucedida, caso contrário retorna false.
     */
    public static function set(int $id_agenda,int $id_funcionario,string $titulo,string $dt_ini,string $dt_fim,string $cor,float $total,int $id_status,string $obs = null,int $id_usuario = null,int $id_cliente = null,int $id=null):int|bool
    {

        $values = new agendamento;

        $mensagens = [];

        if($id && !$values->id = self::get($id)->id){
            $mensagens[] = "Agendamento não encontrada";
        }

        if(!$id_agenda || !$values->id_agenda = agendaModel::get($id_agenda)->id){
            $mensagens[] = "Agenda não encontrada";
        }

        if(!$id_cliente && !$id_usuario){
            $mensagens[] = "Obrigatorio Informar Cliente ou Usuario";
        }
        else{
            if($id_usuario && !$values->id_usuario = usuarioModel::get($id_usuario)->id){
                $mensagens[] = "Usuario não encontrado";
            }

            if($id_cliente && !$values->id_cliente = clienteModel::get($id_cliente)->id){
                $mensagens[] = "Cliente não cadastrado";
            }
        }

        if(!$id_funcionario || !$values->id_funcionario = funcionarioModel::get($id_funcionario)->id){
            $mensagens[] = "Funcionario não cadastrado";
        }

        if(!$values->titulo = ucwords(strtolower(trim($titulo)))){
            $mensagens[] = "Titulo deve ser informado";
        }

        if(!$values->dt_ini = functions::dateTimeBd($dt_ini)){
            $mensagens[] = "Data inicial invalida";
        }

        if(!$values->dt_fim = functions::dateTimeBd($dt_fim)){
            $mensagens[] = "Data final invalida";
        }

        if(!$values->cor = functions::validaCor($cor)){
            $mensagens[] = "Cor invalida";
        }

        if(($values->total = $total) <= 0){
            $mensagens[] = "Total deve ser maior que 0";
        }

        if(!($values->id_status = $id_status) && !StatusModel::get($values->id_status)){
            $mensagens[] = "Status informado invalido";
        }

        if($mensagens){
            mensagem::setErro(...$mensagens);
            return false;
        }

        $values->obs = trim($obs);

        if ($values)
            $retorno = $values->store();

        if ($retorno == true){
            mensagem::setSucesso("Agendamento salvo com sucesso");
            return $values->getLastID();
        }else 
            return False;
        
    }

    /**
     * Exclui um agendamento pelo ID.
     * 
     * @param int $id O ID do agendamento a ser excluído.
     * @return bool Retorna true se a operação for bem-sucedida, caso contrário retorna false.
    */
    public static function delete():bool
    {
        return (new agendamento)->delete();
    }

}