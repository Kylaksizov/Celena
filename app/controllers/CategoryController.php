<?php

namespace app\controllers;

use app\core\Controller;
use app\models\PropertyModel;


class CategoryController extends Controller {



    public function indexAction(){

        //$this->view->load('Nex');

        $ProductModel = new PropertyModel();

        $this->view->include('products');

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