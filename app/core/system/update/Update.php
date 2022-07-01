<?php

namespace app\core\system\update;

use app\core\Base;
use app\core\System;

class Update{

    public function update(){


        //Base::run("ALTER TABLE `".PREFIX."users` ADD `avatar` VARCHAR(20) NOT NULL DEFAULT '' AFTER `password`");

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
            "power" => 1,
            "main" => 1,
            "main_content" => "",
            "power_text" => "Сайт находится на реконструкции."
        ]);*/

        // обязательное изменение версии !
        System::editSystemConfig(["version" => '0.1.3']);
        return true;
    }
}