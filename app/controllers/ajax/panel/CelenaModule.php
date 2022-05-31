<?php

namespace app\controllers\ajax\panel;


use app\models\panel\ModuleModel;

class CelenaModule{


    public function index(){

        if(!empty($_POST["name"])) self::create();
        die("info::error::Неизвестный запрос!");
    }




    /**
     * @name удаление плагина
     * ======================
     * @return void
     */
    private function create(){

        $name = trim(htmlspecialchars(strip_tags($_POST["name"])));
        $version = !empty($_POST["version"]) ? trim(htmlspecialchars(strip_tags($_POST["version"]))) : die("Заполните версию плагина");
        $cv = !empty($_POST["cv"]) ? trim(htmlspecialchars(strip_tags($_POST["cv"]))) : null;
        $descr = !empty($_POST["descr"]) ? trim(htmlspecialchars(strip_tags($_POST["descr"]))) : '';
        $comment = !empty($_POST["comment"]) ? trim(htmlspecialchars(strip_tags($_POST["comment"]))) : '';
        $status = !empty($_POST["status"]) ? 1 : 0;

        if(mb_substr_count($version, ".") != 2) die("Укажите версию плагина в формате X.X.X");
        if(!empty($cv)){
            if(mb_substr_count($cv, ".") != 2) die("Укажите версию Celena в формате X.X.X");
        }

        $base_install = !empty($_POST["base"]["install"]) ? trim(htmlspecialchars(strip_tags($_POST["base"]["install"]))) : '';
        $base_update  = !empty($_POST["base"]["update"]) ? trim(htmlspecialchars(strip_tags($_POST["base"]["update"]))) : '';
        $base_on      = !empty($_POST["base"]["on"]) ? trim(htmlspecialchars(strip_tags($_POST["base"]["on"]))) : '';
        $base_off     = !empty($_POST["base"]["off"]) ? trim(htmlspecialchars(strip_tags($_POST["base"]["off"]))) : '';
        $base_del     = !empty($_POST["base"]["del"]) ? trim(htmlspecialchars(strip_tags($_POST["base"]["del"]))) : '';

        $poster = '';

        $ModuleModel = new ModuleModel();
        $ModuleModel->add(null, $name, $descr, $version, $cv, $poster, $base_install, $base_update, $base_on, $base_off, $base_del, $comment, $status);

        die("OK");
    }

}