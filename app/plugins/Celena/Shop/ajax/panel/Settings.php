<?php

namespace app\plugins\Celena\Shop\ajax\panel;

use app\core\System;

class Settings{

    public function index(){

        //if(empty($_POST["config"]["errors"])) $_POST["config"]["errors"] = 0;

        $newConfig = $_POST["config"];
        System::editPluginConfig($newConfig);

        die("info::success::Сохранил!");
    }

}