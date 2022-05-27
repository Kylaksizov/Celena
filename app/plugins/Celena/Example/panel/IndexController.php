<?php

namespace app\plugins\Celena\Example\panel;

use app\core\PanelController;

class IndexController extends PanelController{


    public function indexAction(){

        //$this->view->include('index');

        $this->view->styles = ['css/style.css'];
        $this->view->scripts = ['js/script.js'];
        
        if($this->ajax) die($this->ajax);

        $content = '<div class="fx jc_c">
            <a href="#" data-a="Test:example=1" class="btn">Первая 1</a>&nbsp;
            <a href="#" data-a="Test:example=2" class="btn">Вторая 2</a>&nbsp;
            <a href="#" data-a="Test:param=1&param=2" class="btn">Вторая 3</a>
        </div>';

        $this->view->render('Example Plugin', $content);
    }

}