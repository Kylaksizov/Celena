<?php

namespace app\plugins\Celena\Example;

use app\controllers\classes\Functions;
use app\core\Controller;


class IndexController extends Controller{


    public function indexAction(){

        // $this->plugin->celena
        // $this->plugin->config



        // если передан AJAX
        if($this->ajax){
            echo 'Все получилось!';
            exit;
        }


        
        Functions::preTreatment($this);

        $this->view->include('page');

        $this->view->setMain('{crumbs}', "");

        $content = '<div class="fx jc_c">
            <a href="#" data-a="Test:example=1" class="btn">Первая 1</a>&nbsp;
            <a href="#" data-a="Test:example=2" class="btn">Вторая 2</a>&nbsp;
            <a href="#" data-a="Test:param=1&param=2" class="btn">Вторая 3</a>
        </div>';

        $this->view->set('{title}', "Название страницы плагина");
        $this->view->set('{content}', 'Содержимое плагина<br><br>' . $content);
        $this->view->setMain('{CONTENT}', $this->view->get());


        $this->view->setMeta("Example plugin", "This is example plugin", [
            [
                'property' => 'og:title',
                'content' => "Example plugin",
            ],
            [
                'property' => 'og:description',
                'content' => "This is example plugin",
            ]
        ]);

        $this->view->render(false);

        Functions::scanTags($this);
        $this->view->display();
    }

}