<?php

namespace app\controllers;

use app\core\Controller;
use app\models\ProductModel;
use Exception;


class CategoryController extends Controller {


    /**
     * @throws Exception
     */
    public function indexAction(){

        //$this->view->load('Nex');

        $ProductModel = new ProductModel();
        $Products = $ProductModel->getProducts($this->urls);

        echo "<pre>";
        print_r($Products);
        echo "</pre>";
        exit;

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