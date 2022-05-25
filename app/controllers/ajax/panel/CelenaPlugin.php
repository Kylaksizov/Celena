<?php

namespace app\controllers\ajax\panel;


use app\core\System;
use app\core\system\shop\ShopController;

class CelenaPlugin{

    public function index(){

        if(!empty($_POST["action"])){

            switch ($_POST["action"]){
                case 'getPlugin': self::getPlugin(); break;
            }
        }

        die("info::error::Неизвестный запрос!");
    }


    private function getPlugin(){

        $result = ShopController::getPlugin(intval($_POST["id"]));

        if(strripos($result, "<script>") === false) die($result);
        else System::script($result);
    }

}