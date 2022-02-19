<?php

namespace app\controllers;

use app\core\Controller;


class ProductController extends Controller {



    public function indexAction(){

        //$this->view->load('Nex');

        $this->view->include('product');
        $this->view->set('{menu-title}', 'Главная');
        $this->view->setMain('{menu}', $this->view->get());

        $this->view->setMeta('Продукты', 'CRM система для автоматизации бизнес процессов', [
            [
                'property' => 'og:title',
                'content' => 'NEX CRM',
            ],
            [
                'property' => 'og:description',
                'content' => 'CRM система для автоматизации бизнес процессов',
            ]
        ]);

        $this->view->render();
    }

}