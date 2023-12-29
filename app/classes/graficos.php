<?php 
    
    namespace app\classes;
    use app\classes\pagina;

    class grafico extends pagina{

        function __construct($datax,$datay,$chartName,$type="line",$class="",$fill="false",$legends='false',$title="",$backColor='random',$borderColor='rgb(0,0,0)')
        {
            $this->getTemplate("../templates/graficos_template.html",true);

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
        private function genereteColors($qtd=1){

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
