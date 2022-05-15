<?php

namespace app\controllers;

use app\controllers\classes\CustomProducts;
use app\controllers\classes\Functions;
use app\core\Controller;
use Exception;


class SearchController extends Controller {


    /**
     * @throws Exception
     */
    public function indexAction(){

        $this->view->include('search');


        $this->view->setMain('{crumbs}', '<div id="crumbs"><a href="' . CONFIG_SYSTEM["home"] . '">' . CONFIG_SYSTEM["site_title"] . '</a>' . CONFIG_SYSTEM["separator"] . '<span>Поиск</span></div>');

        $result = 'Ничего не найдено!';


        // если тег ля вывода продуктов присутствует
        if(!empty($_GET["str"])){
            $_GET["search"] = trim(htmlspecialchars(stripslashes($_GET["str"]))); // repeat
            $Products = new CustomProducts();
            $result = $Products->custom($this, 'index', 'products');
        }

        $this->view->set('{result}', $result);

        $this->view->setMeta('Поиск', 'Поиск', [
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