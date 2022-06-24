<?php

namespace app\core\system\update;

use app\core\Base;
use app\core\System;

class Update{

    public function update(){


        //Base::run("ALTER TABLE `".PREFIX."users` ADD `avatar` VARCHAR(20) NOT NULL DEFAULT '' AFTER `password`");

        //System::removeDir(CORE . "/interface");

        /*System::removeRoute([
            'panel' => [
                'users/roles/(page-[0-9]+/)?$' => ['controller' => 'users', 'action' => 'roles'],
            ]
        ]);*/

        System::addRoute([
            'panel' => [
                'system/info/$' => ['controller' => 'System', 'action' => 'info'],
            ]
        ]);

        if(!isset(CONFIG_SYSTEM["quill_thumbs"]))
            System::addSystemConfig(["quill_thumbs" => 0]);

        //System::addSystemConfig(["comments" => 1]);

        // обязательное изменение версии !
        System::editSystemConfig(["version" => '0.1.1']);
        return true;
    }
}