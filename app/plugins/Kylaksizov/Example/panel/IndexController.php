<?php

namespace app\plugins\Kylaksizov\Example\panel;

use app\core\PanelController;

class IndexController extends PanelController{


    public function indexAction(){

        $this->view->include('index');

        $this->view->render('Example Plugin');
    }

}