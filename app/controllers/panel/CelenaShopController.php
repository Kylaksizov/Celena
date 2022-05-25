<?php


namespace app\controllers\panel;

use app\core\PanelController;
use app\core\system\shop\ShopController;


class CelenaShopController extends PanelController {


    public function indexAction(){

        $this->view->styles = ['css/addon/celena_shop.css'];
        $this->view->scripts = ['js/addon/celena_shop.js'];
        $this->view->plugins = ['rating', 'fancybox'];

        $content = ShopController::getPlugins();

        $this->view->render('', $content);
    }





    public function templatesAction(){

        $content = '';

        /*$this->view->include('');
        $this->view->set('{}', $content);

        $this->view->setMain('{tag}', $this->view->get());*/

        $this->view->render('');
    }


    public function pluginsAction(){

        $content = '';

        /*$this->view->include('');
        $this->view->set('{}', $content);

        $this->view->setMain('{tag}', $this->view->get());*/

        $this->view->render('');
    }


    public function modulesAction(){

        $content = '';

        /*$this->view->include('');
        $this->view->set('{}', $content);

        $this->view->setMain('{tag}', $this->view->get());*/

        $this->view->render('');
    }


    public function orderDevelopmentAction(){

        $content = '';

        /*$this->view->include('');
        $this->view->set('{}', $content);

        $this->view->setMain('{tag}', $this->view->get());*/

        $this->view->render('');
    }

}