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



        $this->view->setMeta(CONFIG_SYSTEM["site_title"], CONFIG_SYSTEM["site_description"], [
            [
                'property' => 'og:title',
                'content' => CONFIG_SYSTEM["site_title"],
            ],
            [
                'property' => 'og:description',
                'content' => CONFIG_SYSTEM["site_description"],
            ]
        ]);

        $this->view->render(false);

        Functions::scanTags($this);
        $this->view->display();
    }

}