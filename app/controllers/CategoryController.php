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

        $this->view->include('products');

        // подбор полей из таблиц в зависимости от заданных тегов - эффективно, если большая база
        #TODO потом можно сделать проверку из конфига
        $findTags = $this->view->findTags([
            '{sid}',
            '{rating}',
            '{test}'
        ]);

        $Products = $ProductModel->getProducts($this->urls);
        
        echo "<pre>";
        print_r($Products);
        echo "</pre>";
        exit;

        /*echo "<pre>";
        print_r($this->view->include["products"]);
        echo "</pre>";
        exit;*/

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