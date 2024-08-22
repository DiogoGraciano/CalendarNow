<?php

namespace app\controllers\main;

use app\controllers\abstract\controller;
use app\view\layout\error;
use app\view\layout\footer;
use app\view\layout\head;

final class errorController extends controller
{
    public function index($parameters = [],$code = 404,$message = "A Pagina que está procurando não existe")
    {
        $head = new head();
        $head->show("Erro","","");

        $error = new error;
        $error->show($code,$message);

        $footer = new footer;
        $footer->show();
    }
}