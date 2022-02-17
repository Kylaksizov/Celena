<?php


namespace app\controllers\panel;

use app\core\PanelController;


class UsersController extends PanelController {


    public function indexAction(){

        $content = '';

        /*$this->view->include('');
        $this->view->set('{}', $content);

        $this->view->setMain('{tag}', $this->view->get());*/

        $this->view->render('');
    }


    public function customerAction(){

        $content = '';

        /*$this->view->include('');
        $this->view->set('{}', $content);

        $this->view->setMain('{tag}', $this->view->get());*/

        $this->view->render('');
    }


    public function employeeAction(){

        $content = '';

        /*$this->view->include('');
        $this->view->set('{}', $content);

        $this->view->setMain('{tag}', $this->view->get());*/

        $this->view->render('');
    }


    public function rolesAction(){

        $content = '';

        /*$this->view->include('');
        $this->view->set('{}', $content);

        $this->view->setMain('{tag}', $this->view->get());*/

        $this->view->render('');
    }

}