<?php

namespace app\core\system\update;

use app\core\Base;
use app\core\System;

class Update{

    public function update(){


        Base::run("CREATE TABLE `".PREFIX."post_ex` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `pid` INT(11) NOT NULL,
            `see` BIGINT(20) NOT NULL DEFAULT '0',
            PRIMARY KEY (`id`)
        ) ENGINE = InnoDB;");


        // обязательное изменение версии !
        System::editSystemConfig(["version" => '0.0.2']);
        return true;
    }
}