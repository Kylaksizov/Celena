<?php

namespace app\controllers;

use app\controllers\classes\CustomProducts;
use app\controllers\classes\Functions;
use app\core\Controller;
use Exception;


class CategoryController extends Controller {


    /**
     * @throws Exception
     */
    public function indexAction(){

        // если тег ля вывода продуктов присутствует
        if($this->view->findTag('{CONTENT}', 1)){
            $Products = new CustomProducts();
            $products = $Products->get($this, true, end($this->urls), 'products');
            $this->view->setMain('{CONTENT}', $products);
            $this->view->clear();
        }


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

        $this->view->render(false);

        Functions::scanTags($this);
        $this->view->display();
    }

}