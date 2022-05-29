<?php


namespace app\controllers\panel;

use app\core\PanelController;
use app\core\system\shop\ShopController;


class CelenaUpdatesController extends PanelController {


    public function indexAction(){

        $this->view->styles = ['css/celenaUpdate.css'];
        $this->view->scripts = ['js/celenaUpdate.js'];

        $content = ShopController::getUpdate();

        $this->view->render('Обновления Celena', $content);
    }

}