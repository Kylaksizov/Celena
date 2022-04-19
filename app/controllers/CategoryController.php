<?php

namespace app\controllers;

use app\core\Controller;
use app\core\System;
use app\models\ProductModel;
use Exception;


class CategoryController extends Controller {


    /**
     * @throws Exception
     */
    public function indexAction(){

        //$this->view->load('Nex');
/*echo "<pre>";
print_r("Блять!");
echo "</pre>";
exit;*/
        $ProductModel = new ProductModel();

        $this->view->include('products');

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

        if($this->view->findTag('{poster}')) $fieldsQuery['{poster}'] = 'i.src, i.alt, i.position';
        if($this->view->findTag('{images}')) $fieldsQuery['{images}'] = '1';

        $findTags = $this->view->findTags($fieldsQuery);

        $Products = $ProductModel->getProducts($this->urls, $findTags);

        $CategoryStep = System::setKeys($Products["categories"], "url");
        $categoryLink = implode("/", $this->urls);

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
        $crumbs = '<div id="crumbs">';
        if(count($CategoryStep) > 1){

            $crumbs .= '<a href="' . CONFIG_SYSTEM["home"] . '">' . CONFIG_SYSTEM["site_title"] . '</a>';

            $addLink = CONFIG_SYSTEM["home"];
            foreach ($CategoryStep as $row) {

                $addLink .= $row["url"].'/';
                $crumbs .= CONFIG_SYSTEM["separator"] . '<a href="' . $addLink . '">' . $row["title"] . '</a>';
            }

        } else $crumbs .= '<a href="' . CONFIG_SYSTEM["home"] . '">' . CONFIG_SYSTEM["site_title"] . '</a>' . CONFIG_SYSTEM["separator"] . $CategoryStep[end($this->urls)]["title"];

        $crumbs .= '</div>';

        $this->view->setMain('{crumbs}', $crumbs);


        // CATEGORY NAME
        $categoryName = $CategoryStep[end($this->urls)]["title"];
        $this->view->setMain('{category-name}', $categoryName);

        // проверяем теги тут, что бы меньше было поиска в цикле
        $tagPrice = $this->view->findTag('{price}');
        $tagOldPrice = $this->view->findTag('{old-price}');
        $tagStock = $this->view->findTag('{stock}');

        foreach ($Products["products"] as $row) {


            // FULL LINK
            $link = $row["url"].CONFIG_SYSTEM["seo_type_end"];
            if(CONFIG_SYSTEM["seo_type"] == '2' || CONFIG_SYSTEM["seo_type"] == '4')
                $link = $row["id"] . '-' . $link;
            if(CONFIG_SYSTEM["seo_type"] == '3' || CONFIG_SYSTEM["seo_type"] == '4')
                $link = $categoryLink . '/' . $link;
            $link = CONFIG_SYSTEM["home"].$link;

            
            $poster = !empty($row["src"]) ? $row["src"] : 'no-image.png';


            $this->view->set('{id}', !empty(CONFIG_SYSTEM["str_pad_id"]) ? str_pad($row["id"], CONFIG_SYSTEM["str_pad_id"], '0', STR_PAD_LEFT) : $row["id"]);

            if($this->view->findTag('{vendor}'))
                $this->view->set('{vendor}', !empty(CONFIG_SYSTEM["str_pad_vendor"]) ? str_pad($row["vendor"], CONFIG_SYSTEM["str_pad_vendor"], '0', STR_PAD_LEFT) : $row["vendor"]);

            $this->view->set('{link}', $link);
            $this->view->set('{title}', $row["title"]);

            $row["price"] = $price = round($row["price"]);

            if(!empty($row["sale"])){

                if(is_numeric($row["sale"])){

                    $price = round($price - intval($row["sale"]), 2);
                    $row["sale"] .= CONFIG_SYSTEM["currency"];

                } else if(strripos($row["sale"], "%") !== false){

                    $price = round($price - (($price / 100) * trim($row["sale"], "%")));
                }
            }

            if($tagPrice)    $this->view->set('{price}', $price);
            if($tagOldPrice) $this->view->set('{old-price}', $row["price"]);
            if($tagStock)    $this->view->set('{stock}', $row["stock"]);

            $this->view->set('{currency}', CONFIG_SYSTEM["currency"]);
            $this->view->set('{poster}', CONFIG_SYSTEM["home"].'uploads/products/'.$poster);


            $data_goods = [
                "id" => $row["id"],
                "title" => $row["title"],
                "link"  => $link,
                "price" => $row["price"],
                "image" => $poster,
            ];
            $data_goods = json_encode($data_goods, JSON_UNESCAPED_UNICODE);

            $this->view->set('{images}', '');
            $this->view->set('{rating}', '');
            $this->view->set('{description}', '');
            $this->view->set('{buy}', '<a href="/cart.html" class="ks_buy" data-goods=\''.$data_goods.'\'>Купить</a>');
            $this->view->set('{buy-click}', '<a href="#order_click" class="buy_on_click open_modal" title="Заказать по телефону"></a>');
            $this->view->set('{add-cart}', '<a href="#" class="ks_add_cart" data-goods=\''.$data_goods.'\' title="Добавить в корзину"></a>');

            if(!empty($row["sale"])){

                $this->view->setPreg('/\[no-sale\](.*?)\[\/no-sale\]/is', '');
                $this->view->set('{sale}', $row["sale"]);
                $this->view->set('[sale]', '');
                $this->view->set('[/sale]', '');

            } else{

                $this->view->setPreg('/\[sale\](.*?)\[\/sale\]/is', '');
                $this->view->set('{sale}', '');
                $this->view->set('[no-sale]', '');
                $this->view->set('[/no-sale]', '');
            }

            $this->view->push();
        }

        $this->view->clearPush();




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