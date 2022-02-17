<?php

namespace app\controllers;

use app\core\Controller;


class BalanceController extends Controller {



    public function indexAction(){

        //$this->view->load('Nex');

        $this->view->include('balance');
        $this->view->set('{menu-title}', 'Главная');
        $this->view->setMain('{menu}', $this->view->get());

        $this->view->render('Баланс');
    }

}