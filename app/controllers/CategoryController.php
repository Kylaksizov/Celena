<?php

namespace app\controllers;

use app\controllers\classes\Custom;
use app\controllers\classes\Functions;
use app\core\Controller;
use app\core\View;
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
            $content = $Custom->get($this, true, end($this->urls));

            if(empty($content)){
                header("Location: ".CONFIG_SYSTEM["home"]."/404/");
                View::errorCode(404);
            }

            $this->view->setMain('{CONTENT}', $content);
            $this->view->clear();
        }

        $this->view->render(false);

        Functions::scanTags($this);
        $this->view->display();
    }

}