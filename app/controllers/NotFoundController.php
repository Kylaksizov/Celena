<?php

namespace app\controllers;

use app\core\Controller;


class NotFoundController extends Controller{

    public function indexAction(){

        $this->view->load();

        $this->view->include('notFound');
        $this->view->set('{menu-title}', 'Страница не найдена');

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