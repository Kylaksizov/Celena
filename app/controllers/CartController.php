<?php

namespace app\controllers;

use app\core\Controller;
use Exception;


class CartController extends Controller {


    /**
     * @throws Exception
     */
    public function indexAction(){

        $this->view->include('cart');


        $this->view->setMain('{crumbs}', '<div id="crumbs"><a href="' . CONFIG_SYSTEM["home"] . '">' . CONFIG_SYSTEM["site_title"] . '</a>' . CONFIG_SYSTEM["separator"] . '<span>Корзина</span></div>');

        $this->view->set('{cart}', '<div id="cart_ordering" class="w100"></div>');
        $this->view->set('{name}', !empty(USER["name"]) ? USER["name"] : '');
        $this->view->set('{email}', !empty(USER["email"]) ? USER["email"] : '');




        $this->view->setMeta('Категория', 'CRM система для автоматизации бизнес процессов', [
            [
                'property' => 'og:title',
                'content' => 'NEX CRM',
            ],
            [
                'property' => 'og:description',
                'content' => 'CRM система для автоматизации бизнес процессов',
            ]
        ]);

        $this->view->render();
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