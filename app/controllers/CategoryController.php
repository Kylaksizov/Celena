<?php

namespace app\controllers;

use app\core\Controller;
use app\models\ProductModel;
use Exception;


class CategoryController extends Controller {


    /**
     * @throws Exception
     */
    public function indexAction(){

        //$this->view->load('Nex');

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

        if($this->view->findTag('{poster}')) $fieldsQuery['{poster}'] = 'i.src, i.alt';
        if($this->view->findTag('{images}')) $fieldsQuery['{images}'] = '1';

        $findTags = $this->view->findTags($fieldsQuery);

        $Products = $ProductModel->getProducts($this->urls, $findTags);
        






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

}