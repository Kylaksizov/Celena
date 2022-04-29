<?php

namespace app\controllers;

use app\_classes\Auth;
use app\core\Controller;


class IndexController extends Controller {


    public function indexAction(){

        if(!USER) Auth::google_callback();

        $Auth = Auth::google_init();


        $this->view->include('login');

        $this->view->set('{reg}', $Auth);

        $new_password = '';
        if(!empty($_GET["member_pass"])){

            $AuthModel = new AuthModel(true);
            $member_test = $AuthModel->member_test($_GET["member_pass"]);

            if(is_numeric($member_test)){

                $new_password = '<div id="new_password" class="modal" style="display:block;">
                    <h4 class="modal_title">Восстановление пароля</h4>
                    <form action="#" method="POST" class="inp100">
                        <input type="password" name="password" placeholder="Введите новый пароль">
                        <input type="password" name="password_repeat" placeholder="Повторите пароль">
                        <input type="hidden" name="action" value="member_pass_start">
                        <input type="hidden" name="new_password" value="'.$member_test.'"><br>
                        <input type="submit" class="btn" data-a="Registration" value="Восстановить">
                    </form>
                </div>';

            } else{

                header("Location: /");
                exit;
            }
        }

        $this->view->set('{new-password}', $new_password);

        $login = $this->view->get();
        $this->view->clear();

        $this->view->setMain('{login}', $login);

        $this->view->setMeta('Panel', 'CRM система для автоматизации бизнес процессов', [
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