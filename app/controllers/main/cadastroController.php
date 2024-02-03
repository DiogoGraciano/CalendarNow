<?php 
namespace app\controllers\main;
use app\classes\head;
use app\classes\form;
use app\classes\elements;
use app\classes\controllerAbstract;
use app\classes\consulta;
use app\classes\footer;
use app\classes\functions;
use app\models\main\usuarioModel;
use app\models\main\enderecoModel;
use app\models\main\cidadeModel;
use app\models\main\empresaModel;
use app\models\main\funcionarioModel;

class cadastroController extends controllerAbstract{

    public function index($parameters){

        $head = new head();
        $head -> show("agendas","consulta");

        $tipo_usuario = "";

        if (array_key_exists(0,$parameters)){
            $tipo_usuario = functions::decrypt($parameters[0]);
        }

        $elements = new elements;

        $user = usuarioModel::getLogged();

        $buttons = [$elements->button("Voltar","voltar","button","btn btn-primary","location.href='".$this->url."opcoes'")]; 

        $cadastro = new consulta();

        $cadastro->addColumns("1","Id","id")
                ->addColumns("10","CPF/CNPJ","cpf_cnpj")
                ->addColumns("15","Nome","nome")
                ->addColumns("15","Email","email")
                ->addColumns("11","Telefone","telefone");

        
        if ($tipo_usuario == 1 || $tipo_usuario == 0){
            $cadastro->addColumns("10","CPF/CNPJ Empresa","cnpj")->addColumns("15","Empresa","nome_empresa");
            $dados = empresaModel::getEmpresa();
        }
        if ($tipo_usuario == 2){
            $cadastro->addColumns("10","Grupo de Funcionarios","grupo_funcionario")
                    ->addColumns("10","Grupo de Servicos","grupo_servico")
                    ->addColumns("5","Inicio Expediente","hora_ini")
                    ->addColumns("5","Fim Expediente","hora_fim")
                    ->addColumns("5","Inicio Almoço","hora_almoco_ini")
                    ->addColumns("5","Fim Almoço","hora_almoco_fim")
                    ->addColumns("15","Dias","dia");
            $dados = funcionarioModel::getListFuncionariosByEmpresa($user->id_empresa);
        }

        $cadastro->addColumns("14","Ações","acoes");

        $cadastro->show($this->url."cadastro/manutencao/".functions::encrypt($tipo_usuario),$this->url."cadastro/action/",$buttons,$dados);
      
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

        if ($tipo_usuario == 2){
            $dadoFuncionario = funcionarioModel::get($cd);
            $dado = usuarioModel::get($dadoFuncionario->id_usuario);
            $form->setHidden("id_funcionario",functions::encrypt($dadoFuncionario->id));
            $form->setHidden("id_empresa",functions::encrypt($user->id_empresa));
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
                $elements->input("nome_empresa","Nome da Empresa",$dado->nome,true),
                $elements->input("fantasia","Nome Fantasia",$dado->nome,true),
                $elements->input("razao","Razao Social:",functions::formatCnpjCpf($dado->cnpj),true),
                array("nome_empresa","fantasia","razao")
            );
        }

