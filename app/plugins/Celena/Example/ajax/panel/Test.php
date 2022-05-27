<?php

namespace app\plugins\Celena\Example\ajax\panel;

use app\core\System;

class Test{



    /**
     * @name обязательный метод index!
     * ===============================
     * @return void
     */
    public function index(){

        if(!empty($_POST["example"])){

            if($_POST["example"] == '1') $this->exampleMethod();
            if($_POST["example"] == '2') {

                // Здесь ничего не делаем,
                // пост запрос пропускаем дальше в тело контроллера по этому же адресу роутера
                // и $_POST уходит весь в контроллер

                // Если мы сделаем возврат (return), то
                // в контроллере его можно поймать из свойства $this->ajax
                // передать можно строку, массив или что угодно. Пока нет ограничений...
                return 'success';
            }

        } else die("info::error::Уведомление об ошибке");
        // die("info::success::Уведомление об успехе");
    }




    /**
     * @name демонстрация обработки пост запросов через скрипт JS || JQuery
     * ====================================================================
     * @description комментарии в скрипте запрещены!
     * @return void
     */
    private function exampleMethod(){

        $script = '<script>
            $.server_say({say: "Это простое системное уведомление!", status: "success"});
            $("h1").text("Заменим заголовок на нашей странице!");
        </script>';

        System::script($script);
    }

}