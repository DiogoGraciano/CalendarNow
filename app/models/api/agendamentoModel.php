<?php 
namespace app\models\api;

use app\helpers\functions;
use app\db\tables\agendamento;
use app\helpers\mensagem;
use app\models\abstract\model;

/**
 * Classe agendamentoModel
 * 
 * Esta classe fornece métodos para interagir com os agendamentos.
 * Ela utiliza a classe agendamento para realizar operações de consulta, inserção, atualização e exclusão no banco de dados.
 * 
 * @package app\models\main
*/
final class agendamentoModel extends model{

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
     * @param string $status O status do agendamento (opcional, padrão é 4).
     * @return array Retorna um array com os eventos de agendamento dentro do intervalo de datas especificado.
    */
    public static function getEvents($dt_inicio,$dt_fim,$id_agenda,$isnotstatus=4):array
    {
        $db = new agendamento;
        $results = $db->addFilter("dt_ini",">=",$dt_inicio)
                      ->addFilter("dt_fim","<=",$dt_fim)
                      ->addFilter("id_agenda","=",intval($id_agenda))
                      ->addFilter("status","!=",$isnotstatus)
                      ->selectAll();

        $return = [];

        if ($results){
            foreach ($results as $result){
                $return[] = [
                    'id' => functions::encrypt($result->id),
                    'title' => $result->titulo,
                    'color' => $result->cor,
                    'start' => $result->dt_ini,
                    'end' => $result->dt_fim,
                ];
            }
        }
        return $return;
    }

    /**
     * Obtém os eventos de agendamento dentro de um intervalo de datas para um funcionário específico em uma determinada agenda.
     * 
     * @param string $dt_inicio A data de início do intervalo.
     * @param string $dt_fim A data de término do intervalo.
     * @param string $id_agenda O ID da agenda.
     * @param string $id_funcionario O ID do funcionário.
     * @param string $status O status do agendamento (opcional, padrão é 4).
     * @return array Retorna um array com os eventos de agendamento dentro do intervalo de datas especificado para o funcionário.
    */
    public static function getEventsbyFuncionario($dt_inicio,$dt_fim,$id_agenda,$id_funcionario,$id_status=4):array
    {
        $db = new agendamento;
        $results = $db->addFilter("dt_ini",">=",$dt_inicio)
                      ->addFilter("dt_fim","<=",$dt_fim)
                      ->addFilter("id_agenda","=",intval($id_agenda))
                      ->addFilter("id_funcionario ","=",intval($id_funcionario))
                      ->addFilter("id_status","!=",$id_status)
                      ->selectAll();

        $return = [];

        $user = usuarioModel::getLogged();

        if ($results){
            foreach ($results as $result){
                if ($user->tipo_usuario != 3){
                    $return[] = [
                        'id' => functions::encrypt($result->id),
                        'title' => $result->titulo,
                        'color' => $result->cor,
                        'start' => $result->dt_ini,
                        'end' => $result->dt_fim,
                    ];
                }
                elseif ($user->id == $result->id_usuario){
                    $return[] = [
                        'id' => functions::encrypt($result->id),
                        'title' => $result->titulo,
                        'color' => $result->cor,
                        'start' => $result->dt_ini,
                        'end' => $result->dt_fim,
                    ];
                }
                else{
                    $return[] = [
                        'title' => "Outro agendamento",
                        'color' => "#9099ad",
                        'start' => $result->dt_ini,
                        'end' => $result->dt_fim,
                    ];
                }
            }
        }
        return $return;
    }

