<?php

namespace app\controllers\ajax\panel;

use app\core\System;
use Exception;


class Notify{

    use \app\traits\panel\Notify;

    public function index(){

        if(!empty($_POST["see"])) self::see();
        die("info::error::Неизвестный запрос!");
    }



    /**
     * @name клик по уведомлению
     * =========================
     * @return void
     * @throws Exception
     */
    private function see(){

        $notifyId = !empty($_POST["see"]) ? intval($_POST["see"]) : die("info::error::Не указан ID уведомления!");

        self::seeNotify($notifyId);

        if(!empty($_POST["link"])){

            $script = '<script>
                window.location.href = "'.$_POST["link"].'";
            </script>';

        } else{

            $script = '<script>
                $(".cel_tmp").parent().addClass("notify_see");
            </script>';
        }

        System::script($script);
    }

}