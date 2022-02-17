<?php


namespace app\controllers\panel;

use app\core\PanelController;


class OrdersController extends PanelController {


    public function indexAction(){

        $content = '';

        /*$this->view->include('');
        $this->view->set('{}', $content);

        $this->view->setMain('{tag}', $this->view->get());*/

        $this->view->render('Заказы');
    }


    public function clickAction(){

        $content = '';

        /*$this->view->include('');
        $this->view->set('{}', $content);

        $this->view->setMain('{tag}', $this->view->get());*/

        $this->view->render('Заказы');
    }

}