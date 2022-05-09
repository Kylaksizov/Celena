<?php


namespace app\controllers\panel;

use app\core\PanelController;


class SupportController extends PanelController {


    public function indexAction(){

        $content = '';

        $this->view->render('Поддержка');
    }

}