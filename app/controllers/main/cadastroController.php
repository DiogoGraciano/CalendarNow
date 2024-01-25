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

        $cadastro->addColumns("1","Id","id")->addColumns("15","CPF/CNPJ","cpf_cnpj")->addColumns("15","Email","email")->addColumns("10","Telefone","telefone");

        
        if ($tipo_usuario == 1 || $tipo_usuario == 0){
            $cadastro->addColumns("20","CPF/CNPJ Empresa","cnpj")->addColumns("20","Empresa","nome_empresa");
            $dados = empresaModel::getEmpresa();
        }
        if ($tipo_usuario == 2){
            $cadastro->addColumns("10","Grupo de Funcionarios","grupo_funcionario")
            ->addColumns("10","Grupo de Servicos","grupo_servico")
            ->addColumns("10","Hora de Inicio","hora_ini")
            ->addColumns("10","Hora de Fim","hora_fim")
            ->addColumns("15","Dias","dia");
            $dados = funcionarioModel::getListFuncionariosByEmpresa($user->id_empresa);
        }

        $cadastro->addColumns("14","Ações","acoes");

        $cadastro->show($this->url."cadastro/manutencao/".functions::encrypt($tipo_usuario),$this->url."cadastro/action/",$buttons,$dados);
      
        $footer = new footer;
        $footer->show();
    }
    public function manutencao($parameters = array(),$login=False){

        $head = new head();
        $head->show("Cadastro","");

        $cd = "";
        $tipo_usuario = "";
        if ($login)
            $form = new form($this->url."login/save");
        else
            $form = new form($this->url."cadastro/action");

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

        $dado = usuarioModel::get($cd,$tipo_usuario);

        $elements = new elements;

        $form->setDoisInputs(
            $elements->input("nome","Nome",$dado->nome,true),
            $elements->input("cpf_cnpj","CPF/CNPJ:",$dado->cpf_cnpj,true),
            array("nome","cpf_cnpj")
        );

        $form->setTresInputs(
            $elements->input("email","Email",$dado->email,true,false,"","email"),
            $elements->input("senha","Senha","",true,false,"","password"),
            $elements->input("telefone","Telefone",$dado->telefone,true),
            array("email","senha","telefone")
        );
      
        if ($tipo_usuario == 1){
            $form->setTresInputs(
                $elements->input("nome_empresa","Nome da Empresa",$dado->nome,true),
                $elements->input("fantasia","Nome Fantasia",$dado->nome,true),
                $elements->input("razao","Razao Social:",$dado->cpf_cnpj,true),
                array("nome_empresa","fantasia","razao")
            );
        }

        if ($tipo_usuario == 2){
            $form->setDoisInputs(
                $elements->select("Grupo de Funcionarios","id_grupo_funcionario",$elements->getOptions("grupo_funcionario","id","nome"),$dado->id_estado?:24,true),
                $elements->select("Grupo de Servicos","id_grupo_servico",$elements->getOptions("grupo_funcionario","id","nome"),$dado->id_estado?:24,true),
                array("id_grupo_funcionario","id_grupo_servico")
            );

            $dias = [
                $form->getCustomInput(2,$elements->checkbox("dom","Domingo",false,false,false,"dom"),"dom"),
                $form->getCustomInput(2,$elements->checkbox("seg","Segunda",false,false,false,"seg"),"seg"),
                $form->getCustomInput(2,$elements->checkbox("ter","Terça",false,false,false,"ter"),"ter"),
                $form->getCustomInput(2,$elements->checkbox("qua","Quarta",false,false,false,"qua"),"qua"),
                $form->getCustomInput(2,$elements->checkbox("qui","Quinta",false,false,false,"qui"),"qui"),
                $form->getCustomInput(2,$elements->checkbox("sex","Sexta",false,false,false,"sex"),"sex"),
                $form->getCustomInput(2,$elements->checkbox("sab","Sabado",false,false,false,"sab"),"sab"),
            ];
    
            $form->setDoisInputs(
                $elements->input("hora_ini","Hora Inicial de Trabalho","",true,false,"","time"),
                $elements->input("hora_fim","Hora Final de Trabalho","",true,false,"","time"),
                array("hora_ini","hora_fim")
            );

            $form->setInputs(
                $elements->label("Dias de trabalho na Semana")
            );

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
        $form->setButton($elements->button("Voltar","voltar","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."login'"));
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
                        $this->go("cadastro/index");

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
        }
        elseif ($tipo_usuario == 3){ 
            $id_usuario = usuarioModel::set($nome,$cpf_cnpj,$email,$telefone,$senha,$cd,$tipo_usuario);
            if ($id_usuario){
                $id_endereco = enderecoModel::set($cep,$id_estado,$id_cidade,$bairro,$rua,$numero,$complemento,"",$id_usuario);
                if($id_endereco){
                    if ($login)
                        $this->go("login/index/".functions::encrypt($cpf_cnpj)."/".functions::encrypt($senha));
                    else 
                        $this->go("cadastro/index");
                }
                usuarioModel::delete($id_usuario);
            }
        }
        if ($login)
            $this->go("login/cadastro/".functions::encrypt($tipo_usuario));
        else 
            $this->go("cadastro/manutencao/".functions::encrypt($tipo_usuario)); 
    }

}

