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
                'templates/(page-[0-9]+/)?$' => ['controller' => 'templates'],
            ]
        ]);

        //System::addSystemConfig(["comments" => 1]);

        // обязательное изменение версии !
        System::editSystemConfig(["version" => '0.1.0']);
        return true;
    }
}