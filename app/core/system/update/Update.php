<?php

namespace app\core\system\update;

use app\core\Base;
use app\core\System;

class Update{

    public function update(){

        Base::run("CREATE TABLE `".PREFIX."notify` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `title` VARCHAR(30) NOT NULL,
            `message` VARCHAR(300) NOT NULL,
            `link` VARCHAR(200) NOT NULL DEFAULT '',
            `created` INT(11) NOT NULL,
            `see` TINYINT(1) NOT NULL DEFAULT '0',
            `status` TINYINT(1) NOT NULL DEFAULT '1',
            PRIMARY KEY (`id`)
        ) ENGINE = InnoDB;");

        //System::removeDir(APP . "/libs/binotel");

        /*System::removeRoute([
            'panel' => [
                'users/roles/(page-[0-9]+/)?$' => ['controller' => 'users', 'action' => 'roles'],
            ]
        ]);*/

        /*System::addRoute([
            'panel' => [
                'system/info/$' => ['controller' => 'System', 'action' => 'info'],
            ]
        ]);*/

        /*System::addSystemConfig([
            "crumbs_title" => "Celena",
        ]);*/

        // обязательное изменение версии !
        System::editSystemConfig(["version" => '0.1.6']);
        //System::editSystemConfig(["system_update" => 1], true); // удаляем инфу об обновлении
        return true;
    }
}