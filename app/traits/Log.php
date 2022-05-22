<?php

namespace app\traits;

use app\models\panel\SystemModel;

trait Log {


    public static function add($text, $status = 0){

        $SystemModel = new SystemModel();
        return $SystemModel->setLog($text, $status);
    }
}