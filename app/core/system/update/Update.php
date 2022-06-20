<?php

namespace app\core\system\update;

use app\core\Base;
use app\core\System;

class Update{

    public function update(){


        //Base::run("TRUNCATE ".PREFIX."plugins");
        //System::removeDir(CORE . "/interface");
        System::addSystemConfig(["quill_thumbs" => 1]);

        // обязательное изменение версии !
        System::editSystemConfig(["version" => '0.0.7']);
        return true;
    }
}