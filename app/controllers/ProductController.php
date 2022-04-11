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

        echo "<pre>";
        print_r($Product);
        echo "</pre>";
        exit;


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