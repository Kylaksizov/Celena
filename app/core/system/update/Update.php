<?php

namespace app\core\system\update;

use app\core\Base;
use app\core\System;

class Update{

    public function update(){


        Base::run("ALTER TABLE `".PREFIX."categories` ADD `plugin_id` VARCHAR(300) NULL DEFAULT '' AFTER `id`");


        // обязательное изменение версии !
        System::editSystemConfig(["version" => '0.0.4']);
        return true;
    }
}