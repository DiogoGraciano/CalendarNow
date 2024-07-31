<?php

namespace app\layout;

use app\layout\abstract\pagina;
use app\helpers\mensagem;
use app\helpers\functions;
use app\layout\elements;

/**
 * Classe consulta representa uma consulta de dados.
 *
 * Esta classe estende a classe pagina e implementa funcionalidades específicas para exibir uma consulta de dados em uma tabela.
 */
class consulta extends pagina
{
    /**
     * @var array $buttons Array que armazena os botões a serem exibidos na consulta.
     */
    private array $buttons = [];


    /**
     * @var object $table irá conter a classe tabela || tabelaMobile.
    */
    private tabelaMobile|tabela $table;

    /**
     * @var bool $massaction verifica se tem massation.
    */
    private bool $massaction = false;


    public function __construct(bool $massaction = false)
    {
        // Cria uma instância da tabela com base no dispositivo
        $this->table = $this->isMobile() ? new tabelaMobile : new tabela;

        $this->massaction = $massaction;

        // Adiciona colunas à tabela
        if ($this->massaction) 
            $this->table->addColumns("1", $this->isMobile() ? "Selecionar" : "","massaction");
        
    }

    /**
     * Exibe a consulta de dados em uma tabela.
     *
     * @param string $pagina_manutencao URL da página de manutenção.
     * @param string $pagina_action URL da página de ação.
     * @param bool|array $dados Array de dados a serem exibidos na tabela.
     * @param string $coluna_action Nome da coluna que contém a ação (Editar/Excluir).
     * @param bool $checkbox Indica se a coluna de checkbox deve ser exibida.
     */
    public function show(string $pagina_manutencao,string $pagina_action,null|bool|array $dados,string $coluna_action = "id")
    {
        // Carrega o template de consulta
        $this->tpl = $this->getTemplate("consulta_template.html");
        
        // Instancia a classe mensagem para exibir mensagens
        $mensagem = new mensagem;
        $this->tpl->mensagem = $mensagem->show(false);
        $this->tpl->pagina_manutencao = $pagina_manutencao;

        // Adiciona botões ao template
        foreach ($this->buttons as $button) {
            $this->tpl->button = $button;
            $this->tpl->block("BLOCK_BUTTONS");
        }

        // Popula a tabela com os dados fornecidos
        if ($dados) {
            $i = 0;
            foreach ($dados as $data) {
                if(is_subclass_of($data,"app\db\db")){
                    $data = $data->getArrayData();
                }

                if(array_key_exists($coluna_action,$data)){

                    if($this->massaction)
                        $data["massaction"] = (new elements)->checkbox("id_check_" . ($i + 1), false, false, false, false, $data[$coluna_action]);

                    $data["acoes"]  = '<button type="button" class="btn btn btn-primary">
                                                <a href="' . $pagina_manutencao . '/' . functions::encrypt($data[$coluna_action]) . '">Editar</a>
                                                </button>
                                                <button class="btn btn btn-primary" onclick="confirmaExcluir()" type="button">
                                                    <a href="' . $pagina_action . '/' . functions::encrypt($data[$coluna_action]) . '">Excluir</a>
                                                </button>';
                }

                $this->table->addRow($data);
                $i++;
            }

            $this->tpl->qtd_list = $i;
            $this->tpl->table = $this->table->parse();
        } else {
            $this->tpl->block('BLOCK_SEMDADOS');
        }

        // Exibe o template
        $this->tpl->show();
    }

    /**
     * Adiciona uma coluna à tabela.
     *
     * @param string|int $width Largura da coluna em porcentagem.
     * @param string $nome Nome da coluna.
     * @param string $coluna Nome da coluna associada aos dados.
     * @return $this
     */
    public function addColumns(string|int $width,string $nome,string $coluna)
    {
        $this->table->addColumns($width,$nome,$coluna);
        return $this;
    }

    /**
     * Adiciona um botão à consulta.
     *
     * @param string $button Botão a ser adicionado.
     * @return $this
     */
    public function addButtons(string $button)
    {
        $this->buttons[] = $button;
        return $this;
    }
}

?>
