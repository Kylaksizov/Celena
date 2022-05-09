<?php


namespace app\controllers\panel;

use app\core\PanelController;


class SettingsController extends PanelController {


    public function indexAction(){

        $content = '<h1>Общие настройки</h1>';

        $content .= '<form action="" method="POST">
            <div class="tabs">
                <ul class="tabs_caption">
                    <li class="active">Общие</li>
                    <li>---</li>
                </ul>
                <div class="tabs_content active">
                    <div class="dg settings">
                        <div class="set_item">
                            <div>
                                <h3>Знак валюты</h3>
                            </div>
                            <div>
                                <input type="text" name="config[currency]" value="$">
                            </div>
                        </div>
                        <div class="set_item">
                            <div>
                                <h3>Название настройки</h3>
                            </div>
                            <div>
                                <input type="checkbox" name="name" value="1" id="ch">
                                <label for="ch"></label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- tab SEO -->
                <div class="tabs_content">
                    ---
                </div>
                
            </div>
            
            <input type="submit" class="btn" data-a="Admin" value="Сохранить">
            
        </form>';

        $this->view->render('Настройки '.CONFIG_SYSTEM["separator"].' Общие настройки', $content);
    }


    public function seoAction(){

        $content = '<h1>SEO</h1>';

        $this->view->render('Настройки '.CONFIG_SYSTEM["separator"].' SEO', $content);
    }


    public function promoCodesAction(){

        $content = '<h1>Промокоды</h1>';

        $this->view->render('Настройки '.CONFIG_SYSTEM["separator"].' Промокоды', $content);
    }


    public function langAction(){

        $content = '<h1>Языки</h1>';

        $this->view->render('Настройки '.CONFIG_SYSTEM["separator"].' Языки', $content);
    }


    public function currencyAction(){

        $content = '<h1>Валюта</h1>';

        $this->view->render('Настройки '.CONFIG_SYSTEM["separator"].' Валюта', $content);
    }


    public function paymentMethodsAction(){

        $content = '<h1>Способы оплаты</h1>';

        $this->view->render('Настройки '.CONFIG_SYSTEM["separator"].' Способы оплаты', $content);
    }


    public function deliveryMethodsAction(){

        $content = '<h1>Способы доставки</h1>';

        $this->view->render('Настройки '.CONFIG_SYSTEM["separator"].' Способы доставки', $content);
    }

}