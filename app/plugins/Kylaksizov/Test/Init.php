<?php

namespace app\plugins\Kylaksizov\Test;

use app\core\Base;
use app\core\interface\InitPlugin;
use PDOException;

/**
 * @name Установка_обновление_удаление_плагина
 * ===========================================
 */
class Init implements InitPlugin {


    // install...
    public function install()
    {
        Base::run("CREATE TABLE " . PREFIX . "new____table (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `plugin_id` int(11) NOT NULL,
            `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Название плагина или модуля',
            `config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Конфиг для плагина',
            `hashfile` varchar(75) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT '',
            `status` tinyint(1) DEFAULT 0,
            PRIMARY KEY (`id`),
            UNIQUE KEY `plugin_id` (`plugin_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

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
        Base::run("DROP TABLE IF EXISTS " . PREFIX . "new_table");

        return true;
    }

}