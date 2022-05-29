<?php

namespace app\controllers\panel;

use app\core\PanelController;


class NotFoundController extends PanelController{

    public function indexAction(){

        $this->view->include('notFound');
        $this->view->set('{menu-title}', 'Страница не найдена');

        $this->view->render('Страница не найдена');
    }

}