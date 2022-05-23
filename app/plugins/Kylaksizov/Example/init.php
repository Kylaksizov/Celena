<?php

namespace app\plugins\Kylaksizov\Example;

use app\core\interface\InitPlugin;

/**
 * @name Установка_обновление_удаление_плагина
 * ===========================================
 */
class init implements InitPlugin {


    public function install()
    {
        // install...
        return true;
    }


    public function update()
    {
        // update...
        return true;
    }


    public function delete()
    {
        // delete...
        return true;
    }

}