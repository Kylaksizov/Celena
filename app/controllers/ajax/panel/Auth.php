<?php

namespace app\controllers\ajax\panel;

use app\core\System;
use app\models\panel\BrandModel;
use app\models\UsersModel;
use Exception;

class Auth{



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

        die("info::error::Ещё не готова регистрация!");
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
                window.location.href = "/'.CONFIG_SYSTEM["panel"].'/";
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