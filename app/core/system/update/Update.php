<?php

namespace app\core\system\update;

use app\core\System;

class Update{

    public function update(){




        // обязательное изменение версии !
        System::editSystemConfig(["version" => '0.0.2']);
        return true;
    }
}