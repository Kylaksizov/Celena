<?php


namespace app\controllers\panel;

use app\core\PanelController;


class SupportController extends PanelController {


    public function indexAction(){

        $content = '<h1>Поддержка</h1>
            <div class="box_">В разработке<br><br>Будьте вежливы и терпеливы в ожидании ответа иначе Вы больше не сможете получать обновления.<br>Спасибо за понимание!
            <br><br>
            <a href="#" class="btn btn-danger">Опасная кнопка - тоже в разработке!</a></div>';

        $this->view->render('Поддержка', $content);
    }

}