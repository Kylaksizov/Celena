<?php

namespace app\plugins\Celena\Example;

use app\core\Base;
use app\core\interface\InitPlugin;
use app\core\System;
use app\traits\Log;

/**
 * @name Установка_обновление_удаление_плагина
 * ===========================================
 */
class Init implements InitPlugin {


    // install...
    public function install()
    {

        // добавление таблиц
        Base::run("CREATE TABLE " . PREFIX . "example (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Комментарий...',
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        // добавление данных в таблицу
        Base::run("INSERT INTO " . PREFIX . "example (name) VALUES (?)", ['Это тестовая таблица, можно удалить...']);



        // добавление роутов
        $resultAdd = System::addRoute([
            'panel' => [
                'examplePlugin/$' => ['controller' => 'plugins\Celena\Example\Index'],
            ],
            'web' => [
                'example/$' => ['controller' => 'plugins\Celena\Example\Index'],
            ]
        ]);

        if(!$resultAdd){
            Log::add('Не удалось добавить роуты при установке плагина', 2);
            return 'Не удалось добавить роуты';
        }

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

        // удаление таблиц
        Base::run("DROP TABLE IF EXISTS " . PREFIX . "example");


        // удаление роутов
        $resultRemoved = System::removeRoute([
            'panel' => [
                'examplePlugin/$',
            ],
            'web' => [
                'example/$',
            ]
        ]);

        if(!$resultRemoved){
            Log::add('Не удалось удалить роуты при удалении плагина', 2);
            return 'Не удалось добавить роуты';
        }

        return true;
    }

}