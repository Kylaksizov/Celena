<?php

namespace app\controllers\plugins\Kylaksizov\Example\web;

use app\core\Controller;
use app\_classes\System;
use app\traits\Users;


class IndexController extends Controller{


    use Users;

    public function indexAction(){
        
        /*$generationCode = System::getNormSize(System::getDirSize(ROOT.'/app'));
        
        echo "<pre>";
        print_r($generationCode);
        echo "</pre>";
        exit;*/
        
        
        
        $Users = $this->getUsers();
        
        echo "<pre>";
        print_r($Users);
        echo "</pre>";
        
        

        $this->view->load('Web2');

        // $includeMenu = $this->view->include('includes/menu');


        $this->view->include('board');
        $this->view->set('{menu-title}', 'Главная');
        $this->view->setMain('{board}', $this->view->get());
        $this->view->clear();


        //$this->view->set('{test}', 'work');

        //$this->view->get('page');

        $this->view->include('test');
        $this->view->set('{res}', '777');
        $test = $this->view->get();
        $this->view->clear();

        $this->view->include('page');
        $this->view->set('{test}', '333');

        foreach (['1', '2'] as $item) {

            $this->view->set('{test}', $test . '-' . $item);
            $this->view->push();
        }
        $this->view->clearPush();

        //$this->view->setMain('{menu}', $this->view->get());


        /*$this->view->styles = [
            'css/test.css',
            'css/test2.css'
        ];
        $this->view->scripts = [
            'js/test.js',
            'js/test2.js'
        ];*/

        $this->view->setMeta('Доска объявлений', 'Описание плагина', [
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