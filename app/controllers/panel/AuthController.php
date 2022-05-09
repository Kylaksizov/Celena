<?php


namespace app\controllers\panel;

use app\core\PanelController;


class AuthController extends PanelController {


    public function indexAction(){
        $this->view->load('PanelAuth');
        $this->view->render('Авторизация в панели');
    }

}