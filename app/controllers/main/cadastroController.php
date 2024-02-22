<?php 
namespace app\controllers\main;
use app\classes\head;
use app\classes\form;
use app\classes\elements;
use app\classes\controllerAbstract;
use app\classes\consulta;
use app\classes\footer;
use app\classes\functions;
use app\classes\filter;
use app\classes\modal;
use app\models\main\agendaModel;
use app\models\main\usuarioModel;
use app\models\main\enderecoModel;
use app\models\main\cidadeModel;
use app\models\main\empresaModel;
use app\models\main\funcionarioModel;
use app\models\main\grupoFuncionarioModel;


class cadastroController extends controllerAbstract{

    public function index($parameters){

        $head = new head();
        $head -> show("cadastro","consulta");

        $tipo_usuario = "";

        if (array_key_exists(0,$parameters)){
            $tipo_usuario = functions::decrypt($parameters[0]);
        }

        $elements = new elements;

        $user = usuarioModel::getLogged();

        $cadastro = new consulta();

        $filter = new filter($this->url."funcionario/filter/");

        $filter->addbutton($elements->button("Buscar","buscar","submit","btn btn-primary pt-2"));

        $filter->addFilter(4,[$elements->input("pesquisa","Pesquisa:")]);

        $cadastro->addButtons($elements->button("Voltar","voltar","button","btn btn-primary","location.href='".$this->url."opcoes'"));

        $cadastro->addColumns("1","Id","id")
                ->addColumns("10","CPF/CNPJ","cpf_cnpj")
                ->addColumns("15","Nome","nome")
                ->addColumns("15","Email","email")
                ->addColumns("11","Telefone","telefone");

        
        if ($tipo_usuario == 1 || $tipo_usuario == 0){
            $cadastro->addColumns("10","CPF/CNPJ Empresa","cnpj")->addColumns("15","Empresa","nome_empresa");
            $dados = empresaModel::get();
        }
        if ($tipo_usuario == 2){

            $id_grupo_funcionario = "";

            if (array_key_exists(1,$parameters)){
                $id_grupo_funcionario = functions::decrypt($parameters[1]);
            }
            
            $agendas = agendaModel::getByEmpresa($user->id_empresa);

            if($agendas){
                $modal = new modal($this->url."cadastro/massActionAgenda/","massActionAgenda");

                $elements->addOption("","Selecione/Todos");
                
                foreach ($agendas as $agenda){
                    $elements->addOption($agenda->id,$agenda->nome);
                }
                $agenda = $elements->select("Agenda:","agenda");

                $modal->setinputs($agenda);

                $modal->setButton($elements->button("Salvar","submitModalConsulta","button","btn btn-primary w-100 pt-2 btn-block"));

                $modal->show();

                $filter->addFilter(4,[$agenda]);
            }

            $cadastro->addColumns("5","Inicio Expediente","hora_ini")
            ->addColumns("5","Fim Expediente","hora_fim")
            ->addColumns("5","Inicio Almoço","hora_almoco_ini")
            ->addColumns("5","Fim Almoço","hora_almoco_fim")
            ->addColumns("14","Dias","dia");

            if ($id_grupo_funcionario)
                $dados = funcionarioModel::getListFuncionariosByGrupoFuncionario($id_grupo_funcionario);
            else  
                $dados = funcionarioModel::getListFuncionariosByEmpresa($user->id_empresa);

            $cadastro->addButtons($elements->button("Adicionar Agenda ao Funcionario","openModel","button","btn btn-primary","openModal('massActionAgenda')"));
        }
        $grupo_funcionarios = grupoFuncionarioModel::getByEmpresa($user->id_empresa);

        if ($grupo_funcionarios){

            $elements->addOption("","Selecione/Todos");
            foreach ($grupo_funcionarios as $grupo_funcionario){
                $elements->addOption($grupo_funcionario->id,$grupo_funcionario->nome);
            }

            $grupo_funcionario = $elements->select("Grupo Funcionario","grupo_funcionario");

            $modal = new modal($this->url."servico/massActionGrupoFuncionario/","massActionGrupoFuncionario");

            $modal->setinputs($grupo_funcionario);

            $modal->setButton($elements->button("Salvar","submitModalConsulta","button","btn btn-primary w-100 pt-2 btn-block"));

            $modal->show();

            $filter->addFilter(4,[$grupo_funcionario]);

            $cadastro->addButtons($elements->button("Adicionar Funcionario ao Grupo","openModelGrupoFuncionario","button","btn btn-primary","openModal('massActionGrupoFuncionario')"));
        }

        $filter->show();

        $cadastro->addColumns("14","Ações","acoes");

        $cadastro->show($this->url."cadastro/manutencao/".functions::encrypt($tipo_usuario),$this->url."cadastro/action/",$dados,"id",true);
      
        $footer = new footer;
        $footer->show();
    }
    public function manutencao($parameters = array(),$login=False){

        if ($login)
            $form = new form($this->url."login/save");
        else
            $form = new form($this->url."cadastro/action");

        $head = new head();
        $head->show("Cadastro","");

        $cd = "";
        $tipo_usuario = "";
        $user = usuarioModel::getLogged();

        if (array_key_exists(0,$parameters)){
            $form->setHidden("tipo_usuario",$parameters[0]);
            $tipo_usuario = functions::decrypt($parameters[0]);
        }else{
            if ($login)
                $this->go("login/index");
            else 
                $this->go("cadastro/index");
        }

        if (array_key_exists(1,$parameters)){
            $form->setHidden("cd",$parameters[1]);
            $cd = functions::decrypt($parameters[1]);
        }
        
        if ($tipo_usuario == 1){
            $dado = usuarioModel::get($cd);
            $dadoEndereco = enderecoModel::get($dado->id);
            $dadoEmpresa = empresaModel::get($dado->id_empresa);
        }

        if ($tipo_usuario == 2){
            $dadoFuncionario = funcionarioModel::get($cd);
            $dado = usuarioModel::get($dadoFuncionario->id_usuario);
            $form->setHidden("id_funcionario",functions::encrypt($dadoFuncionario->id));
            $form->setHidden("id_empresa",functions::encrypt($user->id_empresa));
        }

        if ($tipo_usuario == 3){
            $dado = usuarioModel::get($cd);
            $dadoEndereco = enderecoModel::get($dado->id);
        }

        $elements = new elements;

        $form->setDoisInputs(
            $elements->input("nome","Nome",$dado->nome,true),
            $elements->input("cpf_cnpj","CPF/CNPJ:",$dado->cpf_cnpj?functions::formatCnpjCpf($dado->cpf_cnpj):"",true),
            array("nome","cpf_cnpj")
        );

        $form->setTresInputs(
            $elements->input("email","Email",$dado->email,true,false,"","email"),
            $elements->input("senha","Senha","",true,false,"","password"),
            $elements->input("telefone","Telefone",functions::formatPhone($dado->telefone),true),
            array("email","senha","telefone")
        );
      
        if ($tipo_usuario == 1){
            $form->setTresInputs(
                $elements->input("nome_empresa","Nome da Empresa",$dadoEmpresa->nome,true),
                $elements->input("fantasia","Nome Fantasia",$dadoEmpresa->fantasia,true),
                $elements->input("razao","Razao Social:",$dadoEmpresa->razao,true),
                array("nome_empresa","fantasia","razao")
            );
        }

        if ($tipo_usuario == 2){
            $elements->setOptions("grupo_funcionario","id","nome");
            $id_grupo_funcionario = $elements->select("Grupo de Funcionarios","id_grupo_funcionario");

            $form->setInputs(
                $id_grupo_funcionario
            );

            $form->setDoisInputs(
                $elements->input("hora_ini","Hora Inicial de Trabalho",functions::formatTime($dadoFuncionario->hora_ini),true,false,"","time"),
                $elements->input("hora_fim","Hora Final de Trabalho",functions::formatTime($dadoFuncionario->hora_fim),true,false,"","time"),
                array("hora_ini","hora_fim")
            );

            $form->setDoisInputs(
                $elements->input("hora_almoco_ini","Hora Inicial de Almoço",functions::formatTime($dadoFuncionario->hora_almoco_ini),true,false,"","time"),
                $elements->input("hora_almoco_fim","Hora Final de Almoço",functions::formatTime($dadoFuncionario->hora_almoco_fim),true,false,"","time"),
                array("hora_almoco_ini","hora_almoco_fim")
            );

            $form->setInputs(
                $elements->label("Dias de trabalho na Semana")
            );

            if ($dadoFuncionario->dias)
                $checkDias = explode(",",$dadoFuncionario->dias);
            else 
                $checkDias = [];

            $form->addCustomInput(2,$elements->checkbox("dom","Domingo",false,isset($checkDias[0]) && $checkDias[0]?true:false,false,"dom"),"dom");
            $form->addCustomInput(2,$elements->checkbox("seg","Segunda",false,isset($checkDias[1]) && $checkDias[1]?true:false,false,"seg"),"seg");
            $form->addCustomInput(2,$elements->checkbox("ter","Terça",false,isset($checkDias[2]) && $checkDias[2]?true:false,false,"ter"),"ter");
            $form->addCustomInput(2,$elements->checkbox("qua","Quarta",false,isset($checkDias[3]) && $checkDias[3]?true:false,false,"qua"),"qua");
            $form->addCustomInput(2,$elements->checkbox("qui","Quinta",false,isset($checkDias[4]) && $checkDias[4]?true:false,false,"qui"),"qui");
            $form->addCustomInput(2,$elements->checkbox("sex","Sexta",false,isset($checkDias[5]) && $checkDias[5]?true:false,false,"sex"),"sex");
            $form->addCustomInput(2,$elements->checkbox("sab","Sabado",false,isset($checkDias[6]) && $checkDias[6]?true:false,false,"sab"),"sab");
        
            $form->setCustomInputs();
        }

        if ($tipo_usuario != 2){
            $elements->setOptions("estado","id","nome");
            $estado = $elements->select("Estado","id_estado",$dadoEndereco->id_estado?:24,true);

            $form->setDoisInputs(
                $elements->input("cep","CEP",$dadoEndereco->cep,true),
                $estado,
                array("cep","id_estado")
            );

            $cidades = cidadeModel::getByEstado($dadoEndereco->id_estado?:24);

            foreach ($cidades as $cidade){
                $elements->addOption($cidade->id,$cidade->nome);
            }
    
            $form->setDoisInputs(
                $elements->select("Cidade","id_cidade",$dadoEndereco->id_cidade?:4487,true),
                $elements->input("bairro","Bairro",$dadoEndereco->bairro,true),
                array("bairro","id_cidade")
            );
            $form->setDoisInputs(
                $elements->input("rua","Rua",$dadoEndereco->rua,true),
                $elements->input("numero","Numero",$dadoEndereco->numero,true,false,"","number","form-control",'min="0" max="999999"'),
                array("rua","numero")
            );
            $form->setInputs(
                $elements->textarea("complemento","Complemento",$dadoEndereco->complemento,true),"complemento"
            );
        }
      
        $form->setButton($elements->button("Salvar","submit"));
        if ($login)
            $form->setButton($elements->button("Voltar","voltar","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."login'"));
        else 
            $form->setButton($elements->button("Voltar","voltar","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."cadastro/index/".functions::encrypt($tipo_usuario)."'"));

        $form->show();

        $footer = new footer;
        $footer->show();
    }
    public function action($parameters = array(),$login=""){

        $tipo_usuario = functions::decrypt($this->getValue('tipo_usuario'));
       
        if (!$tipo_usuario && $login)
            $this->go("login/index");
        elseif(!$tipo_usuario)
            $this->go("cadastro/index");

        $cd = functions::decrypt($this->getValue('cd'));
        $nome = $this->getValue('nome');
        $cpf_cnpj = $this->getValue('cpf_cnpj');
        $senha = $this->getValue('senha');
        $email = $this->getValue('email');
        $telefone = $this->getValue('telefone');
        if ($tipo_usuario != 2){
            $cep = $this->getValue('cep');
            $id_estado = $this->getValue('id_estado');
            $id_cidade = $this->getValue('id_cidade');
            $bairro = $this->getValue('bairro');
            $rua = $this->getValue('rua');
            $numero = $this->getValue('numero');
            $complemento = $this->getValue('complemento');
        }
        if ($tipo_usuario == 1){
            $nome_empresa = $this->getValue('nome_empresa');
            $razao = $this->getValue('razao');
            $fantasia = $this->getValue('fantasia');
            $id_empresa = empresaModel::set($nome_empresa,$cpf_cnpj,$email,$telefone,$razao,$fantasia);
            if ($id_empresa){
                $id_usuario = usuarioModel::set($nome,$cpf_cnpj,$email,$telefone,$senha,$cd,$tipo_usuario,$id_empresa);
                if ($id_usuario){
                    $id_endereco = enderecoModel::set($cep,$id_estado,$id_cidade,$bairro,$rua,$numero,$complemento,"",$id_usuario,$id_empresa);
                    if ($id_endereco){
                        if ($login)
                            $this->go("login/index/".functions::encrypt($cpf_cnpj)."/".functions::encrypt($senha));
                        else 
                            $this->go("cadastro/index/".functions::encrypt($tipo_usuario));
                    }else
                        usuarioModel::delete($id_usuario);
                }else
                    empresaModel::delete($id_empresa);
            }
        }
        elseif ($tipo_usuario == 2){
            $id_grupo_funcionario = $this->getValue('id_grupo_funcionario');
            $hora_ini = $this->getValue('hora_ini');
            $hora_fim = $this->getValue('hora_fim');
            $hora_almoco_ini = $this->getValue('hora_almoco_ini');
            $hora_almoco_fim = $this->getValue('hora_almoco_fim');
               
            $dias = implode(",",[$this->getValue("dom"),$this->getValue("seg"),$this->getValue("ter"),$this->getValue("qua"),$this->getValue("qui"),$this->getValue("sex"),$this->getValue("sab")]);

            if ($id_empresa = functions::decrypt($this->getValue("id_empresa"))){
                $id_usuario = usuarioModel::set($nome,$cpf_cnpj,$email,$telefone,$senha,$cd,$tipo_usuario,$id_empresa);
                if ($id_usuario){
                    $id_funcionario = functions::decrypt($this->getValue("id_funcionario"));
                    $id_funcionario = funcionarioModel::set($id_usuario,$nome,$cpf_cnpj,$email,$telefone,$hora_ini,$hora_fim,$hora_almoco_ini,$hora_almoco_fim,$dias,$id_funcionario);
                    if($id_funcionario){
                        if ($id_grupo_funcionario && $id_funcionario)
                            funcionarioModel::setFuncionarioGrupoFuncionario($id_grupo_funcionario,$id_funcionario);
                        if ($login)
                            $this->go("login/index/".functions::encrypt($cpf_cnpj)."/".functions::encrypt($senha));
                        else 
                            $this->go("cadastro/index/".functions::encrypt($tipo_usuario));
                    }
                    usuarioModel::delete($id_usuario);
                }
            }
        }
        elseif ($tipo_usuario == 3){ 
            $id_usuario = usuarioModel::set($nome,$cpf_cnpj,$email,$telefone,$senha,$cd,$tipo_usuario);
                if ($id_usuario){
                    $id_endereco = enderecoModel::set($cep,$id_estado,$id_cidade,$bairro,$rua,$numero,$complemento,"",$id_usuario);
                    if($id_endereco){
                        if ($login)
                            $this->go("login/index/".functions::encrypt($cpf_cnpj)."/".functions::encrypt($senha));
                        else 
                            $this->go("cadastro/index/".functions::encrypt($tipo_usuario));
                    }
                    usuarioModel::delete($id_usuario);
                }
        }
        if ($login)
            $this->go("login/cadastro/".functions::encrypt($tipo_usuario));
        else 
            $this->go("cadastro/manutencao/".functions::encrypt($tipo_usuario)."/".functions::encrypt($cd)); 
    }

    public function filter(){
        $this->go("cadastro/index/".functions::encrypt("2")."/".functions::encrypt($this->getValue("grupo_funcionario")));
    }

    public function massActionAgenda(){

        $qtd_list = $this->getValue("qtd_list");
        $id_agenda = $this->getValue("agenda");

        if ($qtd_list && $id_agenda){
            for ($i = 1; $i <= $qtd_list; $i++) {
                if($id_servico = $this->getValue("id_check_".$i)){
                    funcionarioModel::setAgendaFuncionario($id_servico,$id_agenda);
                }
            }
        }

        $this->go("funcionario");
    }

    public function massActionGrupoFuncionario(){

        $qtd_list = $this->getValue("qtd_list");
        $id_grupo_funcionario = $this->getValue("grupo_funcionario");

        if ($qtd_list && $id_grupo_funcionario){
            for ($i = 1; $i <= $qtd_list; $i++) {
                if($id_funcionario = $this->getValue("id_check_".$i)){
                    funcionarioModel::setFuncionarioGrupoFuncionario($id_funcionario,$id_grupo_funcionario);
                }
            }
        }

        $this->go("servico");
    }

}

