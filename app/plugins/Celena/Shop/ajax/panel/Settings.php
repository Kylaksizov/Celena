<?php

namespace app\plugins\Celena\Shop\panel;

use app\core\System;

class Settings{

    public function index(){

        if(empty($_POST["config"]["errors"])) $_POST["config"]["errors"] = 0;
        if(empty($_POST["config"]["db_log"])) $_POST["config"]["db_log"] = 0;
        if(empty($_POST["config"]["dev_tools"])) $_POST["config"]["dev_tools"] = 0;
        if(empty($_POST["config"]["email_confirm"])) $_POST["config"]["email_confirm"] = 0;

        $newConfig = $_POST["config"];

        $newConfig["dev"] = explode("\r\n", $newConfig["dev"]);

        System::editSystemConfig($newConfig);

        die("info::success::Сохранил!");
    }

}