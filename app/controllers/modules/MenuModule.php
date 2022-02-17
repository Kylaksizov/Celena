<?php

namespace app\controllers\modules;

use app\core\Module;
use app\traits\Telegram;
use app\traits\Users;


class MenuModule extends Module {


    use Telegram, Users;


    public function init($elements){

        /*$UserModel = new UsersModel();
        $User = $UserModel->getUser(1);*/

        /*echo "<pre>";
        print_r($this->addUser('Татьяна', 'snegurochka@gmail.com'));
        echo "</pre>";
        
        echo "<pre>";
        print_r($this->getUser(1));
        echo "</pre>";*/

        //$this->getThisUser();

        /*echo "<pre>";
        print_r($this->thisUser);
        echo "</pre>";
        exit;*/

        //$this->chatId = '407136082';
        //$this->TelegramSend('test');

        //$elements->tplIndex = str_replace('{{MenuModule}}', '111', $elements->tplIndex);
        return 'Модуль работает!';
    }


    public function turn($elements){
        //$control->tplIndex = str_replace('{{MenuModule}}', '111', $control->tplIndex);
        return 'Модуль работает!';

    }
}