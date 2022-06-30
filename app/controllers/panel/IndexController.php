<?php

namespace app\controllers\panel;

use app\core\PanelController;
use app\libs\charts\Circle;
use app\libs\charts\Column;
use app\libs\charts\Combined;
use app\libs\charts\Radar;


class IndexController extends PanelController {


    public function indexAction(){


        //$this->view->load('Nex');

        // $includeMenu = $this->view->include('includes/menu');


        
        /*$this->view->include('includes/menu');
        $this->view->set('{menu-title}', 'Главная');
        $this->view->setMain('{menu}', $this->view->get());
        $this->view->clear();*/

        //$this->view->set('{test}', 'work');
        //$this->view->get('page');

        $this->view->include('home');

        $Circle   = new Circle();
        $Radar    = new Radar();
        $Column   = new Column();
        $Combined = new Combined();

        $this->view->set('{chart_circle}', $Circle->create('chart_circle'));
        $this->view->set('{chart_radar}', $Radar->create('chart_radar'));
        $this->view->set('{chart_column}', $Column->create('chart_column'));
        $this->view->set('{chart_combined}', $Combined->create('chart_combined'));

        //$test = $this->view->get();
        //$this->view->clear();

        /*$this->view->include('page');
        $this->view->set('{test}', '333');

        foreach (['1', '2'] as $item) {

            $this->view->set('{test}', $test . '-' . $item);
            $this->view->push();
        }
        $this->view->clearPush();*/

        //$this->view->setMain('{menu}', $this->view->get());

        /*$this->view->styles = [
            'css/test.css',
            'css/test2.css'
        ];
        $this->view->scripts = [
            'js/test.js',
            'js/test2.js'
        ];*/


        //$this->view->setMain('{chart_combined}', Combined::chart('chart_combined'));

        $this->view->render('Рабочий стол');
    }
    
}