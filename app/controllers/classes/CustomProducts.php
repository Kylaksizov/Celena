<?php

namespace app\controllers\classes;

use app\core\System;
use app\models\ProductModel;

class CustomProducts{


    public function get($e, $template = 'products'){

        $ProductModel = new ProductModel();

        $e->view->include($template);

        // подбор полей из таблиц в зависимости от заданных тегов - эффективно, если большая база
        #TODO потом можно сделать проверку из конфига

        $fieldsQuery = [
            'p.id, p.uid AS author_id, p.title, p.url, p.category',
            '{price}'      => 'p.price',
            '{sale}'       => 'p.sale',
            '{stock}'      => 'p.stock',
            '{vendor}'     => 'p.vendor',
            '{date}'       => 'p.created',
            '{brand-id}'   => 'p.brand AS brand_id',
            '{brand-name}' => 'b.name AS brand_name',
            '{brand-url}'  => 'b.url AS brand_url',
            '{brand-icon}' => 'b.icon AS brand_icon'
        ];

        if($e->view->findTag('{poster}')) $fieldsQuery['{poster}'] = 'i.src, i.alt, i.position';
        if($e->view->findTag('{images}')) $fieldsQuery['{images}'] = '1';

        $findTags = $e->view->findTags($fieldsQuery);

        $Products = $ProductModel->getProducts($e->urls, $findTags);

        $CategoryStep = System::setKeys($Products["categories"], "url");
        $categoryLink = implode("/", $e->urls);

        /*echo '<ul class="editor_menu">';
        foreach ($Products["categories"] as $row) {
            if($row["parent_category"] == 0) {
                echo '<li>'.$row["id"].' '.$row["title"];
                echo self::ReintCategory($Products["categories"], $row["id"]);
                echo '</li>';
            }
        }
        echo "</ul>";*/

        // CRUMBS
        /*$crumbs = '<div id="crumbs">';
        if(count($CategoryStep) > 1){

            $crumbs .= '<a href="//' . CONFIG_SYSTEM["home"] . '/">' . CONFIG_SYSTEM["site_title"] . '</a>';

            $addLink = '/'.CONFIG_SYSTEM["home"].'/';
            foreach ($CategoryStep as $row) {

                $addLink .= $row["url"].'/';
                $crumbs .= CONFIG_SYSTEM["separator"] . '<a href="' . $addLink . '">' . $row["title"] . '</a>';
            }

        } else $crumbs .= '<a href="//' . CONFIG_SYSTEM["home"] . '/">' . CONFIG_SYSTEM["site_title"] . '</a>' . CONFIG_SYSTEM["separator"] . $CategoryStep[end($e->urls)]["title"];

        $crumbs .= '</div>';

        $e->view->setMain('{crumbs}', $crumbs);*/


        // CATEGORY NAME
        $categoryName = $CategoryStep[end($e->urls)]["title"];
        $e->view->setMain('{category-name}', $categoryName);

        // проверяем теги тут, что бы меньше было поиска в цикле
        $tagPrice = $e->view->findTag('{price}');
        $tagOldPrice = $e->view->findTag('{old-price}');
        $tagStock = $e->view->findTag('{stock}');

        foreach ($Products["products"] as $row) {


            // FULL LINK
            $link = $row["url"].CONFIG_SYSTEM["seo_type_end"];
            if(CONFIG_SYSTEM["seo_type"] == '2' || CONFIG_SYSTEM["seo_type"] == '4')
                $link = $row["id"] . '-' . $link;
            if(CONFIG_SYSTEM["seo_type"] == '3' || CONFIG_SYSTEM["seo_type"] == '4')
                $link = $categoryLink . '/' . $link;
            $link = '//'.CONFIG_SYSTEM["home"].'/'.$link;


            $poster = !empty($row["src"]) ? $row["src"] : 'no-image.png';


            $e->view->set('{id}', !empty(CONFIG_SYSTEM["str_pad_id"]) ? str_pad($row["id"], CONFIG_SYSTEM["str_pad_id"], '0', STR_PAD_LEFT) : $row["id"]);

            if($e->view->findTag('{vendor}'))
                $e->view->set('{vendor}', !empty(CONFIG_SYSTEM["str_pad_vendor"]) ? str_pad($row["vendor"], CONFIG_SYSTEM["str_pad_vendor"], '0', STR_PAD_LEFT) : $row["vendor"]);

            $e->view->set('{link}', $link);
            $e->view->set('{title}', $row["title"]);

            $row["price"] = $price = round($row["price"]);

            if(!empty($row["sale"])){

                if(is_numeric($row["sale"])){

                    $price = round($price - intval($row["sale"]), 2);
                    $row["sale"] .= CONFIG_SYSTEM["currency"];

                } else if(strripos($row["sale"], "%") !== false){

                    $price = round($price - (($price / 100) * trim($row["sale"], "%")));
                }
            }

            if($tagPrice)    $e->view->set('{price}', $price);
            if($tagOldPrice) $e->view->set('{old-price}', $row["price"]);
            if($tagStock)    $e->view->set('{stock}', $row["stock"]);

            $e->view->set('{currency}', CONFIG_SYSTEM["currency"]);
            $e->view->set('{poster}', '//'.CONFIG_SYSTEM["home"].'/uploads/products/'.$poster);


            $data_goods = [
                "id" => $row["id"],
                "title" => $row["title"],
                "link"  => $link,
                "price" => $row["price"],
                "image" => $poster,
            ];
            $data_goods = json_encode($data_goods, JSON_UNESCAPED_UNICODE);

            $e->view->set('{images}', '');
            $e->view->set('{rating}', '');
            $e->view->set('{description}', '');
            $e->view->set('{buy}', '<a href="/cart.html" class="ks_buy" data-goods=\''.$data_goods.'\'>Купить</a>');
            $e->view->set('{buy-click}', '<a href="#order_click" class="buy_on_click open_modal" title="Заказать по телефону"></a>');
            $e->view->set('{add-cart}', '<a href="#" class="ks_add_cart" data-goods=\''.$data_goods.'\' title="Добавить в корзину"></a>');

            if(!empty($row["sale"])){

                $e->view->setPreg('/\[no-sale\](.*?)\[\/no-sale\]/is', '');
                $e->view->set('{sale}', $row["sale"]);
                $e->view->set('[sale]', '');
                $e->view->set('[/sale]', '');

            } else{

                $e->view->setPreg('/\[sale\](.*?)\[\/sale\]/is', '');
                $e->view->set('{sale}', '');
                $e->view->set('[no-sale]', '');
                $e->view->set('[/no-sale]', '');
            }

            $e->view->push();
        }
        $e->view->clearPush();

        //$e->view->include[$e->route["controller"]] = str_replace($product, $res, $e->view->include[$e->route["controller"]]);

        //return $e->view->include[$template];
        return $e->view->get();
    }

}