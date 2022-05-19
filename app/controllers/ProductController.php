<?php

namespace app\controllers;

use app\controllers\classes\Functions;
use app\core\Controller;
use app\core\System;
use app\models\ProductModel;


class ProductController extends Controller {



    public function indexAction(){

        $ProductModel = new ProductModel();

        $this->view->include('product');

        $fieldsQuery = [
            'p.id, p.uid AS author_id, p.title, p.url, p.content, p.m_title, p.m_description, p.category, p.price',
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

        $url = str_replace(CONFIG_SYSTEM["seo_type_end"], "", end($this->urls));

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



        // CRUMBS
        $CategoryStep = System::setKeys($Product["categories"], "url");

        // CATEGORY NAME
        $categoryName = !empty($CategoryStep[end($this->urls)]) ? $CategoryStep[end($this->urls)]["title"] : '';
        $this->view->setMain('{category-name}', $categoryName);

        $addCategoryLink = '//'.CONFIG_SYSTEM["home"].'/';

        $catLink = $this->urls;
        array_pop($catLink);
        $catLink = implode("/", $catLink);

        $crumbs = '<div id="crumbs">';
        if(count($CategoryStep) > 1){

            $crumbs .= '<a href="//' . CONFIG_SYSTEM["home"] . '/">' . CONFIG_SYSTEM["site_title"] . '</a>';

            foreach ($this->urls as $url) {

                if(!empty($CategoryStep[$url]["title"])){
                    $addCategoryLink .= $url . '/';
                    $crumbs .= CONFIG_SYSTEM["separator"] . '<a href="' . $addCategoryLink . '">' . $CategoryStep[$url]["title"] . '</a>';
                }
            }

        } else{

            $crumbs .= '<a href="//' . CONFIG_SYSTEM["home"] . '/">' . CONFIG_SYSTEM["site_title"] . '</a>' . CONFIG_SYSTEM["separator"] . '<a href="//' . CONFIG_SYSTEM["home"] . '/'. $catLink . '/">' . $CategoryStep[$catLink]["title"] . '</a>';
        }

        $crumbs .= '</div>';

        $this->view->setMain('{crumbs}', $crumbs);
        // CRUMBS END



        $poster = '//'.CONFIG_SYSTEM["home"].'/templates/'.CONFIG_SYSTEM["template"].'/img/no-image.svg';
        if(!empty($Product["product"]["src"])){
            $poster = '//'.CONFIG_SYSTEM["home"].'/uploads/products/'.$Product["product"]["src"];
        } else if(!empty($Product["product"]["poster"]) && !empty($Product["images"][$Product["product"]["poster"]]["src"])){
            $poster = '//'.CONFIG_SYSTEM["home"].'/uploads/products/'.$Product["images"][$Product["product"]["poster"]]["src"];
        } else if(!empty($Product["images"])){
            $poster = '//'.CONFIG_SYSTEM["home"].'/uploads/products/'.end($Product["images"])["src"];
        }


        $this->view->set('{id}', !empty(CONFIG_SYSTEM["str_pad_id"]) ? str_pad($Product["product"]["id"], CONFIG_SYSTEM["str_pad_id"], '0', STR_PAD_LEFT) : $Product["product"]["id"]);

        if($this->view->findTag('{vendor}'))
            $this->view->set('{vendor}', !empty(CONFIG_SYSTEM["str_pad_vendor"]) ? str_pad($Product["product"]["vendor"], CONFIG_SYSTEM["str_pad_vendor"], '0', STR_PAD_LEFT) : $Product["product"]["vendor"]);

        //$this->view->set('{link}', $link);
        $this->view->set('{title}', $Product["product"]["title"]);



        // --- категории товара
        if($this->view->findTag('{categories}')){
            $categories = '<ul class="nex_categories">';
            $addLink = '//'.CONFIG_SYSTEM["home"].'/';
            foreach ($CategoryStep as $row) {

                $addLink .= $row["url"].'/';
                $categories .= '<li><a href="' . $addLink . '">' . $row["title"] . '</a></li>';
            }
            $categories .= '</ul>';
            $this->view->set('{categories}', $categories);
        }

        // конечная категория
        if($this->view->findTag('{category}')){
            $categoryLast = end($Product["categories"]);
            $this->view->set('{category}', '<a href="'.$addCategoryLink.'">'.$categoryLast["title"].'</a>');
        }

        // конечная категория
        if($this->view->findTag('{category-title}')){
            $categoryLast = end($Product["categories"]);
            $this->view->set('{category-title}', $categoryLast["title"]);
        }
        // ---



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

        $categoryLink = implode("/", $this->urls);

        // FULL LINK
        $link = $Product["product"]["url"].CONFIG_SYSTEM["seo_type_end"];
        if(CONFIG_SYSTEM["seo_type"] == '2' || CONFIG_SYSTEM["seo_type"] == '4')
            $link = $Product["product"]["id"] . '-' . $link;
        if(CONFIG_SYSTEM["seo_type"] == '3' || CONFIG_SYSTEM["seo_type"] == '4')
            $link = $categoryLink . '/' . $link;
        $link = '//'.CONFIG_SYSTEM["home"].'/'.$link;


        $this->view->set('{currency}', CONFIG_SYSTEM["currency"]);
        $this->view->set('{poster}', $poster);


        $data_goods = [
            "id" => $Product["product"]["id"],
            "title" => str_replace("'", "", $Product["product"]["title"]), #TODO решить проблему с апострофом
            "link"  => $link,
            "price" => $Product["product"]["price"],
            "image" => $poster,
        ];
        $data_goods = json_encode($data_goods, JSON_UNESCAPED_UNICODE);

        /*echo "<pre>";
        print_r($data_goods);
        echo "</pre>";
        exit;*/

        // IMAGES
        $images = '';
        if(!empty($findTags["{images}"]) && !empty($Product["images"])){
            foreach ($Product["images"] as $image) {
                $images .= '<figure><a href="//'.CONFIG_SYSTEM["home"].'/uploads/products/'.$image["src"].'" data-fancybox="group"><img src="//'.CONFIG_SYSTEM["home"].'/uploads/products/'.$image["src"].'" alt=""></a></figure>';
            }
        }
        $this->view->set('{images}', $images);



        // высчитываем средний бал по отзывам
        $count_reviews = $rateAll = $rating = 0;
        if(!empty($Product["reviews"])){
            foreach ($Product["reviews"] as $review) {
                if($review["status"] != '0'){
                    $rateAll += $review["rating"];
                    $count_reviews++;
                }
            }
        }
        if($rateAll != 0){

            $rating = round($rateAll / $count_reviews, 2);

            # TODO проверить!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            $rating = '<div class="rating rating_stop" data-rate-value="'.$rating.'" data-nid="'.$Product["product"]["id"].'"></div>
            <div class="dn" itemprop="aggregateRating" itemscope="" itemtype="http://schema.org/AggregateRating">
                <span itemprop="ratingValue">'.$rating.'</span>
                <span itemprop="worstRating">1</span>
                <span itemprop="bestRating">5</span>
                <span itemprop="ratingCount">'.$rateAll.'</span>
            </div>';

        } else $rating = '<div class="rating rating_stop" data-rate-value="0" data-nid="'.$Product["product"]["id"].'"></div>';


        $this->view->set('{rating}', $rating);
        $this->view->set('{content}', $Product["product"]["content"]);
        $this->view->set('{buy}', '<a href="/cart/" class="ks_buy" data-product=\''.$data_goods.'\'>Купить</a>');
        $this->view->set('{buy-click}', '<a href="#order_click" class="buy_on_click open_modal" title="Заказать по телефону"></a>');
        $this->view->set('{add-cart}', '<a href="#" class="ks_add_cart" data-product=\''.$data_goods.'\' title="Добавить в корзину"></a>');


        $this->view->set('{rating-count}', '1');
        $this->view->set('{reviews}', '1');

        $Product["product"]["price"] = $price = (CONFIG_SYSTEM["penny"]) ? floatval($Product["product"]["price"]) : round($Product["product"]["price"]);


        $properties = '';

        if(!empty($Product["properties"])){

            foreach ($Product["properties"] as $propName => $props) {

                // select
                if($props[0]["f_type"] == '1'){

                    $selectProp = '<div class="nex_properties">
                            <label for="">'.$propName.':</label>
                            <select name="ft_select" class="ft_select" data-type-select="'.$props[0]["f_type"].'">';

                    $allowedNext = false;
                    foreach ($props as $prop) {

                        if(!empty($prop["val"]) || !empty($prop["sep"])){

                            $allowedNext = true;

                            $priceProp = (CONFIG_SYSTEM["penny"]) ? floatval($prop["price"]) : round($prop["price"]);

                            if($prop["pv"] === null) $calc = 'new';
                            else $calc = $prop["pv"];

                            $active = ($prop["def"] == 1) ? ' selected' : '';
                            $val = !empty($prop["sep"]) ? $prop["sep"] : $prop["val"];
                            
                            if($priceProp == '0') $priceProp = '';

                            if($val == '') $selectProp .= '<option data-p-sum="" data-p-calc="" value="">- выбрать -</option>';
                            else $selectProp .= '<option data-p-id="'.$prop["id"].'" data-p-title="'.$propName.'" data-p-sum="'.$priceProp.'" data-p-stock="'.$prop["stock"].'" data-p-calc="'.$calc.'"'.$active.' value="'.$val.'">'.$val.'</option>';
                        }
                    }

                    $selectProp .= '</select>
                        </div>';

                    if($allowedNext) $properties .= $selectProp;
                }

                // change
                if($props[0]["f_type"] == '2' || $props[0]["f_type"] == '3'){

                    $properties .= '<div class="nex_properties">
                            <label for="">'.$propName.':</label>
                            <ul class="ft_select" data-type-select="'.$props[0]["f_type"].'">';

                    $c = 0;
                    foreach ($props as $prop) {

                        $priceProp = (CONFIG_SYSTEM["penny"]) ? floatval($prop["price"]) : round($prop["price"]);

                        if($prop["pv"] === null) $calc = 'new';
                        else $calc = $prop["pv"];

                        $class_active = ($prop["def"] == 1) ? ' class="active"' : '';
                        $val = !empty($prop["sep"]) ? $prop["sep"] : $prop["val"];

                        $properties .= '<li data-p-id="'.$prop["id"].'" data-p-title="'.$propName.'" data-p-sum="'.(($priceProp != 0) ? $priceProp : $price).'" data-p-stock="'.$prop["stock"].'" data-p-calc="'.$calc.'"'.$class_active.' data-p-val="'.$val.'">'.$val.'</li>';
                        $c++;
                    }

                    $properties .= '</ul>
                        </div>';
                }
            }
        }

        $this->view->set('{properties}', $properties);





        if(!empty($Product["product"]["sale"])){

            if(is_numeric($Product["product"]["sale"])){

                $price = $price - intval($Product["product"]["sale"]);
                $Product["product"]["sale"] .= CONFIG_SYSTEM["currency"];

            } else if(strripos($Product["product"]["sale"], "%") !== false){

                $price = $price - (($price / 100) * trim($Product["product"]["sale"], "%"));
            }
        }

        $this->view->set('{categories}', $Product["product"]["category"]);
        
        $this->view->set('{price}', $price);
        $this->view->set('{old-price}', $Product["product"]["price"]);
        $this->view->set('{stock}', !empty($Product["product"]["stock"]) ? $Product["product"]["stock"] : '');



        $edit = '';
        if(ADMIN) $edit = '<a href="//'.CONFIG_SYSTEM["home"].'/'.CONFIG_SYSTEM["panel"].'/products/edit/'.$Product["product"]["id"].'/" target="_blank" class="edit_goods" title="Редактировать"></a>';

        $this->view->set('{edit}', $edit);
        $this->view->setMain('{CONTENT}', $this->view->get());


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

        $this->view->render(false);

        Functions::scanTags($this);
        $this->view->display();
    }

}