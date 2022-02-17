<?php


namespace app\controllers\panel;

use app\core\PanelController;


class SupportController extends PanelController {


    public function indexAction(){

        $content = '';

        /*$this->view->include('');
        $this->view->set('{}', $content);

        $this->view->setMain('{tag}', $this->view->get());*/

        $this->view->render('');
    }

}