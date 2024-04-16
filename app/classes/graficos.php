<?php
namespace app\classes;
use app\classes\pagina;

/**
 * Classe responsável por gerar gráficos dinâmicos utilizando um template HTML.
 * Esta classe herda de `pagina`, que fornece funcionalidades básicas para a criação de páginas HTML.
 */
class grafico extends pagina{

    /**
     * Construtor da classe `grafico`.
     *
     * @param array $datax Os dados para o eixo x do gráfico.
     * @param array $datay Os dados para o eixo y do gráfico.
     * @param string $chartName Nome do gráfico.
     * @param string $type Tipo do gráfico (line, bar, horizontalBar, pie, doughnut).
     * @param string $class Classe CSS adicional para o gráfico.
     * @param bool $fill Define se a área sob o gráfico deve ser preenchida.
     * @param bool $legends Define se os legendas devem ser exibidas.
     * @param string $title Título do gráfico.
     * @param string $backColor Cor de fundo do gráfico.
     * @param string $borderColor Cor da borda do gráfico.
     */
    function __construct(array $datax,array $datay,string $chartName,string $type = "line",string $class = "",bool $fill,bool $legends,bool $title,string $backColor = 'random',string $borderColor = 'rgb(0,0,0)')
    {
        $this->getTemplate("../templates/graficos_template.html", true);

        if ($type == "line" 
            || $type == "bar" 
            || $type == "horizontalBar" 
            || $type == "pie" 
            || $type == "doughnut"){

            if (!$title)
                $displayTitle = 'false';
            else 
                $displayTitle = 'true';

            if(is_string($backColor))
                $backColor = '"'.$backColor.'"';

            if(is_string($borderColor))
                $borderColor = '"'.$borderColor.'"';

            if ($backColor == '"random"')
                $backColor = json_encode($this->genereteColors(count($datax)));

            if ($borderColor == '"random"')
                $borderColor = json_encode($this->genereteColors(count($datax)));
            
            $this->tpl->datax = json_encode($datax);
            $this->tpl->datay = json_encode($datay);
            $this->tpl->backColor = $backColor;
            $this->tpl->borderColor = $borderColor;
            $this->tpl->chartName = $chartName;
            $this->tpl->type = $type;
            $this->tpl->legends = $legends;
            $this->tpl->displayTitle = $displayTitle;
            $this->tpl->title = $title;
            $this->tpl->fill = $fill;
            $this->tpl->class = $class;

            $this->tpl->block("BLOCK_GRAFICO");

            $this->tpl->show();
        }
    }

    /**
     * Gera cores aleatórias para os gráficos.
     *
     * @param int $qtd Quantidade de cores a serem geradas.
     * @return array Um array contendo as cores geradas no formato 'rgb(r,g,b)'.
     */
    private function genereteColors(int $qtd = 1){

        $array = [];

        if ($qtd > 0){
            $i = 0;
            while($i < $qtd){ 
                $r = rand(0, 255);
                $g = rand(0, 255);
                $b = rand(0, 255);

                $array[] = 'rgb('.$r.','.$g.','.$b.')';

                $i++;
            }
        }
        return ($array);
    }
}
?>