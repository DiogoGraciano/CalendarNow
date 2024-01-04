<?php 
namespace app\controllers\main;
use app\classes\head;
use app\classes\form;
use app\classes\elements;
use app\classes\controllerAbstract;
use app\classes\footer;
use app\models\main\usuarioModel;
use app\models\main\enderecoModel;
use app\models\main\cidadeModel;
use app\models\main\empresaModel;

class cadastroController extends controllerAbstract{

    public function index(){

    }
    public function manutencao($parameters = array(),$login=False){

        $head = new head();
        $head->show("Cadastro","");

        $cd = "";
        $tipo_usuario = "";


        if (array_key_exists(0,$parameters))
            $tipo_usuario = $parameters[0];
        else{
            if ($login)
                $this->go("login/index");
            else 
                $this->go("cadastro/index");
        }

        if (array_key_exists(1,$parameters))
            $cd = $parameters[1];

        $dado = usuarioModel::get($cd,$tipo_usuario);

        if ($login)
            $form = new form("LOGO",$this->url."login/save");
        else
            $form = new form("LOGO",$this->url."cadastro/action");

        $elements = new elements;

        $form->setHidden("cd",$cd);
        $form->setHidden("tipo_usuario",$tipo_usuario);

        $form->setDoisInputs(
            $elements->input("nome","Nome",$dado->nome,true),
            $elements->input("cpf_cnpj","CPF/CNPJ:",$dado->cpf_cnpj,true),
            array("nome","cpf_cnpj")
        );

        $form->setTresInputs(
            $elements->input("email","Email",$dado->email,true,false,"","email"),
            $elements->input("senha","Senha","",true,false,"","password"),
            $elements->input("telefone","Telefone","",true),
            array("email","senha","telefone")
        );
        if ($tipo_usuario == "agenda"){
            $form->setTresInputs(
                $elements->input("nome_empresa","Nome da Empresa",$dado->nome,true),
                $elements->input("fantasia","Nome Fantasia",$dado->nome,true),
                $elements->input("razao","Razao Social:",$dado->cpf_cnpj,true),
                array("nome_empresa","fantasia","razao")
            );
        }

        if ($tipo_usuario == "funcionario"){
            $form->setDoisInputs(
                $elements->select("Grupo de Funcionarios","id_grupo_funcionario",$elements->getOptions("grupo_funcionario","id","nome"),$dado->endereco->id_estado?:24,true),
                $elements->select("Grupo de Servicos","id_grupo_servico",$elements->getOptions("grupo_funcionario","id","nome"),$dado->endereco->id_estado?:24,true),
                array("id_grupo_funcionario","id_grupo_servico")
            );

            $dias = [
                $form->getCustomInput(2,$elements->checkbox("dom","Domingo"),"dom"),
                $form->getCustomInput(2,$elements->checkbox("seg","Segunda"),"seg"),
                $form->getCustomInput(2,$elements->checkbox("ter","Terça"),"ter"),
                $form->getCustomInput(2,$elements->checkbox("qua","Quarta"),"qua"),
                $form->getCustomInput(2,$elements->checkbox("qui","Quinta"),"qui"),
                $form->getCustomInput(2,$elements->checkbox("sex","Sexta"),"sex"),
                $form->getCustomInput(2,$elements->checkbox("sab","Sabado"),"sab"),
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

        if ($tipo_usuario != "funcionario"){
            $form->setDoisInputs(
                $elements->input("cep","CEP",$dado->endereco->cep,true),
                $elements->select("Estado","id_estado",$elements->getOptions("estado","id","nome"),$dado->endereco->id_estado?:24,true),
                array("cep","id_estado")
            );
            $form->setDoisInputs(
                $elements->select("Cidade","id_cidade",cidadeModel::getOptionsbyEstado($dado->endereco->id_estado?:24),$dado->endereco->id_cidade?:4487,true),
                $elements->input("bairro","Bairro",$dado->endereco->bairro,true),
                array("bairro","id_cidade")
            );
            $form->setDoisInputs(
                $elements->input("rua","Rua",$dado->endereco->rua,true),
                $elements->input("numero","Numero",$dado->endereco->cep,true,false,"","number","form-control",'min="0" max="999999"'),
                array("rua","numero")
            );
            $form->setInputs(
                $elements->textarea("complemento","Complemento",$dado->endereco->complemento,true),"complemento"
            );
        }
      
        $form->setButton($elements->button("Salvar","submit"));
        $form->setButton($elements->button("Voltar","voltar","button","btn btn-primary w-100 pt-2 btn-block","location.href='".$this->url."login'"));
        $form->show();

        $footer = new footer;
        $footer->show();
    }
    public function action($parameters = array(),$login=""){

        $tipo_usuario = $this->getValue('tipo_usuario');
       
        if ($tipo_usuario == "agenda" || $tipo_usuario == "usuario"){
            $cd = $this->getValue('cd');
            $nome = $this->getValue('nome');
            $cpf_cnpj = $this->getValue('cpf_cnpj');
            $senha = $this->getValue('senha');
            $email = $this->getValue('email');
            $telefone = $this->getValue('telefone');
            $cep = $this->getValue('cep');
            $id_estado = $this->getValue('id_estado');
            $id_cidade = $this->getValue('id_cidade');
            $bairro = $this->getValue('bairro');
            $rua = $this->getValue('rua');
            $numero = $this->getValue('numero');
            $complemento = $this->getValue('complemento');
            if ($tipo_usuario == "usuario")
                $tipo_usuario = 3;
            else{
                $tipo_usuario = 1;
                $nome_empresa = $this->getValue('nome_empresa');
                $razao = $this->getValue('razao');
                $fantasia = $this->getValue('fantasia');
            }
            
        }
        elseif ($tipo_usuario == "funcionario"){
            $tipo_usuario = 2;
            $id_grupo_funcionario = $this->getValue('id_grupo_funcionario');
            $id_grupo_servico = $this->getValue('id_grupo_servico');
            $hora_ini = $this->getValue('hora_ini');
            $hora_fim = $this->getValue('hora_fim');
        }
        else{ 
            if ($login)
                $this->go("login/index");
            else 
                $this->go("cadastro/index");
        }

        $id_usuario = usuarioModel::set($nome,$cpf_cnpj,$email,$telefone,$senha,$cd,$tipo_usuario);
        if ($id_usuario && $tipo_usuario == 3){
            $id_endereco = enderecoModel::set($cep,$id_estado,$id_cidade,$bairro,$rua,$numero,$complemento,"",$id_usuario);
            if ($id_endereco){
                if ($login)
                    $this->go("login/index/".base64_encode($cpf_cnpj)."/".base64_encode($senha));
                else 
                    $this->go("cadastro/index");
            }
            else
                usuarioModel::delete($id_usuario);
        }
        elseif ($id_usuario && $tipo_usuario == 2){
           
        }
        elseif ($id_usuario && $tipo_usuario == 1){
            $id_empresa = empresaModel::set($nome_empresa,$cpf_cnpj,$razao,$fantasia);
            if ($id_empresa){
                $id_endereco = enderecoModel::set($cep,$id_estado,$id_cidade,$bairro,$rua,$numero,$complemento,"","",$id_empresa);
                if ($id_endereco){
                    if ($login)
                        $this->go("login/index/".base64_encode($cpf_cnpj)."/".base64_encode($senha));
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
        else{
            if ($login)
                $this->go("login/cadastro/".$tipo_usuario);
            else 
                $this->go("cadastro/manutencao/".$tipo_usuario);

        }
    }

}

