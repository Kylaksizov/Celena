<?php

namespace app\controllers\ajax;

use app\core\System;
use app\models\UsersModel;
use app\traits\Log;
use app\traits\Mail;

class Auth{

    use Mail, Log;


    public function index(){

        if(!empty($_POST["action"])){

            switch ($_POST["action"]){
                case 'registration': self::registration(); break;
                case 'auth': self::auth(); break;
                case 'member_pass_start': self::member_pass_start(); break;
                case 'member_pass_finish': self::member_pass_finish(); break;
            }
        }
    }



    public function registration(){

        $name = !empty($_POST["name"]) ? trim(htmlspecialchars(strip_tags($_POST["name"]))) : die("info::error::Укажите имя!");
        $email = !empty($_POST["email"]) ? trim(htmlspecialchars(strip_tags($_POST["email"]))) : die("info::error::Укажите email!");
        if(empty($_POST["password"])) die("info::error::Укажите пароль!");
        if(empty($_POST["password_repeat"])) die("info::error::Укажите пароль повторно!");

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) die("info::error::Указанный email не действительный!");
        if($_POST["password"] != $_POST["password_repeat"]) die("info::error::Вы ввели разные пароли!");

        $password = sha1(md5($_POST["password"]).':NEX');

        $status = CONFIG_SYSTEM["email_confirm"] ? 0 : 1;

        $UserModel = new UsersModel();
        $User = $UserModel->add($name, $email, $password, '', 2, $status);

        if($User["id"]){

            // если включена проверка почты
            if(CONFIG_SYSTEM["email_confirm"]){

                $theme = 'Регистрация на сайте ' . CONFIG_SYSTEM["home"];
                $body = '<p>Добрый день <b>'.$name.'</b>!</p>
                    <p>На сайте <a href="//'.CONFIG_SYSTEM["home"].'/">'.CONFIG_SYSTEM["home"].'</a> был зарегистрирован Ваш email.</p>
                    <p>Если это сделали Вы, подтвердите свою почту, перейдя по ссылке <a href="//'.CONFIG_SYSTEM["home"].'/reg/'.$User["hash"].'/">подтвердить почту</a></p>
                    <br>
                    <p>Если это были не Вы, проигнорируйте данное сообщение.</p>';

                self::sendMail($email, $theme, $body);

                die("info::success::Вам отправлено письмо для подтверждения!");

            } else{

                // Авторизация !
                SetCookie("uid", $User["id"], time() + 3600 * 24 * 7, "/");
                SetCookie("uhash", $User["hash"], time() + 3600 * 24 * 7, "/");

                $script = '<script>
                    $.server_say({say: "Успешная регистрация!", status: "success"});
                    window.location.href = "/";
                </script>';

                System::script($script);
            }

        } else{

            self::addLog('Не удалось зарегистрировать пользователя!<br>Почта: '.$email, 2);
            die("info::error::Такая почта уже зарегистрирована на сайте!");
        }
    }


    public function auth(){

        $email = trim(htmlspecialchars(strip_tags($_POST["email"])));
        $password = sha1(md5($_POST["password"]).':NEX');

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) die("info::error::Указанный email не действительный!");

        $UserModel = new UsersModel();
        $Login = $UserModel->login($email);

        if(!empty($Login)){

            if($password != $Login["password"]) die("info::error::Неправильный email или пароль!");

            // Авторизация !
            SetCookie("uid", $Login["id"], time() + 3600 * 24 * 7, "/");
            SetCookie("uhash", $Login["hash"], time() + 3600 * 24 * 7, "/");

            $script = '<script>
                $.server_say({say: "Успешная авторизация!", status: "success"});
                window.location.href = "/";
            </script>';

            System::script($script);

        } else{

            die("info::error::Такой пользователь не существует!");
        }

    }


    public function member_pass_start(){

        die("info::error::Ещё не работает восстановление пароля!");
    }


    public function member_pass_finish(){

        die("info::error::Ещё не работает восстановление пароля!");
    }

}