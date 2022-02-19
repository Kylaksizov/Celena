<?php

namespace app\controllers;

use app\core\Controller;


class CategoryController extends Controller {



    public function indexAction(){

        //$this->view->load('Nex');

        $this->view->include('products');
        $this->view->set('{menu-title}', 'Главная');
        $this->view->setMain('{menu}', $this->view->get());

        $this->view->setMeta('Категория', 'CRM система для автоматизации бизнес процессов', [
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