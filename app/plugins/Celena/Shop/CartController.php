<?php

namespace app\plugins\Celena\Shop;

use app\core\Controller;
use app\plugins\Celena\Shop\classes\Functions;
use Exception;


class CartController extends Controller {


    /**
     * @throws Exception
     */
    public function indexAction(){

        $this->view->styles = ['css/shop.css'];
        $this->view->scripts = ['js/shop.js'];

        $this->view->include('cart');

        Functions::preTreatment($this);


        $this->view->setMain('{crumbs}', '<div id="crumbs"><a href="' . CONFIG_SYSTEM["home"] . '">' . CONFIG_SYSTEM["site_title"] . '</a>' . CONFIG_SYSTEM["separator"] . '<span>Корзина</span></div>');

        $this->view->set('{cart}', '<div id="cart_ordering" class="w100"></div>');
        $this->view->set('{name}', !empty(USER["name"]) ? USER["name"] : '');
        $this->view->set('{email}', !empty(USER["email"]) ? USER["email"] : '');


        $this->view->setMain('{CONTENT}', $this->view->get());

        $this->view->setMeta('Оформление заказа', 'Страница оформления заказа', [
            [
                'property' => 'og:title',
                'content' => 'Оформление заказа',
            ],
            [
                'property' => 'og:description',
                'content' => 'Страница оформления заказа',
            ]
        ]);

        $this->view->render(false);

        Functions::scanTags($this);
        $this->view->display();
    }



    private static function ReintCategory($categories, $parent_id){

        $parent_isset = false;
        $result = '<ul class="sub_cat_menu">';
        foreach ($categories as $row) {
            // если id родительской категории = parent_id и у неё parent_id не 0
            if(($parent_id == $row["parent_category"]) && ($row["parent_category"] > 0)){
                $parent_isset = true;
                $result .= '<li>'.$row["id"].' '.$row["title"];
                $result .= self::ReintCategory($categories, $row["id"]);
                $result .= '</li>';
            }
        }
        $result .= '</ul>';
        if($parent_isset == true) {
            return $result;
        }
        return '';
    }

}