        if ($tipo_usuario == 2){
            $form->setDoisInputs(
                $elements->select("Grupo de Funcionarios","id_grupo_funcionario",$elements->getOptions("grupo_funcionario","id","nome"),$dadoFuncionario->id_grupo_funcionario),
                $elements->select("Grupo de Servicos","id_grupo_servico",$elements->getOptions("grupo_funcionario","id","nome"),$dadoFuncionario->id_grupo_servico),
                array("id_grupo_funcionario","id_grupo_servico")
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

            $dias = [
                $form->getCustomInput(2,$elements->checkbox("dom","Domingo",false,isset($checkDias[0]) && $checkDias[0]?true:false,false,"dom"),"dom"),
                $form->getCustomInput(2,$elements->checkbox("seg","Segunda",false,isset($checkDias[1]) && $checkDias[1]?true:false,false,"seg"),"seg"),
                $form->getCustomInput(2,$elements->checkbox("ter","Terça",false,isset($checkDias[2]) && $checkDias[2]?true:false,false,"ter"),"ter"),
                $form->getCustomInput(2,$elements->checkbox("qua","Quarta",false,isset($checkDias[3]) && $checkDias[3]?true:false,false,"qua"),"qua"),
                $form->getCustomInput(2,$elements->checkbox("qui","Quinta",false,isset($checkDias[4]) && $checkDias[4]?true:false,false,"qui"),"qui"),
                $form->getCustomInput(2,$elements->checkbox("sex","Sexta",false,isset($checkDias[5]) && $checkDias[5]?true:false,false,"sex"),"sex"),
                $form->getCustomInput(2,$elements->checkbox("sab","Sabado",false,isset($checkDias[6]) && $checkDias[6]?true:false,false,"sab"),"sab"),
            ];
    

            $form->setCustomInputs($dias);

        }

        if ($tipo_usuario != 2){
            $form->setDoisInputs(
                $elements->input("cep","CEP",$dado->cep,true),
                $elements->select("Estado","id_estado",$elements->getOptions("estado","id","nome"),$dado->id_estado?:24,true),
                array("cep","id_estado")
            );
            $form->setDoisInputs(
                $elements->select("Cidade","id_cidade",cidadeModel::getOptionsbyEstado($dado->id_estado?:24),$dado->id_cidade?:4487,true),
                $elements->input("bairro","Bairro",$dado->bairro,true),
                array("bairro","id_cidade")
            );
            $form->setDoisInputs(
                $elements->input("rua","Rua",$dado->rua,true),
                $elements->input("numero","Numero",$dado->numero,true,false,"","number","form-control",'min="0" max="999999"'),
                array("rua","numero")
            );
            $form->setInputs(
                $elements->textarea("complemento","Complemento",$dado->complemento,true),"complemento"
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
            $id_empresa = empresaModel::set($nome_empresa,$cpf_cnpj,$razao,$fantasia);
            $id_usuario = usuarioModel::set($nome,$cpf_cnpj,$email,$telefone,$senha,$cd,$tipo_usuario,$id_empresa);
            if ($id_empresa && $id_usuario){
                $id_endereco = enderecoModel::set($cep,$id_estado,$id_cidade,$bairro,$rua,$numero,$complemento,"","",$id_empresa);
                if ($id_endereco){
                    if ($login)
                        $this->go("login/index/".functions::encrypt($cpf_cnpj)."/".functions::encrypt($senha));
                    else 
                        $this->go("cadastro/index/".functions::encrypt($tipo_usuario));

                }else{
                    empresaModel::delete($id_empresa);
                    usuarioModel::delete($id_usuario);
                }
            }
            else{
                usuarioModel::delete($id_usuario);
            }
        }
        elseif ($tipo_usuario == 2){
            $id_grupo_funcionario = $this->getValue('id_grupo_funcionario');
            $id_grupo_servico = $this->getValue('id_grupo_servico');
            $hora_ini = $this->getValue('hora_ini');
            $hora_fim = $this->getValue('hora_fim');
            $hora_almoco_ini = $this->getValue('hora_almoco_ini');
            $hora_almoco_fim = $this->getValue('hora_almoco_fim');
               
            $dias = implode(",",[$this->getValue("dom"),$this->getValue("seg"),$this->getValue("ter"),$this->getValue("qua"),$this->getValue("qui"),$this->getValue("sex"),$this->getValue("sab")]);

            if ($id_empresa = functions::decrypt($this->getValue("id_empresa"))){
                $id_usuario = usuarioModel::set($nome,$cpf_cnpj,$email,$telefone,$senha,$cd,$tipo_usuario,$id_empresa);
                if ($id_usuario){
                    $id_funcionario = functions::decrypt($this->getValue("id_funcionario"));
                    $id_endereco = funcionarioModel::set($id_usuario,$id_grupo_funcionario,$id_grupo_servico,$hora_ini,$hora_fim,$hora_almoco_ini,$hora_almoco_fim,$dias,$id_funcionario);
                    if($id_endereco){
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

}

