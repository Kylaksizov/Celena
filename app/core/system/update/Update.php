<?php

namespace app\core\system\update;

use app\core\Base;
use app\core\System;

class Update{

    public function update(){


        Base::run("ALTER TABLE `".PREFIX."pages` ADD `tpl` VARCHAR(20) NOT NULL DEFAULT '' AFTER `url`");
        Base::run("ALTER TABLE `".PREFIX."categories` ADD `tpl_min` VARCHAR(20) NOT NULL DEFAULT '' AFTER `url`,
            ADD `tpl_max` VARCHAR(20) NOT NULL DEFAULT '' AFTER `tpl_min`");

        //System::removeDir(CORE . "/interface");

        //System::addSystemConfig(["quill_thumbs" => 1]);

        // обязательное изменение версии !
        System::editSystemConfig(["version" => '0.0.8']);
        return true;
    }
}