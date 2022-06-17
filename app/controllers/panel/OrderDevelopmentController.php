<?php


namespace app\controllers\panel;

use app\core\PanelController;


class OrderDevelopmentController extends PanelController {


    public function indexAction(){

        $content = '<h1>Фриланс в разработке</h1>';

        $this->view->render('Фриланс', $content);
    }

}