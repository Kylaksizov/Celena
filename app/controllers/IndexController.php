<?php

namespace app\controllers;

use app\controllers\classes\CustomProducts;
use app\controllers\classes\Functions;
use app\core\Controller;


class IndexController extends Controller {


    public function indexAction(){

        Functions::preTreatment($this);


        // если тег ля вывода продуктов присутствует
        $products = '';
        if($this->view->findTag('{CONTENT}', 1)){
            $Products = new CustomProducts();
            $products = $Products->get($this, 1, 'index', 'products');
        }

        $this->view->setMain('{CONTENT}', $products);
        $this->view->clear();

        $this->view->setMain('{crumbs}', '');



        $this->view->setMeta('Panel', 'CRM система для автоматизации бизнес процессов', [
            [
                'property' => 'og:title',
                'content' => 'NEX CRM',
            ],
            [
                'property' => 'og:description',
                'content' => 'CRM система для автоматизации бизнес процессов',
            ]
        ]);

        $this->view->render(false);

        Functions::scanTags($this);
        $this->view->display();
    }

}