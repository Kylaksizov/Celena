<?php


namespace app\controllers\panel;

use app\core\PanelController;


class GoodsController extends PanelController {


    public function indexAction(){

        $content = '';

        /*$this->view->include('');
        $this->view->set('{}', $content);

        $this->view->setMain('{tag}', $this->view->get());*/

        $this->view->render('Товары');
    }


    public function categoriesAction(){

        $content = '<h1>Категории товаров</h1>';

        $this->view->render('Товары', $content);
    }

}