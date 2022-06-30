<?php

namespace app\controllers;

use app\controllers\classes\Custom;
use app\controllers\classes\Functions;
use app\controllers\PageController;
use app\core\Controller;


class IndexController extends Controller {


    public function indexAction(){

        Functions::preTreatment($this);
        


        // если тег ля вывода продуктов присутствует
        if(CONFIG_SYSTEM["main"] == 1 && $this->view->findTag('{CONTENT}', 1)){

            $Custom = new Custom();
            $news = $Custom->get($this, 1, 'index');
            $this->view->setMain('{CONTENT}', $news);

        } else if(CONFIG_SYSTEM["main"] == 2 && !empty(CONFIG_SYSTEM["main_content"])){

            // определяем константу с именем страницы
            define("PAGE_TITLE", CONFIG_SYSTEM["main_content"]);

            $PageController = new PageController($this->route, false);
            $PageController->indexAction();

        } else if(CONFIG_SYSTEM["main"] == 3 && !empty(CONFIG_SYSTEM["main_content"])){

            $this->view->include(CONFIG_SYSTEM["main_content"]);
            $this->view->setMain('{CONTENT}', $this->view->get());
        }

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