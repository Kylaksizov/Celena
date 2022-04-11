<?php

namespace app\controllers;

use app\core\Controller;
use app\core\System;
use app\models\ProductModel;


class ProductController extends Controller {



    public function indexAction(){

        $ProductModel = new ProductModel();

        $this->view->include('product');

        $fieldsQuery = [
            'p.id, p.uid AS author_id, p.title, p.content, p.m_title, p.m_description, p.category, p.price',
            '{sale}'       => 'p.sale',
            '[sale]'       => 'p.sale',
            '{old-price}'  => 'p.sale',
            '{stock}'      => 'p.stock',
            '{vendor}'     => 'p.vendor',
            '{date}'       => 'p.created',
            '{poster}'     => 'p.poster',
            '{brand-id}'   => 'p.brand AS brand_id',
            '{brand-name}' => 'b.name AS brand_name',
            '{brand-url}'  => 'b.url AS brand_url',
            '{brand-icon}' => 'b.icon AS brand_icon',
            '{images}'     => '1',
            '{properties}' => '2',
        ];

        $findTags = $this->view->findTags($fieldsQuery);

        $url = trim(end($this->urls), CONFIG_SYSTEM["seo_type_end"]);

        if(CONFIG_SYSTEM["seo_type"] == '2' || CONFIG_SYSTEM["seo_type"] == '4'){
            
            preg_match('/^([0-9]+)\-(.+?)$/is', $url, $urlParams);
            if(!empty($urlParams[1]) && is_numeric($urlParams[1])){

                unset($this->urls[count($this->urls)-1]);
                $url = [
                    'id' => intval($urlParams[1]),
                    'url' => trim(htmlspecialchars(strip_tags($urlParams[2]))),
                    'categories' => $this->urls
                ];
            }
        }

        $Product = $ProductModel->get($url, $findTags);



        /*$CategoryStep = System::setKeys($Product["categories"], "url");

        // CRUMBS
        if(count($CategoryStep) > 1){

            $crumbs = '<a href="' . CONFIG_SYSTEM["home"] . '">' . CONFIG_SYSTEM["site_title"] . '</a>';

            $addLink = CONFIG_SYSTEM["home"];
            foreach ($CategoryStep as $row) {

                $addLink .= $row["url"].'/';
                $crumbs .= CONFIG_SYSTEM["separator"] . '<a href="' . $addLink . '">' . $row["title"] . '</a>';
            }

        } else $crumbs = '<a href="' . CONFIG_SYSTEM["home"] . '">' . CONFIG_SYSTEM["site_title"] . '</a>' . CONFIG_SYSTEM["separator"] . $CategoryStep[end($this->urls)]["title"];

        $this->view->setMain('{crumbs}', $crumbs);*/


        $poster = 'no-image.png';
        if(!empty($Product["product"]["src"])){
            $poster = $Product["product"]["src"];
        } else if(!empty($Product["images"][$Product["product"]["poster"]]["src"])){
            $poster = $Product["images"][$Product["product"]["poster"]]["src"];
        }


        $this->view->set('{id}', !empty(CONFIG_SYSTEM["str_pad_id"]) ? str_pad($Product["product"]["id"], CONFIG_SYSTEM["str_pad_id"], '0', STR_PAD_LEFT) : $Product["product"]["id"]);

        if($this->view->findTag('{vendor}'))
            $this->view->set('{vendor}', !empty(CONFIG_SYSTEM["str_pad_vendor"]) ? str_pad($Product["product"]["vendor"], CONFIG_SYSTEM["str_pad_vendor"], '0', STR_PAD_LEFT) : $Product["product"]["vendor"]);

        //$this->view->set('{link}', $link);
        $this->view->set('{title}', $Product["product"]["title"]);

        $Product["product"]["price"] = $price = round($Product["product"]["price"]);

        if(!empty($Product["product"]["sale"])){

            if(is_numeric($Product["product"]["sale"])){

                $price = round($price - intval($Product["product"]["sale"]), 2);
                $Product["product"]["sale"] .= CONFIG_SYSTEM["currency"];

            } else if(strripos($Product["product"]["sale"], "%") !== false){

                $price = round($price - (($price / 100) * trim($Product["product"]["sale"], "%")));
            }
        }

        $this->view->set('{price}', $price);
        $this->view->set('{old-price}', $Product["product"]["price"]);
        $this->view->set('{stock}', !empty($Product["product"]["stock"]) ? $Product["product"]["stock"] : '');

        $this->view->set('{currency}', CONFIG_SYSTEM["currency"]);
        $this->view->set('{poster}', CONFIG_SYSTEM["home"].'uploads/products/'.$poster);


        $data_goods = [
            "id" => $Product["product"]["id"],
            "title" => $Product["product"]["title"],
            //"link"  => $link,
            "price" => $Product["product"]["price"],
            "image" => $poster,
        ];
        $data_goods = json_encode($data_goods, JSON_UNESCAPED_UNICODE);

        $this->view->set('{images}', '');
        $this->view->set('{rating}', '');
        $this->view->set('{description}', '');
        $this->view->set('{buy}', '<a href="/cart.html" class="ks_buy" data-goods=\''.$data_goods.'\'>Купить</a>');
        $this->view->set('{add-cart}', '<a href="#" class="ks_add_cart" data-goods=\''.$data_goods.'\' title="Добавить в корзину"></a>');

        if(!empty($Product["product"]["sale"])){

            $this->view->setPreg('/\[no-sale\](.*?)\[\/no-sale\]/is', '');
            $this->view->set('{sale}', $Product["product"]["sale"]);
            $this->view->set('[sale]', '');
            $this->view->set('[/sale]', '');

        } else{

            $this->view->setPreg('/\[sale\](.*?)\[\/sale\]/is', '');
            $this->view->set('{sale}', '');
            $this->view->set('[no-sale]', '');
            $this->view->set('[/no-sale]', '');
        }











        $this->view->setMeta('Продукты', 'CRM система для автоматизации бизнес процессов', [
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

}