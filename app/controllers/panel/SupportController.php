<?php


namespace app\controllers\panel;

use app\core\PanelController;


class SupportController extends PanelController {


    public function indexAction(){

        $content = 'В разработке';

        $this->view->render('Поддержка', $content);
    }

}