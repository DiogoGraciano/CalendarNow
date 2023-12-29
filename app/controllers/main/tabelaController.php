<?php 
namespace app\controllers\main;
use app\classes\head;
use app\db\db;
use app\classes\form;
use app\classes\mensagem;
use app\classes\menu;
use app\classes\footer;
use app\classes\controllerAbstract;
use Exception;

class tabelaController extends controllerAbstract{

    private $tabela;

    const nomes = [
        "cd_cliente" => "Código do Cliente",
        "nm_cliente" => "Nome do Cliente",
        "nr_loja" => "Número da Loja",
        "cd_conexao" => "Código de Conexão",
        "id_conexao" => "ID de Conexão",
        "nm_terminal" => "Nome do Terminal",
        "nm_programa" => "Nome do Programa",
        "nm_usuario" => "Nome do Usuário",
        "senha" => "Senha",
        "obs" => "Observação",
        "nr_caixa" => "Número do Caixa",
        "cd_login" => "Código de Login",
        "nm_usuario" => "Nome de Usuário",
        "cd_ramal" => "Código do Ramal",
        "nm_funcionario" => "Nome do Funcionário",
        "nr_ramal" => "Número do Ramal",
        "nr_telefone" => "Número do Telefone",
        "nr_ip" => "Número IP",
        "nm_usuario" => "Nome de Usuário",
        "cd_usuario" => "Código do Usuário",
        "nm_usuario" => "Nome do Usuário",
        "nm_sistema" => "Nome do Sistema",
        "nm_terminal" => "Nome do Terminal"
    ];

    private function getNomeAmigavel($coluna){
        if (array_key_exists($coluna,$this::nomes))
            return $this::nomes[$coluna];
        else 
            return $coluna;
    }

    private function getNomeColuna($coluna){
        if ($key = array_search($coluna,$this::nomes))
            return $key;
        else 
            return False;
    }

    public function index(){
        $head = new head();
        $head->show("Importar/Exportar","");

        $menu = new menu();
        $menus = array($menu->getMenu($this->url."tabela/tabela_exportar/","Exportar"),
                       $menu->getMenu($this->url."tabela/tabela_importar/","Importar"),
                       $menu->getMenu($this->url,"Voltar"));
        
        $menu->show("Selecione o Modo",$menus);

        $footer = new footer;
        $footer->show();
    }

    public function tabela_importar(){
        $head = new head();
        $head->show("Selecione a tabela","");

        $menu = new menu();
        $menus = array($menu->getMenu($this->url."tabela/importar/tb_cliente","Cliente"),
                       $menu->getMenu($this->url."tabela/Importar/tb_conexao","Conexão"),
                       $menu->getMenu($this->url."tabela/importar/tb_usuario","Usuario"),
                       $menu->getMenu($this->url."tabela/importar/tb_ramal","Ramal"),
                       $menu->getMenu($this->url."tabela","Voltar"));
        
        $menu->show("Selecione a tabela",$menus);

        $footer = new footer;
        $footer->show();
    }

    public function tabela_exportar(){
        $head = new head();
        $head->show("Selecione a tabela","");

        $menu = new menu();
        $menus = array($menu->getMenu($this->url."tabela/exportar/tb_cliente","Cliente"),
                       $menu->getMenu($this->url."tabela/exportar/tb_conexao","Conexão"),
                       $menu->getMenu($this->url."tabela/exportar/tb_usuario","Usuario"),
                       $menu->getMenu($this->url."tabela/exportar/tb_ramal","Ramal"),
                       $menu->getMenu($this->url."tabela","Voltar"));
        
        $menu->show("Selecione a tabela",$menus);

        $footer = new footer;
        $footer->show();
    }

    public function exportar($tabela=array()){

        if (array_key_exists(0,$tabela)){
            $this->tabela = $tabela[0];

            $head = new head();
            $head->show("Exportação","");

            $form = new form("Selecione as Colunas Desejadas",$this->url."tabela/action/exportar/".$tabela[0]);
            
            $db = new db($this->tabela);
            $colunas = $db->getColumns();

            $customs = [];

            if ($colunas){
                $i = 0;
                $checked = true;
                $readonly = true;
                foreach ($colunas as $coluna){
                    $customs[] = $form->getCustomInput(2,$form->checkbox($coluna,$this->getNomeAmigavel($coluna),false,$checked,$readonly));
                    if ($i == 0){
                        $i++;
                        $checked = false;
                        $readonly = false;
                    }
                    
                }
                
                $customs = array_chunk($customs,6);
                
                $form->setCustomInputs($customs);
            }

            $form->setButton($form->button("Executar","btn_carregar","submit"));
            $form->setButtonNoForm($form->button("Voltar","btn_voltar","submit","btn btn-dark pt-2 btn-block","location.href='".$this->url."tabela/tabela_exportar'"));
            $form->show();

            $footer = new footer;
            $footer->show();

        }else{
            mensagem::setErro(array("Tabela não informada"));
            header("location: ".$this->url."tabela/tabela_exportar");
        }
    }