    /**
     * Obtém os agendamentos associados a uma empresa.
     * 
     * @param string $id_empresa O ID da empresa.
     * @return array Retorna um array com os agendamentos associados à empresa.
    */
    public static function getAgendamentosByEmpresa($id_empresa,?string $dt_ini = "",?string $dt_fim = "",$onlyActive = false,?int $id_agenda = null,?int $id_funcionario = null,?int $limit = null,?int $offset = null):array
    {
        $db = new agendamento;

        $db->addJoin("usuario","usuario.id","agendamento.id_usuario","LEFT")
            ->addJoin("agenda","agenda.id","agendamento.id_agenda")
            ->addJoin("cliente","cliente.id","agendamento.id_cliente","LEFT")
            ->addJoin("funcionario","funcionario.id","agendamento.id_funcionario")
            ->addFilter("agenda.id_empresa","=",$id_empresa);
                  
        if($dt_ini && $dt_fim){
            $db->addFilter("agendamento.dt_fim",">=",functions::dateTimeBd($dt_ini));
            $db->addFilter("agendamento.dt_fim","<=",functions::dateTimeBd($dt_fim));
        }

        if($onlyActive){
            $db->addFilter("agendamento.id_status","IN",[1,2]);
        }

        if($id_funcionario){
            $db->addFilter("agendamento.id_funcionario","=",$id_funcionario);
        }

        if($id_agenda){
            $db->addFilter("agendamento.id_agenda","=",$id_agenda);
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

        return $db->selectColumns("agendamento.id","usuario.cpf_cnpj","cliente.nome as cli_nome","usuario.nome as usu_nome","usuario.email","usuario.telefone","agenda.nome as age_nome","funcionario.nome as fun_nome","agendamento.id_status","dt_ini","dt_fim");
    }

    /**
     * Obtém os agendamentos associados a um usuário.
     * 
     * @param int $id_usuario O ID do usuário.
     * @param string $dt_ini data inicial caso não informado será buscado todos.
     * @param string $dt_fim data final caso não informado será buscado todos.
     * @param bool $onlyActive buscara somente os pedidos que o status forem diferentes de cancelado e não atendido.
     * @return array Retorna um array com os agendamentos associados ao usuário.
    */
    public static function getAgendamentosByUsuario(int $id_usuario,string $dt_ini = "",string $dt_fim = "",$onlyActive = false,?int $id_agenda = null,?int $id_funcionario = null,?int $limit = null,?int $offset = null):array
    {
        $db = new agendamento;

        $db->addJoin("usuario","usuario.id","agendamento.id_usuario","LEFT")
                    ->addJoin("cliente","cliente.id","agendamento.id_cliente","LEFT")
                    ->addJoin("agenda","agenda.id","agendamento.id_agenda")
                    ->addJoin("funcionario","funcionario.id","agendamento.id_funcionario")
                    ->addFilter("usuario.id","=",$id_usuario);

        if($dt_ini && $dt_fim){
            $db->addFilter("agendamento.dt_fim",">=",functions::dateTimeBd($dt_ini));
            $db->addFilter("agendamento.dt_fim","<=",functions::dateTimeBd($dt_fim));
        }

        if($onlyActive){
            $db->addFilter("agendamento.id_status","IN",[1,2]);
        }

        if($id_funcionario){
            $db->addFilter("agendamento.id_funcionario","=",$id_funcionario);
        }

        if($id_agenda){
            $db->addFilter("agendamento.id_agenda","=",$id_agenda);
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
        
        return $db->selectColumns("agendamento.id","usuario.cpf_cnpj","cliente.nome as cli_nome","usuario.nome as usu_nome","usuario.email","usuario.telefone","agenda.nome as age_nome","funcionario.nome as fun_nome","agendamento.id_status","dt_ini","dt_fim");;
    }

    public static function prepareList(array $agendamentos)
    {
        $statuses = statusModel::getAll();

        $statusHashMap = [];
        foreach ($statuses as $status)
        {
            $statusHashMap[$status->id] = $status->nome;
        }

        $agendamentosFinal = [];
        foreach ($agendamentos as $agendamento){
            if ($agendamento->cpf_cnpj){
                $agendamento->cpf_cnpj = functions::formatCnpjCpf($agendamento->cpf_cnpj);
            }
            if ($agendamento->telefone){
                $agendamento->telefone = functions::formatPhone($agendamento->telefone);
            }
            if (!$agendamento->usu_nome){
                $agendamento->nome = $agendamento->cli_nome;
            }
            if (!$agendamento->cli_nome){
                $agendamento->nome = $agendamento->usu_nome;
            }
            if ($agendamento->dt_ini){
                $agendamento->dt_ini = functions::dateTimeBr($agendamento->dt_ini);
            }
            if ($agendamento->dt_fim){
                $agendamento->dt_fim = functions::dateTimeBr($agendamento->dt_fim);
            }
            if ($agendamento->id_status){
                $agendamento->status = $statusHashMap[$agendamento->id_status];
            }
            $agendamentosFinal[] = $agendamento;
        }

        return $agendamentosFinal;
    }

    /**
     * Insere ou atualiza um agendamento.
     * 
     * @param float $total O total do agendamento.
     * @param int $id O ID do agendamento (opcional).
     * @return string|bool Retorna o ID do agendamento se a operação for bem-sucedida, caso contrário retorna false.
     */
    public static function setTotal(int $id):int|bool
    {
        $values = new agendamento;

        $mensagens = [];

        if(!($values->id = self::get($id)->id)){
            $mensagens[] = "Agendamento não encontrada";
        }

        $agendamentosItens = agendamentoItemModel::getItens($values->id);

        $total = 0;
        foreach ($agendamentosItens as $agendamentosIten){
            $total += $agendamentosIten->total_item;
        }

        if(($values->total = $total) < 0){
            $mensagens[] = "Total deve ser maior que 0";
        }

        if($mensagens){
            mensagem::setErro(...$mensagens);
            return false;
        }

        if ($values)
            $retorno = $values->store();

        if ($retorno == true){
            mensagem::setSucesso("Agendamento salvo com sucesso");
            return $values->id;
        }
        else 
            return False;
    }

    /**
     * cancela um agendamento.
     * 
     * @param int $id O ID do agendamento.
     * @return int|bool Retorna o ID do agendamento se a operação for bem-sucedida, caso contrário retorna false.
     */
    public static function cancel(int $id):int|bool
    {
        $values = self::get($id);

        $mensagens = [];

        if(!$values->id){
            $mensagens[] = "Agendamento não encontrada";
        }

        if($mensagens){
            mensagem::setErro(...$mensagens);
            return false;
        }

        $values->id_status = 4;

        if ($values->store()){
            mensagem::setSucesso("Agendamento salvo com sucesso");
            return $values->id;
        }
         
        return False;
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
    public static function set(int $id_agenda,int $id_funcionario,string $titulo,string $dt_ini,string $dt_fim,float $total,int $id_status,string|null $cor = null,string|null $obs = null,int|null $id_usuario = null,int|null $id_cliente = null,int|null $id=null):int|bool
    {

        $values = new agendamento;

        $mensagens = [];

        if($id && !($values->id = self::get($id)->id)){
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

        if(!$values->titulo = htmlspecialchars(ucwords(strtolower(trim($titulo))))){
            $mensagens[] = "Titulo deve ser informado";
        }

        if(!$values->dt_ini = functions::dateTimeBd($dt_ini)){
            $mensagens[] = "Data inicial invalida";
        }

        if(!$values->dt_fim = functions::dateTimeBd($dt_fim)){
            $mensagens[] = "Data final invalida";
        }

        if(!$values->cor = functions::validaCor($cor?:"#4267b2")){
            $mensagens[] = "Cor invalida";
        }

        if(($values->total = $total) < 0){
            $mensagens[] = "Total deve ser maior que 0";
        }

        if(!($values->id_status = $id_status) && !StatusModel::get($values->id_status)){
            $mensagens[] = "Status informado invalido";
        }

        if($id_usuario && !$values->id){
            if(($empresa = empresaModel::getByAgenda($id_agenda))){

                $dt_ini = (new \DateTimeImmutable($dt_ini))->format("Y-m-d");
                $primeiroDiaMes = (new \DateTimeImmutable($dt_ini))->format("Y-m")."-01";
                $ultimoDiaMes = (new \DateTimeImmutable($dt_ini))->modify('last day of this month')->format("Y-m-d");
                $primeiroDiaSemana = (new \DateTimeImmutable($dt_ini))->modify('monday this week')->format("Y-m-d");
                $ultimoDiaSemana = (new \DateTimeImmutable($dt_ini))->modify('sunday this week')->format("Y-m-d");

                $agendamentos = self::getAgendamentosByUsuario($id_usuario,$primeiroDiaMes,$ultimoDiaMes,true);

                $dia = 0;
                $semana = 0;
                $mes = 0;
                foreach ($agendamentos as $agendamento){

                    $agendamento_dt_ini = (new \DateTimeImmutable($agendamento->dt_ini))->format("Y-m-d");
                    
                    if($agendamento_dt_ini == $dt_ini){
                        $dia++;
                    }
                    if($agendamento_dt_ini >= $primeiroDiaSemana && $agendamento_dt_ini <= $ultimoDiaSemana){
                        $semana++;
                    }
                    if($agendamento_dt_ini >= $primeiroDiaMes && $agendamento_dt_ini <= $ultimoDiaMes){
                        $mes++;
                    }
                }

                if(intval($empresa->configuracoes->max_agendamento_dia) < $dia)
                    $mensagens[] = "Numero maximo de agendamentos para o dia de hoje atingindo";

                if(intval($empresa->configuracoes->max_agendamento_semana) < $semana)
                    $mensagens[] = "Numero maximo de agendamentos para o essa semana atingindo";

                if(intval($empresa->configuracoes->max_agendamento_mes) < $mes)
                    $mensagens[] = "Numero maximo de agendamentos para o esse mês atingindo";
            }
            else 
                $mensagens[] = "Nenhuma empresa vinculada a agenda informada";
           
        }   

        if($mensagens){
            mensagem::setErro(...$mensagens);
            return false;
        }

        $values->obs = htmlspecialchars(trim($obs));

        if ($values->store()){
            mensagem::setSucesso("Agendamento salvo com sucesso");
            return $values->id;
        }
            
        return False;
    }

    /**
     * Exclui um agendamento pelo ID.
     * 
     * @param int $id O ID do agendamento a ser excluído.
     * @return bool Retorna true se a operação for bem-sucedida, caso contrário retorna false.
    */
    public static function delete(int $id):bool
    {
        return (new agendamento)->delete($id);
    }

}