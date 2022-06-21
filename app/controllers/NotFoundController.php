<?php

namespace app\controllers;

use app\controllers\classes\Functions;
use app\core\Controller;


class NotFoundController extends Controller{

    public function indexAction(){

        Functions::preTreatment($this);

        $this->view->include('notFound');

        $this->view->setMain('{crumbs}', '<div id="crumbs"><a href="//'.CONFIG_SYSTEM["home"].'/">'.CONFIG_SYSTEM["site_title"].'</a></div>');

        $this->view->setMain('{CONTENT}', $this->view->get());
        $this->view->clear();

        $this->view->setMeta('404 Not Found!', '404 Not Found!');

        $this->view->render();
    }

}