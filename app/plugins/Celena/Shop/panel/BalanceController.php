<?php

namespace app\plugins\Celena\Shop\panel;

use app\core\PanelController;


class BalanceController extends PanelController {



    public function indexAction(){

        //$this->view->load('Nex');

        $this->view->include('balance');
        $this->view->set('{menu-title}', 'Главная');
        $this->view->setMain('{menu}', $this->view->get());

        $this->view->render('Баланс');
    }

}