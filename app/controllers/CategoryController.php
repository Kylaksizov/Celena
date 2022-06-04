<?php

namespace app\controllers;

use app\controllers\classes\Custom;
use app\controllers\classes\Functions;
use app\core\Controller;
use Exception;


class CategoryController extends Controller {


    /**
     * @throws Exception
     */
    public function indexAction(){

        Functions::preTreatment($this);

        // если тег ля вывода продуктов присутствует
        if($this->view->findTag('{CONTENT}', 1)){
            $Custom = new Custom();
            $news = $Custom->get($this, true, end($this->urls));
            $this->view->setMain('{CONTENT}', $news);
            $this->view->clear();
        }

        $this->view->render(false);

        Functions::scanTags($this);
        $this->view->display();
    }

}