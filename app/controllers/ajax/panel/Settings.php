<?php

namespace app\controllers\ajax\panel;

use app\core\System;
use app\models\panel\PostModel;
use app\traits\SiteMap;

class Settings{

    public function index(){

        if(!empty($_POST["config"])) self::saveConfig();
        if(!empty($_POST["generateMap"])) self::siteMapGeneration();
    }


    /**
     * @name сохранение настроек
     * =========================
     * @return void
     */
    private static function saveConfig(){

        if(empty($_POST["config"]["errors"])) $_POST["config"]["errors"] = 0;
        if(empty($_POST["config"]["db_log"])) $_POST["config"]["db_log"] = 0;
        if(empty($_POST["config"]["dev_tools"])) $_POST["config"]["dev_tools"] = 0;
        if(empty($_POST["config"]["email_confirm"])) $_POST["config"]["email_confirm"] = 0;
        if(empty($_POST["config"]["ssl"])) $_POST["config"]["ssl"] = 0;
        if(empty($_POST["config"]["quill_thumbs"])) $_POST["config"]["quill_thumbs"] = 0;

        $newConfig = $_POST["config"];

        $newConfig["dev"] = explode("\r\n", $newConfig["dev"]);

        System::editSystemConfig($newConfig);

        die("info::success::Сохранил!");
    }


    /**
     * @name генерация карты сайта
     * ===========================
     * @return void
     */
    private function siteMapGeneration(){

        $PostModel = new PostModel();
        $Posts = $PostModel->getFromMap();
        SiteMap::generation($Posts);

        die("info::success::Карта сгенерирована!");
    }

}