<?php

namespace app\controllers;

use app\controllers\classes\Functions;
use app\core\Controller;


class NotFoundController extends Controller{

    public function indexAction(){

        Functions::preTreatment($this);

        $this->view->include('notFound');

        $this->view->setMain('{crumbs}', '<div id="crumbs"><a href="//'.CONFIG_SYSTEM["home"].'/">'.CONFIG_SYSTEM["site_title"].'</a></div>');

        $this->view->setMain('{CONTENT}', '');
        $this->view->clear();

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

        $this->view->render(false);

        Functions::scanTags($this);
        $this->view->display();
    }

}