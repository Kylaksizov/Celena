<?php


namespace app\controllers\panel;

use app\core\PanelController;


class SupportController extends PanelController {


    public function indexAction(){

        $content = 'В разработке<br><br><a href="#" class="btn btn-danger">Опасная кнопка - тоже в разработке!</a>';

        $this->view->render('Поддержка', $content);
    }

}