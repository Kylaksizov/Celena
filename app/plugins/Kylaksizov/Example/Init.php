<?php

namespace app\plugins\Kylaksizov\Example;

use app\core\Base;
use core\system\plugins\InitPlugin;

/**
 * @name Установка_обновление_удаление_плагина
 * ===========================================
 */
class Init implements InitPlugin {


    // install...
    public function install()
    {

        return true;
    }


    // power on...
    public function powerOn()
    {
        return true;
    }


    // power off...
    public function powerOff()
    {
        return true;
    }


    // update...
    public function update()
    {
        return true;
    }


    // delete...
    public function delete()
    {
        return true;
    }

}