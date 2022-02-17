<?php

namespace app\controllers;

use app\_classes\Auth;
use app\core\Controller;


class IndexController extends Controller {


    public function indexAction(){

        if(!USER) Auth::google_callback();

        $Auth = Auth::google_init();

        $this->view->setMain('{reg}', $Auth);
        
        $this->view->setMeta('NEX CRM', 'CRM система для автоматизации бизнес процессов', [
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