<?php

namespace app\controllers\panel;

use app\core\PanelController;

//use app\core\System;


class LeadsController extends PanelController {



    public function indexAction(){

        //$this->view->load('Nex');
        
        if($this->ajax){

            echo "<pre>";
            print_r($this->ajax);
            echo "</pre>";
            exit;
        }

        /*$script = '<script>
            $.server_say({say: "Запись удалена!", status: "success"});
        </script>';

        System::script($script);*/

        // $includeMenu = $this->view->include('includes/menu');

        $this->view->include('leads');
        $this->view->set('{menu-title}', 'Главная');
        $this->view->setMain('{menu}', $this->view->get());
        //$this->view->clear();

        //$this->view->set('{test}', 'work');
        //$this->view->get('page');

        /*$this->view->include('test');
        $this->view->set('{res}', '777');
        $test = $this->view->get();
        $this->view->clear();

        $this->view->include('leads');
        $this->view->set('{test}', '333');

        foreach (['1', '2'] as $item) {

            $this->view->set('{test}', $test . '-' . $item);
            $this->view->push();
        }
        $this->view->clearPush();


        $this->view->setMain('{chart_circle}', Circle::chart('chart_circle'));*/

        $this->view->render('Лиды');
    }

}