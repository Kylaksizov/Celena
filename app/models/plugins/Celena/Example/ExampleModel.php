<?php

namespace app\models\plugins\Celena\Example;


use app\core\Base;
use app\core\Model;
use app\core\System;
use Exception;
use PDO;
use PDOStatement;

class ExampleModel extends Model{


    public function getTmp(){

        return Base::run("
            SELECT
                *
            FROM " . PREFIX . "example
        ", [])->fetchAll(PDO::FETCH_ASSOC);
    }

}