    public function importar($tabela=array()){

        if (array_key_exists(0,$tabela)){
            $this->tabela = $tabela[0];
            $head = new head();
            $head->show("Importação","");

            $form = new form("Adicione o Arquivo para Importação",$this->url."tabela/action/importar/".$tabela[0]);

                $form->setInputs($form->input("arquivo","Arquivo para Importação:","",false,false,"","file"));

                $form->setButton($form->button("Executar","btn_carregar","submit"));
                $form->setButtonNoForm($form->button("Voltar","btn_voltar","buttom","btn btn-dark pt-2 btn-block","location.href='".$this->url."tabela/tabela_importar'"));
                $form->show();
                $footer = new footer;
                $footer->show();
        }
        else{
            mensagem::setErro(array("Tabela não informada"));
            header("location: ".$this->url."tabela/tabela_importar");
        }
    }
    
    public function action($tabela=array()){
        try{
            if (array_key_exists(0,$tabela)){
                if ($tabela[0] == "exportar"){
                    if (array_key_exists(1,$tabela)){
                        $db = new db($tabela[1]);
                        $colunas = $db->getColumns();

                        $colunas_selecionada = [];
                        $culunas_amigaveis = [];

                        foreach ($colunas as $coluna){
                            if ($_POST[$coluna]){
                                $colunas_selecionada[] = $coluna;
                                $culunas_amigaveis[] = $this->getNomeAmigavel($coluna);
                            }
                        }

                        $resultados = $db->selectColumns($colunas_selecionada);

                        if ($resultados){

                            if (!file_exists("Arquivos/")){
                                mkdir("Arquivos/",777);
                            }

                            $arquivo  = fopen('Arquivos/Relatorio.csv', "w");
                            $nome = "Relatorio.csv";

                            fputcsv($arquivo, $culunas_amigaveis);

                            foreach ($resultados as $resultado){
                                fputcsv($arquivo, (array)$resultado,";");
                            }

                            fclose($arquivo);

                            $arquivoLocal = 'Arquivos/'.$nome; 
                            
                            if (!file_exists($arquivoLocal)) {
                                Mensagem::setErro(array("Não foi possivel gerar o relatorio"));
                                return $this->url."exportar";
                            }
                            Mensagem::setSucesso(array("Exportado com sucesso"));
                            header("location: ".$this->url."arquivos/Relatorio.csv");
                        }
                        else {
                            mensagem::setErro(array("Nenhum registro encontrado"));
                            header("location: ".$this->url."tabela/exportar/".$tabela[1]);
                        }
                    }
                    else{
                        mensagem::setErro(array("Tabela não informada"));
                        header("location: ".$this->url."tabela/tabela_importar");
                    }

                }
                elseif ($tabela[0] == "importar"){
                    $tmpName = $_FILES["arquivo"]["tmp_name"];
                    if(($arquivo = fopen($tmpName, 'r')) !== FALSE) {
                        $i = 0;
                        $rows = [];
                        while (($data = fgetcsv($arquivo, 1000, ";")) !== FALSE) {
                            $rows[] = $data;
                        } 
                            $db = new db($tabela[1]);
                            $colunas_db = $db->getColumns();
                            $colunas = [];
                            $tb = $db->getObject();
                            $r = 1;
                            foreach ($rows as $data){
                                $c = 0;
                                foreach ($data as $coluna){
                                    if ($i == 0){
                                        $colunas[] = $this->getNomeColuna($coluna);
                                        continue;
                                    } 
                                    if (in_array($colunas[$c],$colunas_db)){
                                        $col = $colunas[$c];
                                        $tb->$col = $coluna;
                                        $c++;
                                    }
                                    else {
                                        Mensagem::setErro(array("Coluna ({$this->getNomeAmigavel($colunas[$c])}) não encontrada na Tabela {$tabela[1]} na linha ".$r));
                                        header("location: ".$this->url."tabela/tabela_importar");
                                        return;
                                    }
                                    $db->store($tb);  
                                }
                                $r++;
                                $i++;
                            }
                            
                            fclose($arquivo);

                            Mensagem::setSucesso(array("Exportado com sucesso"));
                            header("location: ".$this->url."tabela/tabela_importar");
                        }
                    }else{
                        Mensagem::setErro(array("Não foi possivel fazer a leitura do arquivo, tente novamente."));
                        header("location: ".$this->url."tabela/tabela_importar");
                    }
            }else{
                Mensagem::setErro(array("Modo não informado"));
                header("location: ".$this->url."tabela/tabela_importar");
            }
        }
        catch (Exception $e){
            Mensagem::setErro(array($e->getMessage()));
            return $this->url."tabela";
        }
    }
}