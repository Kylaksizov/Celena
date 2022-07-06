<?php


namespace app\controllers\panel;

use app\core\PanelController;
use app\core\system\shop\ShopController;


class CelenaUpdatesController extends PanelController {


    public function indexAction(){

        $this->view->styles = ['css/celenaUpdate.css'];
        $this->view->scripts = ['js/celenaUpdate.js'];

        $content = ShopController::getUpdate();

        if(!$content){

            $content = '<h1>Обновление системы Celena</h1>
                <div class="box_ tc">
                    <p style="background:#81c937;color:#fff;padding:7px 10px;display:inline-block;border-radius:2px;">Вы используете последнюю версию <b> '.CONFIG_SYSTEM["version"].'</b></p>
                    <br><br>
                    <p>обновление не требуется.</p>
                </div>';
        }

        $this->view->render('Обновления Celena', $content);
    }

}