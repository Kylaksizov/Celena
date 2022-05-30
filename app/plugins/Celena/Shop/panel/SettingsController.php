<?php


namespace app\plugins\Celena\Shop\panel;

use app\core\PanelController;
use app\core\System;


class SettingsController extends PanelController {


    public function indexAction(){

        $content = '<h1>Настройки магазина</h1>';

        $content .= '<form action="" method="POST">
            <div class="tabs">
                <ul class="tabs_caption">
                    <li class="active">Общие настройки</li>
                    <li>Корзина</li>
                </ul>
                <div class="tabs_content active">
                    <div class="dg settings">
                        <div class="set_item">
                            <div>
                                <h3>Знак валюты</h3>
                            </div>
                            <div>
                                <input type="text" name="config[currency]" value="'.$this->plugin->config->currency.'">
                            </div>
                        </div>
                        <div class="set_item">
                            <div>
                                <h3>Количество знаков в ID товара</h3>
                            </div>
                            <div>
                                <input type="text" name="config[str_pad_id]" value="'.$this->plugin->config->str_pad_id.'">
                            </div>
                        </div>
                        <div class="set_item">
                            <div>
                                <h3>Количество товаров на главной и в категориях</h3>
                            </div>
                            <div>
                                <input type="text" name="config[count_prod_by_cat]" value="'.$this->plugin->config->count_prod_by_cat.'">
                            </div>
                        </div>
                        <div class="set_item">
                            <div>
                                <h3>Редирект после оформления заказа</h3>
                                <div class="setDescription">Можно указать как относительный путь, так и любую ссылку.</div>
                            </div>
                            <div>
                                <input type="text" name="config[after_cart]" value="'.$this->plugin->config->after_cart.'">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- tab Cart -->
                <div class="tabs_content">
                    <div class="dg settings">
                        <div class="set_item">
                            <div>
                                <h3>Тип номера заказа</h3>
                            </div>
                            <div>
                                <select name="config[cart_id]">
                                    <option value="int">Порядковый номер</option>
                                    <option value="rand"'.($this->plugin->config->cart_id=='rand'?' selected':'').'>Произвольное число</option>
                                    <option value="standard"'.($this->plugin->config->cart_id=='standard'?' selected':'').'>С произвольным префиксом</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
            
            <input type="submit" class="btn" data-a="Settings" value="Сохранить">
            
        </form>';

        $this->view->render('Настройки '.CONFIG_SYSTEM["separator"].' Общие настройки', $content);
    }


    public function promoCodesAction(){

        $content = '<h1>Промокоды</h1>';

        $this->view->render('Настройки '.CONFIG_SYSTEM["separator"].' Промокоды', $content);
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