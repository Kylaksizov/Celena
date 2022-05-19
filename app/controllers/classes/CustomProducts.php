<?php

namespace app\controllers\classes;

use app\core\System;
use app\core\View;
use app\models\ProductModel;

class CustomProducts{



    public function get($e, $paginationPow = false, $categories = [], $template = 'customProducts', $limit = 10, $order = 'id', $sort = 'desc'){

        $tpl = new View($e->route);

        if(empty($template)) $template = 'customProducts';

        $ProductModel = new ProductModel();

        $tpl->load();
        $tpl->include($template);

        if(!is_array($categories) || $categories == 'index') $limit = CONFIG_SYSTEM["count_prod_by_cat"];

        if(!empty($_GET["search"])) $Products = $ProductModel->search($_GET["search"], $categories, $limit, $order, $sort);
        else $Products = $ProductModel->getProducts($paginationPow, $categories, $limit, $order, $sort);

        if($e->route["controller"] == 'category'){ // если категория

            // CRUMBS
            $CategoryStep = System::setKeys($Products["categories"], "url");

            // CATEGORY NAME
            $categoryName = !empty($CategoryStep[end($e->urls)]) ? $CategoryStep[end($e->urls)]["title"] : '';
            $e->view->setMain('{category-name}', $categoryName);

            $addCategoryLink = '//'.CONFIG_SYSTEM["home"].'/';

            $crumbs = '<div id="crumbs">';
            if(count($CategoryStep) > 1){

                $crumbs .= '<a href="//' . CONFIG_SYSTEM["home"] . '/">' . CONFIG_SYSTEM["site_title"] . '</a>';

                foreach ($e->urls as $url) {

                    if(!empty($CategoryStep[$url]["title"])){
                        $addCategoryLink .= $url . '/';
                        $crumbs .= CONFIG_SYSTEM["separator"] . '<a href="' . $addCategoryLink . '">' . $CategoryStep[$url]["title"] . '</a>';
                    }
                }

            } else{

                $catLink = $e->urls;
                array_pop($catLink);
                $catLink = implode("/", $catLink);

                $crumbs .= '<a href="//' . CONFIG_SYSTEM["home"] . '/">' . CONFIG_SYSTEM["site_title"] . '</a>' . CONFIG_SYSTEM["separator"] . '<a href="//' . CONFIG_SYSTEM["home"] . '/'. $catLink . '/">' . $CategoryStep[$catLink]["title"] . '</a>';
            }

            $crumbs .= '</div>';

            $e->view->setMain('{crumbs}', $crumbs);
            // CRUMBS END
        }

        // проверяем теги тут, что бы меньше было поиска в цикле
        $tagPrice = $tpl->findTag('{price}');
        $tagOldPrice = $tpl->findTag('{old-price}');
        $tagStock = $tpl->findTag('{stock}');

        $buildCatLinks = (CONFIG_SYSTEM["seo_type"] == '3' || CONFIG_SYSTEM["seo_type"] == '4') ? Functions::buildCatLinks($Products["categories"]) : '';

        foreach ($Products["products"] as $row) {

            // FULL LINK
            $link = $row["url"].CONFIG_SYSTEM["seo_type_end"];
            if(CONFIG_SYSTEM["seo_type"] == '2')
                $link = $row["id"] . '-' . $link;
            if(CONFIG_SYSTEM["seo_type"] == '3')
                $link = $buildCatLinks[$row["category_id"]]["urls"].'/' . $link;
            if(CONFIG_SYSTEM["seo_type"] == '4')
                $link = $buildCatLinks[$row["category_id"]]["urls"].'/' . $row["id"] . '-' . $link;
            $link = '//'.CONFIG_SYSTEM["home"].'/'.$link;


            $poster = !empty($row["poster"]) ? $row["poster"] : 'no-image.png';


            $tpl->set('{id}', !empty(CONFIG_SYSTEM["str_pad_id"]) ? str_pad($row["id"], CONFIG_SYSTEM["str_pad_id"], '0', STR_PAD_LEFT) : $row["id"]);

            if($tpl->findTag('{vendor}'))
                $tpl->set('{vendor}', !empty(CONFIG_SYSTEM["str_pad_vendor"]) ? str_pad($row["vendor"], CONFIG_SYSTEM["str_pad_vendor"], '0', STR_PAD_LEFT) : $row["vendor"]);

            $tpl->set('{link}', $link);
            $tpl->set('{title}', $row["title"]);

            $row["price"] = $price = round($row["price"]);

            if(!empty($row["sale"])){

                if(is_numeric($row["sale"])){

                    $price = round($price - intval($row["sale"]), 2);
                    $row["sale"] .= CONFIG_SYSTEM["currency"];

                } else if(strripos($row["sale"], "%") !== false){

                    $price = round($price - (($price / 100) * trim($row["sale"], "%")));
                }
            }

            if($tagPrice)    $tpl->set('{price}', $price);
            if($tagOldPrice) $tpl->set('{old-price}', $row["price"]);
            if($tagStock)    $tpl->set('{stock}', $row["stock"]);

            $tpl->set('{currency}', CONFIG_SYSTEM["currency"]);
            $tpl->set('{poster}', '//'.CONFIG_SYSTEM["home"].'/uploads/products/'.$poster);


            $data_goods = [
                "id" => $row["id"],
                "title" => $row["title"],
                "link"  => $link,
                "price" => $row["price"],
                "image" => $poster,
            ];
            $data_goods = json_encode($data_goods, JSON_UNESCAPED_UNICODE);

            $tpl->set('{images}', '');
            $tpl->set('{rating}', '');
            $tpl->set('{description}', '');
            $tpl->set('{buy}', '<a href="/cart.html" class="ks_buy" data-goods=\''.$data_goods.'\'>Купить</a>');
            $tpl->set('{buy-click}', '<a href="#order_click" class="buy_on_click open_modal" title="Заказать по телефону"></a>');
            $tpl->set('{add-cart}', '<a href="#" class="ks_add_cart" data-goods=\''.$data_goods.'\' title="Добавить в корзину"></a>');


            if(!empty($row["sale"])){

                $tpl->setPreg('/\[no-sale\](.*?)\[\/no-sale\]/is', '');
                $tpl->set('{sale}', $row["sale"]);
                $tpl->set('[sale]', '');
                $tpl->set('[/sale]', '');

            } else{

                $tpl->setPreg('/\[sale\](.*?)\[\/sale\]/is', '');
                $tpl->set('{sale}', '');
                $tpl->set('[no-sale]', '');
                $tpl->set('[/no-sale]', '');
            }

            $tpl->push();
        }

        $tpl->clearPush();

        return $tpl->get().(!empty($Products["pagination"]) ? $Products["pagination"] : '');
    }

}