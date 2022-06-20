<?php

namespace app\controllers\ajax\panel;

use app\core\System;

class Settings{

    public function index(){

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

}