<?php


namespace app\controllers\panel;

use app\core\PanelController;
use app\core\system\shop\ShopController;


class CelenaPluginsController extends PanelController {


    public function indexAction(){

        $this->view->styles = ['css/celenaShop.css'];
        $this->view->scripts = ['js/celenaShop.js'];
        $this->view->plugins = ['rating', 'fancybox'];

        $content = ShopController::getPlugins();

        $this->view->render('Магазин плагинов и модулей', $content);
    }

}