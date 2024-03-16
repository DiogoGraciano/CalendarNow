<?php

namespace app\classes;
use app\classes\pagina;
use app\classes\mensagem;

class agenda extends pagina{

    private $buttons;
    
    public function show($action,$eventos,$days_off=",seg,ter,qua,qui,sex,",$initial_time = "08:00",$final_time = "19:00",$dinner_start="12:00",$dinner_end="13:00",$slot_duration = 30)
    {
        $this->tpl = $this->getTemplate("agenda_template.html");
        $mensagem = new mensagem;
        $this->tpl->mensagem = $mensagem->show(false);
        $this->tpl->action = $action;
        $this->tpl->initial_time = $initial_time;

        $time = explode(":",$final_time);
        $lastTime = intval($time[1]);
        if ($slot_duration > $lastTime)
            $lastTime = "00";
        
        $this->tpl->final_time = $time[0].":".$lastTime;

        if ($slot_duration >= 60)
            $this->tpl->slot_duration = "01:00";
        else 
            $this->tpl->slot_duration = "00:".$slot_duration;


        $days_off = str_replace("dom",0,$days_off);
        $days_off = str_replace("seg",1,$days_off);
        $days_off = str_replace("ter",2,$days_off);
        $days_off = str_replace("qua",3,$days_off);
        $days_off = str_replace("qui",4,$days_off);
        $days_off = str_replace("sex",5,$days_off);
        $days_off = str_replace("sab",6,$days_off);

        $days = explode(",",$days_off);

        $daysOffFinal = [];
        $daysOn = [];

        foreach ($days as $key => $value){
            $alldays = [0,1,2,3,4,5,6];
            if (!in_array($value,$alldays))
                $daysOffFinal[] = $alldays[$key];
            else 
                $daysOn[] = $alldays[$key];
        } 

        $dinnerStop = [];

        $dinnerStop[] =  [
            'daysOfWeek' => $daysOn, 
            'startTime' => $dinner_start,
            'endTime' => $dinner_end,
            'title' => "Almoço",
            'color' => "#000",
        ];

        $this->tpl->events = json_encode(array_merge($eventos,$dinnerStop));

        $this->tpl->days_off = json_encode($daysOffFinal);

        $date = new \DateTimeImmutable();
        $this->tpl->initial_date = $date->format(\DateTimeInterface::ISO8601);

        foreach ($this->buttons as $button){
            $this->tpl->button = $button;
            $this->tpl->block("BLOCK_BUTTON");
        }
        $this->tpl->block("BLOCK_CALENDARIO");
        $this->tpl->show();
    }

    public function addButton($button){
        $this->buttons[] = $button;
    }
}
