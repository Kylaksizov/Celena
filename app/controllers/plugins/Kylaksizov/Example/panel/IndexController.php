<?php

namespace app\controllers\plugins\Kylaksizov\Example\panel;

use app\core\PanelController;

class IndexController extends PanelController{


    public function __construct($route, $ajax){
        parent::__construct($route, $ajax);
    }

    
    public function indexAction(){

        $this->view->include('leads');

        $this->view->setMeta('Лиды');

        $this->view->render();
    }

}