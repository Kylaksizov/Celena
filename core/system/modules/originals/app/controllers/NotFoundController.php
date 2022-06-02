<?php

namespace app\controllers;

use app\core\Controller;


class NotFoundController extends Controller{

    public function indexAction(){

        $this->view->load();

        $this->view->setMain('{crumbs}', '<div id="crumbs"><a href="//'.CONFIG_SYSTEM["home"].'/">'.CONFIG_SYSTEM["site_title"].'</a></div>');

        $this->view->setMeta('Главная страница', 'Описание страницы', [
            [
                'property' => 'og:title',
                'content' => 'Описание страницы',
            ],
            [
                'property' => 'og:description',
                'content' => 'Для гугла',
            ]
        ]);

        $this->view->render();
    }

}